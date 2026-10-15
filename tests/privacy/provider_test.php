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

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core\context\system as context_system;
use theme_iqralight\local\mode;
use theme_iqralight\local\stats;

/**
 * Tests of the privacy provider.
 *
 * @package    theme_iqralight
 * @category   test
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \theme_iqralight\privacy\provider
 */
final class provider_test extends \core_privacy\tests\provider_testcase {
    /**
     * Two learners with counter rows and preferences.
     *
     * @return array
     */
    protected function create_learners(): array {
        $first = $this->getDataGenerator()->create_user();
        $second = $this->getDataGenerator()->create_user();
        stats::record($first->id, 2 * 1048576, 1048576);
        stats::record($second->id, 1048576, 0);
        set_user_preference(mode::PREF_MODE, 'economy', $first);
        set_user_preference(mode::PREF_EASYREAD, 1, $first);
        return [$first, $second];
    }

    /**
     * The counter rows are found in the system context.
     */
    public function test_contexts_and_users(): void {
        $this->resetAfterTest();
        [$first] = $this->create_learners();
        $this->assertSame([context_system::instance()->id], provider::get_contexts_for_userid($first->id)->get_contextids());

        $userlist = new userlist(context_system::instance(), 'theme_iqralight');
        provider::get_users_in_context($userlist);
        $this->assertCount(2, $userlist->get_userids());
    }

    /**
     * A learner's counter rows and preferences are exported.
     */
    public function test_export(): void {
        $this->resetAfterTest();
        [$first] = $this->create_learners();
        $context = context_system::instance();

        provider::export_user_data(new approved_contextlist($first, 'theme_iqralight', [$context->id]));
        $data = writer::with_context($context)->get_data([get_string('pluginname', 'theme_iqralight')]);
        $this->assertCount(1, $data->dataavoided);
        $this->assertSame(2 * 1048576, $data->dataavoided[0]['cachebytes']);

        provider::export_user_preferences($first->id);
        $preferences = writer::with_context($context)->get_user_preferences('theme_iqralight');
        $this->assertSame('economy', $preferences->{mode::PREF_MODE}->value);
        $this->assertEquals(1, $preferences->{mode::PREF_EASYREAD}->value);
    }

    /**
     * Deletion removes one learner's rows, several learners' rows, or all of them.
     */
    public function test_delete(): void {
        global $DB;
        $this->resetAfterTest();
        [$first, $second] = $this->create_learners();
        $context = context_system::instance();

        provider::delete_data_for_user(new approved_contextlist($first, 'theme_iqralight', [$context->id]));
        $this->assertSame(0, $DB->count_records('theme_iqralight_stats', ['userid' => $first->id]));
        $this->assertSame(1, $DB->count_records('theme_iqralight_stats', ['userid' => $second->id]));

        provider::delete_data_for_users(new approved_userlist($context, 'theme_iqralight', [$second->id]));
        $this->assertSame(0, $DB->count_records('theme_iqralight_stats'));

        $this->create_learners();
        provider::delete_data_for_all_users_in_context($context);
        $this->assertSame(0, $DB->count_records('theme_iqralight_stats'));
    }
}
