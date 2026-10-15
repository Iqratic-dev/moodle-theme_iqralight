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

use moodle_page;
use moodle_url;

/**
 * Builds the learner's bottom navigation bar: My courses, Messages, My account.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation {
    /** @var string[] Layouts where the bar never appears. */
    public const EXCLUDED_LAYOUTS = ['embedded', 'popup', 'print', 'maintenance', 'secure', 'redirect', 'login', 'frametop'];

    /**
     * Whether the bar should be shown on this page.
     *
     * Teachers in editing mode keep the full Boost interface (UX-10).
     *
     * @param moodle_page $page
     * @return bool
     */
    public static function is_visible(moodle_page $page): bool {
        if (!isloggedin() || isguestuser()) {
            return false;
        }
        if (in_array($page->pagelayout, self::EXCLUDED_LAYOUTS, true)) {
            return false;
        }
        if (strpos($page->pagetype, 'mod-quiz-attempt') === 0) {
            return false;
        }
        return !$page->user_is_editing();
    }

    /**
     * Template data for the bar.
     *
     * @param moodle_page $page
     * @return array
     */
    public static function export_for_template(moodle_page $page): array {
        global $CFG, $USER;

        $items = [];
        $items[] = [
            'key' => 'courses',
            'label' => get_string('nav_courses', 'theme_iqralight'),
            'url' => (new moodle_url('/my/courses.php'))->out(false),
            'active' => in_array($page->pagelayout, ['mycourses', 'mydashboard', 'course', 'incourse'], true),
        ];
        if (!empty($CFG->messaging)) {
            $unread = \core_message\api::count_unread_conversations($USER);
            $items[] = [
                'key' => 'messages',
                'label' => get_string('nav_messages', 'theme_iqralight'),
                'url' => (new moodle_url('/message/index.php'))->out(false),
                'active' => strpos($page->pagetype, 'message-') === 0,
                'badge' => $unread > 0 ? ($unread > 9 ? '9+' : (string) $unread) : '',
                'badgelabel' => $unread > 0 ? get_string('unreadmessages', 'theme_iqralight', $unread) : '',
            ];
        }
        $items[] = [
            'key' => 'account',
            'label' => get_string('nav_account', 'theme_iqralight'),
            'url' => (new moodle_url('/user/profile.php'))->out(false),
            'active' => strpos($page->pagetype, 'user-') === 0,
        ];
        foreach ($items as &$item) {
            $item['is' . $item['key']] = true;
        }
        return ['items' => $items];
    }
}
