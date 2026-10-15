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
 * Network data avoided: recording, totals and anonymous aggregation (F11).
 *
 * Two categories are always kept apart: media the learner never loaded, and
 * resources served by the service worker cache instead of the network. Figures
 * are never rounded up.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stats {
    /** @var int Highest amount accepted in one report, per category (50 MB). */
    public const MAX_PER_REPORT = 52428800;

    /** @var int Default number of days learner rows are kept. */
    public const DEFAULT_RETENTION = 90;

    /**
     * Whether the counter is enabled on the site.
     *
     * @return bool
     */
    public static function enabled(): bool {
        return get_config('theme_iqralight', 'stats') !== '0';
    }

    /**
     * Whether only anonymous site totals are kept.
     *
     * @return bool
     */
    public static function anonymous(): bool {
        return (bool) get_config('theme_iqralight', 'statsanonymous');
    }

    /**
     * Records a report sent by a learner's browser.
     *
     * @param int $userid
     * @param int $cachebytes
     * @param int $mediabytes
     * @param int|null $now
     */
    public static function record(int $userid, int $cachebytes, int $mediabytes, ?int $now = null): void {
        global $DB;
        $cachebytes = max(0, min($cachebytes, self::MAX_PER_REPORT));
        $mediabytes = max(0, min($mediabytes, self::MAX_PER_REPORT));
        if (!self::enabled() || ($cachebytes + $mediabytes) === 0) {
            return;
        }
        $now = $now ?? time();

        if (self::anonymous()) {
            self::add_to_month(self::month_start($now), $cachebytes, $mediabytes, 0, $now);
            return;
        }

        $day = usergetmidnight($now, 99);
        $row = $DB->get_record('theme_iqralight_stats', ['userid' => $userid, 'day' => $day]);
        if ($row) {
            $row->cachebytes += $cachebytes;
            $row->mediabytes += $mediabytes;
            $row->timemodified = $now;
            $DB->update_record('theme_iqralight_stats', $row);
        } else {
            $DB->insert_record('theme_iqralight_stats', (object) [
                'userid' => $userid,
                'day' => $day,
                'cachebytes' => $cachebytes,
                'mediabytes' => $mediabytes,
                'timemodified' => $now,
            ]);
        }
    }

    /**
     * Total avoided by a learner since the start of the current month, in bytes.
     *
     * @param int $userid
     * @param int|null $now
     * @return int
     */
    public static function learner_month_total(int $userid, ?int $now = null): int {
        global $DB;
        $total = $DB->get_field_sql(
            'SELECT SUM(cachebytes + mediabytes) FROM {theme_iqralight_stats} WHERE userid = ? AND day >= ?',
            [$userid, self::month_start($now ?? time())]
        );
        return (int) $total;
    }

    /**
     * The sentence shown to a learner, or null when there is nothing to show.
     *
     * @param int $userid
     * @return string|null
     */
    public static function learner_sentence(int $userid): ?string {
        if (!self::enabled() || self::anonymous()) {
            return null;
        }
        $bytes = self::learner_month_total($userid);
        if ($bytes < 1048576) {
            return null;
        }
        $megabytes = (int) floor($bytes / 1048576);
        $sentence = wording::get('saved', $megabytes);
        $price = (float) get_config('theme_iqralight', 'mbprice');
        $currency = trim((string) get_config('theme_iqralight', 'mbcurrency'));
        if ($price > 0 && $currency !== '') {
            $sentence .= ' ' . get_string('savedmoney', 'theme_iqralight', (object) [
                'amount' => format_float(floor($megabytes * $price * 100) / 100, 2, true, true),
                'currency' => $currency,
            ]);
        }
        return $sentence;
    }

    /**
     * Monthly totals of the site, newest first.
     *
     * @param int $months Number of months.
     * @param int|null $now
     * @return array[] Each with month (timestamp), cachebytes, mediabytes, learners.
     */
    public static function site_report(int $months = 12, ?int $now = null): array {
        global $DB;
        $now = $now ?? time();
        $starts = [];
        $start = self::month_start($now);
        for ($i = 0; $i < $months; $i++) {
            $starts[] = $start;
            $start = self::month_start($start - 1);
        }
        $report = [];
        foreach ($starts as $i => $start) {
            $end = $i === 0 ? $now + 1 : $starts[$i - 1];
            $folded = $DB->get_record('theme_iqralight_sitestats', ['month' => $start]);
            $live = $DB->get_record_sql(
                'SELECT SUM(cachebytes) AS cachebytes, SUM(mediabytes) AS mediabytes, COUNT(DISTINCT userid) AS learners
                   FROM {theme_iqralight_stats} WHERE day >= ? AND day < ?',
                [$start, $end]
            );
            $report[] = [
                'month' => $start,
                'cachebytes' => (int) ($folded->cachebytes ?? 0) + (int) $live->cachebytes,
                'mediabytes' => (int) ($folded->mediabytes ?? 0) + (int) $live->mediabytes,
                'learners' => (int) ($folded->learners ?? 0) + (int) $live->learners,
            ];
        }
        return $report;
    }

    /**
     * Folds learner rows older than the retention period into anonymous monthly totals.
     *
     * @param int|null $now
     * @return int Number of learner rows removed.
     */
    public static function fold_old_rows(?int $now = null): int {
        global $DB;
        $now = $now ?? time();
        $days = (int) (get_config('theme_iqralight', 'statsretention') ?: self::DEFAULT_RETENTION);
        $limit = usergetmidnight($now - $days * DAYSECS, 99);
        $rows = $DB->get_records_select('theme_iqralight_stats', 'day < ?', [$limit], 'day ASC');
        if (!$rows) {
            return 0;
        }
        $months = [];
        foreach ($rows as $row) {
            $month = self::month_start($row->day);
            $months[$month] = $months[$month] ?? ['cache' => 0, 'media' => 0, 'users' => []];
            $months[$month]['cache'] += (int) $row->cachebytes;
            $months[$month]['media'] += (int) $row->mediabytes;
            $months[$month]['users'][$row->userid] = true;
        }
        $transaction = $DB->start_delegated_transaction();
        foreach ($months as $month => $totals) {
            self::add_to_month($month, $totals['cache'], $totals['media'], count($totals['users']), $now);
        }
        $DB->delete_records_select('theme_iqralight_stats', 'day < ?', [$limit]);
        $transaction->allow_commit();
        return count($rows);
    }

    /**
     * Adds amounts to the anonymous total of a month.
     *
     * @param int $month
     * @param int $cachebytes
     * @param int $mediabytes
     * @param int $learners
     * @param int $now
     */
    protected static function add_to_month(int $month, int $cachebytes, int $mediabytes, int $learners, int $now): void {
        global $DB;
        $row = $DB->get_record('theme_iqralight_sitestats', ['month' => $month]);
        if ($row) {
            $row->cachebytes += $cachebytes;
            $row->mediabytes += $mediabytes;
            $row->learners += $learners;
            $row->timemodified = $now;
            $DB->update_record('theme_iqralight_sitestats', $row);
        } else {
            $DB->insert_record('theme_iqralight_sitestats', (object) [
                'month' => $month,
                'cachebytes' => $cachebytes,
                'mediabytes' => $mediabytes,
                'learners' => $learners,
                'timemodified' => $now,
            ]);
        }
    }

    /**
     * Timestamp of the start of the month containing a time, in the server time zone.
     *
     * @param int $time
     * @return int
     */
    public static function month_start(int $time): int {
        $date = usergetdate($time, 99);
        return make_timestamp($date['year'], $date['mon'], 1, 0, 0, 0, 99);
    }
}
