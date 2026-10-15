<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace theme_iqralight\local;

/**
 * Tests of the connection mode resolution.
 *
 * @package    theme_iqralight
 * @category   test
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \theme_iqralight\local\mode
 */
final class mode_test extends \advanced_testcase {
    /**
     * Clears the request data the mode reads.
     */
    protected function tearDown(): void {
        unset($_COOKIE[mode::COOKIE_NETWORK], $_SERVER['HTTP_SAVE_DATA']);
        parent::tearDown();
    }

    /**
     * Without any information, the light mode applies (EF-04); the network measure and
     * the Save-Data header are then taken into account.
     */
    public function test_effective_mode(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());
        set_config('defaultmode', 'auto', 'theme_iqralight');
        set_config('savedata', 1, 'theme_iqralight');

        $this->assertSame('auto', mode::requested());
        $this->assertSame('economy', mode::effective());

        $_COOKIE[mode::COOKIE_NETWORK] = 'fast';
        $this->assertSame('full', mode::effective());

        $_SERVER['HTTP_SAVE_DATA'] = 'on';
        $this->assertSame('economy', mode::effective());

        set_config('savedata', 0, 'theme_iqralight');
        $this->assertSame('full', mode::effective());
    }

    /**
     * The site default applies until the learner chooses, and the learner's choice
     * comes before a category profile.
     */
    public function test_learner_choice_comes_first(): void {
        global $PAGE;
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        set_config('defaultmode', 'full', 'theme_iqralight');
        $this->assertSame('full', mode::requested());

        profiles::save([$category->id => 'light']);
        $PAGE->set_course($course);
        $this->assertSame('economy', mode::requested());

        set_user_preference(mode::PREF_MODE, 'full');
        $this->assertSame('full', mode::requested());
    }

    /**
     * Text only: the learner's explicit choice, the "lighter" profile, or a very slow network.
     */
    public function test_text_only(): void {
        global $PAGE;
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $this->setUser($this->getDataGenerator()->create_user());
        set_config('defaultmode', 'auto', 'theme_iqralight');
        set_config('autotextonly', 1, 'theme_iqralight');

        $this->assertFalse(mode::text_only());
        $_COOKIE[mode::COOKIE_NETWORK] = 'veryslow';
        $this->assertTrue(mode::text_only());
        unset($_COOKIE[mode::COOKIE_NETWORK]);

        profiles::save([$category->id => 'lighter']);
        $PAGE->set_course($course);
        $this->assertTrue(mode::text_only());

        set_user_preference(mode::PREF_TEXTONLY, 0);
        $this->assertFalse(mode::text_only());

        set_user_preference(mode::PREF_TEXTONLY, 1);
        set_user_preference(mode::PREF_MODE, 'full');
        $this->assertFalse(mode::text_only(), 'Text only never applies in "load everything" mode.');
    }

    /**
     * The dark mode only applies to the page types checked in dark.
     */
    public function test_dark_class(): void {
        $this->resetAfterTest();
        $this->setUser($this->getDataGenerator()->create_user());

        $page = new \moodle_page();
        $page->set_pagetype('my-index');
        $this->assertSame('iqra-dark-auto', mode::dark_class($page));

        $page = new \moodle_page();
        $page->set_pagetype('mod-lesson-view');
        $this->assertNull(mode::dark_class($page));

        set_config('darkmode', 'on', 'theme_iqralight');
        $page = new \moodle_page();
        $page->set_pagetype('course-view-topics');
        $this->assertSame('iqra-dark-on', mode::dark_class($page));

        set_config('darkmode', 'off', 'theme_iqralight');
        $this->assertNull(mode::dark_class($page));
    }
}
