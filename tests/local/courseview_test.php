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
 * Tests of the action verbs of the course page.
 *
 * @package    theme_iqralight
 * @category   test
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \theme_iqralight\local\courseview
 */
final class courseview_test extends \advanced_testcase {
    /**
     * Known activity types have their own verb; others are "Open".
     */
    public function test_verbs(): void {
        $this->assertSame(get_string('verb_page', 'theme_iqralight'), courseview::verb('page', 0));
        $this->assertSame(get_string('verb_quiz', 'theme_iqralight'), courseview::verb('quiz', 0));
        $this->assertSame(get_string('verb_default', 'theme_iqralight'), courseview::verb('nosuchmodule', 0));
    }

    /**
     * Files show their size; videos are watched rather than downloaded.
     */
    public function test_file_verbs(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $resource = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $context = \core\context\module::instance($resource->cmid);
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_resource', 'content');
        $fs->create_file_from_string(['contextid' => $context->id, 'component' => 'mod_resource', 'filearea' => 'content',
            'itemid' => 0, 'filepath' => '/', 'filename' => 'notes.pdf', 'sortorder' => 1], str_repeat('x', 2048));
        $expected = get_string('verb_resource', 'theme_iqralight', display_size(2048));
        $this->assertSame($expected, courseview::verb('resource', $resource->cmid));

        $video = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $context = \core\context\module::instance($video->cmid);
        $fs->delete_area_files($context->id, 'mod_resource', 'content');
        $fs->create_file_from_string(['contextid' => $context->id, 'component' => 'mod_resource', 'filearea' => 'content',
            'itemid' => 0, 'filepath' => '/', 'filename' => 'clip.mp4', 'sortorder' => 1], str_repeat('x', 4096));
        $expected = get_string('verb_watch', 'theme_iqralight', display_size(4096));
        $this->assertSame($expected, courseview::verb('resource', $video->cmid));
    }
}
