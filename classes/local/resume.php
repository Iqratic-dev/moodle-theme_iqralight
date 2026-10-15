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

use completion_info;
use core\context\course as context_course;
use moodle_url;

/**
 * Builds the "Resume" card: the course the learner used last and where to continue.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resume {
    /**
     * Data for the resume card, or null when there is nothing to resume.
     *
     * @param int $userid
     * @return array|null
     */
    public static function for_user(int $userid): ?array {
        global $DB, $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->dirroot . '/course/lib.php');

        $sql = "SELECT ul.courseid, ul.timeaccess
                  FROM {user_lastaccess} ul
                  JOIN {course} c ON c.id = ul.courseid
                 WHERE ul.userid = :userid AND c.id <> :siteid
              ORDER BY ul.timeaccess DESC";
        $recent = $DB->get_records_sql($sql, ['userid' => $userid, 'siteid' => SITEID], 0, 5);

        foreach (array_keys($recent) as $courseid) {
            $course = get_course($courseid);
            $context = context_course::instance($course->id);
            if (!is_enrolled($context, $userid, '', true)) {
                continue;
            }
            if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', $context, $userid)) {
                continue;
            }
            return self::export_course($course, $context, $userid);
        }
        return null;
    }

    /**
     * Exports one course for the card.
     *
     * @param \stdClass $course
     * @param context_course $context
     * @param int $userid
     * @return array
     */
    protected static function export_course(\stdClass $course, context_course $context, int $userid): array {
        $modinfo = get_fast_modinfo($course, $userid);
        $target = self::last_activity($course->id, $userid, $modinfo);

        $data = [
            'coursename' => format_string($course->fullname, true, ['context' => $context]),
            'url' => $target ? $target->url->out(false) : (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
            'sectionname' => $target ? get_section_name($course, $target->sectionnum) : '',
            'activityname' => $target ? format_string($target->name, true, ['context' => $target->context]) : '',
            'hasprogress' => false,
            'ctalabel' => get_string('continue', 'theme_iqralight'),
            'kicker' => wording::get('resume'),
        ];

        $completion = new completion_info($course);
        if ($completion->is_enabled()) {
            $total = 0;
            $done = 0;
            foreach ($completion->get_activities() as $cm) {
                if (!$cm->uservisible) {
                    continue;
                }
                $total++;
                $state = (int) $completion->get_data($cm, true, $userid)->completionstate;
                if (in_array($state, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS], true)) {
                    $done++;
                }
            }
            if ($total > 0) {
                $label = $done === 0 ? 'start' : ($done === $total ? 'review' : 'continue');
                $data['ctalabel'] = get_string($label, 'theme_iqralight');
                $data['hasprogress'] = true;
                $data['done'] = $done;
                $data['total'] = $total;
                $data['percent'] = (int) round(100 * $done / $total);
            }
        }
        return $data;
    }

    /**
     * The last activity the learner opened in this course, if still visible to them.
     *
     * Uses the data Moodle already records for the "Recently accessed items" block.
     *
     * @param int $courseid
     * @param int $userid
     * @param \course_modinfo $modinfo
     * @return \cm_info|null
     */
    protected static function last_activity(int $courseid, int $userid, \course_modinfo $modinfo): ?\cm_info {
        global $DB;
        if (!$DB->get_manager()->table_exists('block_recentlyaccesseditems')) {
            return null;
        }
        $records = $DB->get_records(
            'block_recentlyaccesseditems',
            ['userid' => $userid, 'courseid' => $courseid],
            'timeaccess DESC',
            'id, cmid',
            0,
            5
        );
        foreach ($records as $record) {
            $cms = $modinfo->get_cms();
            if (isset($cms[$record->cmid]) && $cms[$record->cmid]->uservisible && $cms[$record->cmid]->url) {
                return $cms[$record->cmid];
            }
        }
        return null;
    }
}
