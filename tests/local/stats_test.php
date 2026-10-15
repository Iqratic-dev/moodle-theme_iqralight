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
 * Tests of the data counter.
 *
 * @package    theme_iqralight
 * @category   test
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \theme_iqralight\local\stats
 */
final class stats_test extends \advanced_testcase {
    /**
     * Reports add up per day, are capped, and ignore negative amounts.
     */
    public function test_record(): void {
        global $DB;
        $this->resetAfterTest();
        set_config('statsanonymous', 0, 'theme_iqralight');
        $user = $this->getDataGenerator()->create_user();

        stats::record($user->id, 30 * 1048576, 18 * 1048576);
        stats::record($user->id, 0, 0);
        $this->assertSame(48 * 1048576, stats::learner_month_total($user->id));
        $this->assertSame(1, $DB->count_records('theme_iqralight_stats', ['userid' => $user->id]));

        stats::record($user->id, 999 * 1048576, -5);
        $this->assertSame(48 * 1048576 + stats::MAX_PER_REPORT, stats::learner_month_total($user->id));
    }

    /**
     * The learner sentence never rounds up, and shows money when the site sets a price.
     */
    public function test_learner_sentence(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        stats::record($user->id, 512 * 1024, 0);
        $this->assertNull(stats::learner_sentence($user->id), 'Under 1 MB, nothing is shown.');

        stats::record($user->id, 10 * 1048576, 0);
        $this->assertSame(get_string('saved', 'theme_iqralight', 10), stats::learner_sentence($user->id));

        set_config('mbprice', '2.5', 'theme_iqralight');
        set_config('mbcurrency', 'FC', 'theme_iqralight');
        $this->assertStringContainsString('FC', stats::learner_sentence($user->id));
    }

    /**
     * The anonymous mode keeps site totals only.
     */
    public function test_anonymous(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        set_config('statsanonymous', 1, 'theme_iqralight');
        $months = $DB->count_records('theme_iqralight_sitestats');

        stats::record($user->id, 5 * 1048576, 0);
        $this->assertSame(0, $DB->count_records('theme_iqralight_stats', ['userid' => $user->id]));
        $this->assertGreaterThanOrEqual(max(1, $months), $DB->count_records('theme_iqralight_sitestats'));
        $this->assertNull(stats::learner_sentence($user->id));
    }

    /**
     * Learner rows older than the retention period become anonymous monthly totals.
     */
    public function test_fold_old_rows(): void {
        global $DB;
        $this->resetAfterTest();
        $first = $this->getDataGenerator()->create_user();
        $second = $this->getDataGenerator()->create_user();
        $old = time() - 120 * DAYSECS;
        set_config('statsanonymous', 0, 'theme_iqralight');
        set_config('statsretention', 90, 'theme_iqralight');
        $DB->delete_records('theme_iqralight_stats');
        $DB->delete_records('theme_iqralight_sitestats');

        stats::record($first->id, 10 * 1048576, 0, $old);
        stats::record($second->id, 0, 4 * 1048576, $old);
        stats::record($first->id, 1048576, 0);

        $this->assertSame(2, stats::fold_old_rows());
        $this->assertSame(1, $DB->count_records('theme_iqralight_stats'));
        $month = $DB->get_record('theme_iqralight_sitestats', ['month' => stats::month_start($old)]);
        $this->assertEquals(10 * 1048576, $month->cachebytes);
        $this->assertEquals(4 * 1048576, $month->mediabytes);
        $this->assertEquals(2, $month->learners);

        $report = stats::site_report(12);
        $this->assertSame(1, $report[0]['learners']);
    }
}
