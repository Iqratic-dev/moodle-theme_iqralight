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
 * The learner's courses shown on the home page: the three last visited, with progress.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mycourses {
    /** @var string[] Cover tones, in turn, so that neighbouring courses look different. */
    protected const TONES = ['brand', 'blue', 'amber'];

    /**
     * Courses for the home page: the last visited first, then other enrolled courses.
     *
     * @param int $userid
     * @param int $limit
     * @return array[] Each with name, url, category, tone, hasprogress and percent.
     */
    public static function for_user(int $userid, int $limit = 3): array {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        $ids = array_keys(course_get_recent_courses($userid, $limit));
        if (count($ids) < $limit) {
            foreach (enrol_get_all_users_courses($userid, true, null, 'visible DESC, sortorder ASC') as $course) {
                if (count($ids) >= $limit) {
                    break;
                }
                if (!in_array($course->id, $ids)) {
                    $ids[] = $course->id;
                }
            }
        }
        $rows = [];
        foreach (array_values($ids) as $index => $id) {
            $course = get_course($id);
            $context = \core\context\course::instance($course->id);
            $category = \core_course_category::get($course->category, IGNORE_MISSING, true);
            $percent = \core_completion\progress::get_course_progress_percentage($course, $userid);
            $rows[] = [
                'name' => format_string($course->fullname, true, ['context' => $context]),
                'url' => (new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                'category' => $category ? $category->get_formatted_name() : '',
                'tone' => self::TONES[$index % count(self::TONES)],
                'hasprogress' => $percent !== null,
                'percent' => $percent === null ? 0 : (int) floor($percent),
            ];
        }
        return $rows;
    }
}
