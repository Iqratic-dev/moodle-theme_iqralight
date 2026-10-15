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

namespace theme_iqralight\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core\context\system as context_system;
use theme_iqralight\local\mode;

/**
 * Privacy provider for theme_iqralight.
 *
 * The theme stores two user preferences and, unless the counter is anonymous,
 * the network data each learner avoided per day, kept for a limited time.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\user_preference_provider {
    /**
     * Describes the stored data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference(mode::PREF_MODE, 'privacy:metadata:preference:mode');
        $collection->add_user_preference(mode::PREF_TEXTONLY, 'privacy:metadata:preference:textonly');
        $collection->add_user_preference(mode::PREF_CONTRAST, 'privacy:metadata:preference:contrast');
        $collection->add_user_preference(mode::PREF_EASYREAD, 'privacy:metadata:preference:easyread');
        $collection->add_database_table('theme_iqralight_stats', [
            'userid' => 'privacy:metadata:stats:userid',
            'day' => 'privacy:metadata:stats:day',
            'cachebytes' => 'privacy:metadata:stats:cachebytes',
            'mediabytes' => 'privacy:metadata:stats:mediabytes',
        ], 'privacy:metadata:stats');
        return $collection;
    }

    /**
     * Contexts holding data for a user: the system context, when the user has counter rows.
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;
        $contextlist = new contextlist();
        if ($DB->record_exists('theme_iqralight_stats', ['userid' => $userid])) {
            $contextlist->add_system_context();
        }
        return $contextlist;
    }

    /**
     * Users with data in a context.
     *
     * @param userlist $userlist
     */
    public static function get_users_in_context(userlist $userlist) {
        if ($userlist->get_context() instanceof context_system) {
            $userlist->add_from_sql('userid', 'SELECT userid FROM {theme_iqralight_stats}', []);
        }
    }

    /**
     * Exports a user's counter rows.
     *
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof context_system) {
                continue;
            }
            $rows = $DB->get_records('theme_iqralight_stats', ['userid' => $contextlist->get_user()->id], 'day ASC');
            if ($rows) {
                $data = array_map(fn($row) => [
                    'day' => \core_privacy\local\request\transform::date($row->day),
                    'cachebytes' => (int) $row->cachebytes,
                    'mediabytes' => (int) $row->mediabytes,
                ], array_values($rows));
                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'theme_iqralight')],
                    (object) ['dataavoided' => $data]
                );
            }
        }
    }

    /**
     * Deletes every learner row of a context.
     *
     * @param \context $context
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context instanceof context_system) {
            $DB->delete_records('theme_iqralight_stats');
        }
    }

    /**
     * Deletes a user's rows.
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context instanceof context_system) {
                $DB->delete_records('theme_iqralight_stats', ['userid' => $contextlist->get_user()->id]);
            }
        }
    }

    /**
     * Deletes the rows of several users.
     *
     * @param approved_userlist $userlist
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        if (!$userlist->get_context() instanceof context_system || !$userlist->get_userids()) {
            return;
        }
        [$sql, $params] = $DB->get_in_or_equal($userlist->get_userids());
        $DB->delete_records_select('theme_iqralight_stats', "userid {$sql}", $params);
    }

    /**
     * Exports the learner's preferences.
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid) {
        $value = get_user_preferences(mode::PREF_MODE, null, $userid);
        if ($value !== null) {
            writer::export_user_preference(
                'theme_iqralight',
                mode::PREF_MODE,
                $value,
                get_string('privacy:request:preference:mode', 'theme_iqralight', get_string('mode_' . $value, 'theme_iqralight'))
            );
        }
        $textonly = get_user_preferences(mode::PREF_TEXTONLY, null, $userid);
        if ($textonly !== null) {
            writer::export_user_preference(
                'theme_iqralight',
                mode::PREF_TEXTONLY,
                $textonly,
                get_string('privacy:request:preference:textonly', 'theme_iqralight', get_string($textonly ? 'yes' : 'no'))
            );
        }
        foreach ([mode::PREF_CONTRAST => 'contrast', mode::PREF_EASYREAD => 'easyread'] as $preference => $key) {
            $value = get_user_preferences($preference, null, $userid);
            if ($value !== null) {
                writer::export_user_preference(
                    'theme_iqralight',
                    $preference,
                    $value,
                    get_string('privacy:request:preference:' . $key, 'theme_iqralight', get_string($value ? 'yes' : 'no'))
                );
            }
        }
    }
}
