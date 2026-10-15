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
 * Tests of the category profiles.
 *
 * @package    theme_iqralight
 * @category   test
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \theme_iqralight\local\profiles
 */
final class profiles_test extends \advanced_testcase {
    /**
     * A profile applies to its category and its subcategories; the nearest one wins.
     */
    public function test_inheritance(): void {
        $this->resetAfterTest();
        $parent = $this->getDataGenerator()->create_category();
        $child = $this->getDataGenerator()->create_category(['parent' => $parent->id]);
        $other = $this->getDataGenerator()->create_category();

        profiles::save([$parent->id => 'light']);
        $this->assertSame('light', profiles::for_category($parent->id));
        $this->assertSame('light', profiles::for_category($child->id));
        $this->assertSame('site', profiles::for_category($other->id));

        profiles::save([$parent->id => 'light', $child->id => 'lighter']);
        $this->assertSame('lighter', profiles::for_category($child->id));
        $this->assertSame('light', profiles::for_category($parent->id));
    }

    /**
     * Unknown levels and categories, and the "site" level, are not stored.
     */
    public function test_save_keeps_valid_values_only(): void {
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();
        profiles::save([$category->id => 'turbo', 999999 => 'light', 0 => 'lighter']);
        $this->assertSame([], profiles::all());

        profiles::save([$category->id => 'site']);
        $this->assertSame([], profiles::all());
        $this->assertSame('', get_config('theme_iqralight', 'categoryprofiles'));
    }

    /**
     * The profile of the current page comes from its course.
     */
    public function test_current(): void {
        global $PAGE;
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        profiles::save([$category->id => 'lighter']);

        $this->assertSame('site', profiles::current());
        $PAGE->set_course($course);
        $this->assertSame('lighter', profiles::current());
    }
}
