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

/**
 * Data counter report for administrators: network data avoided per month.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use theme_iqralight\local\stats;

admin_externalpage_setup('theme_iqralight_stats');

$download = optional_param('download', '', PARAM_ALPHA);
$report = stats::site_report(12);
$price = (float) get_config('theme_iqralight', 'mbprice');
$currency = trim((string) get_config('theme_iqralight', 'mbcurrency'));
$withmoney = $price > 0 && $currency !== '';

// Bytes as megabytes, rounded down: figures are never rounded up.
$mb = fn(int $bytes): string => format_float(floor($bytes / 10485.76) / 100, 2, true, true);

$columns = [
    'month' => get_string('report_month', 'theme_iqralight'),
    'media' => get_string('report_media', 'theme_iqralight'),
    'cache' => get_string('report_cache', 'theme_iqralight'),
    'total' => get_string('report_total', 'theme_iqralight'),
    'learners' => get_string('report_learners', 'theme_iqralight'),
];
if ($withmoney) {
    $columns['money'] = get_string('report_money', 'theme_iqralight', $currency);
}
$rows = [];
foreach ($report as $line) {
    $total = $line['cachebytes'] + $line['mediabytes'];
    $row = [
        'month' => userdate($line['month'], get_string('strftimemonthyear', 'langconfig')),
        'media' => $mb($line['mediabytes']),
        'cache' => $mb($line['cachebytes']),
        'total' => $mb($total),
        'learners' => $line['learners'],
    ];
    if ($withmoney) {
        $row['money'] = format_float(floor($total / 1048576 * $price * 100) / 100, 2, true, true);
    }
    $rows[] = $row;
}

if ($download !== '') {
    \core\dataformat::download_data('iqra-data-avoided', $download, $columns, $rows);
    die;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('report_title', 'theme_iqralight'));
echo html_writer::tag('p', get_string('report_intro', 'theme_iqralight'));
if (stats::anonymous()) {
    echo $OUTPUT->notification(get_string('report_anonymous', 'theme_iqralight'), 'info');
}
$table = new html_table();
$table->head = array_values($columns);
$table->data = array_map('array_values', $rows);
echo html_writer::table($table);
echo $OUTPUT->download_dataformat_selector(get_string('report_download', 'theme_iqralight'), $PAGE->url, 'download');
echo html_writer::tag('p', get_string('credit', 'theme_iqralight', 'https://iqratic.com'), ['class' => 'iqra-credit text-muted']);
echo $OUTPUT->footer();
