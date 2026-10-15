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
 * Course lightness report for teachers (RG-13.4): what weighs on learners in this course.
 *
 * Nothing is changed: the report lists the heaviest files and what the "tap to load"
 * filter makes wait, with advice to lighten the course.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

use theme_iqralight\local\audit;

$courseid = required_param('id', PARAM_INT);
$course = get_course($courseid);
require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/course:update', $context);

$PAGE->set_url(new moodle_url('/theme/iqralight/coursereport.php', ['id' => $course->id]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('coursereport', 'theme_iqralight'));
$PAGE->set_heading(format_string($course->fullname, true, ['context' => $context]));

$kb = fn(int $bytes): string => display_size($bytes);
$files = audit::heavy_files(30, $course->id);
$waiting = audit::dry_run($course->id);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('coursereport', 'theme_iqralight'));
echo html_writer::tag('p', get_string('coursereport_intro', 'theme_iqralight'));

echo $OUTPUT->heading(get_string('coursereport_files', 'theme_iqralight'), 3);
if (!$files) {
    echo html_writer::tag('p', get_string('audit_files_none', 'theme_iqralight'));
} else {
    $table = new html_table();
    $table->head = [get_string('audit_file', 'theme_iqralight'), get_string('audit_activity', 'theme_iqralight'),
        get_string('audit_filesize', 'theme_iqralight')];
    foreach ($files as $file) {
        $table->data[] = [s($file['filename']), s($file['activity']), $kb($file['bytes'])];
    }
    echo html_writer::table($table);
}

echo $OUTPUT->heading(get_string('coursereport_waiting', 'theme_iqralight'), 3);
if (!class_exists('\filter_iqralight\text_filter')) {
    echo html_writer::tag('p', get_string('audit_dryrun_nofilter', 'theme_iqralight'));
} else if (!$waiting) {
    echo html_writer::tag('p', get_string('audit_dryrun_nothing', 'theme_iqralight'));
} else {
    $table = new html_table();
    $table->head = [get_string('audit_activity', 'theme_iqralight'), get_string('audit_kind', 'theme_iqralight'),
        get_string('audit_filesize', 'theme_iqralight')];
    foreach ($waiting as $item) {
        $table->data[] = [s($item['activity']), get_string('audit_kind_' . $item['kind'], 'theme_iqralight'),
            $item['bytes'] === null ? get_string('unknownsize', 'filter_iqralight') : $kb($item['bytes'])];
    }
    echo html_writer::table($table);
}

echo $OUTPUT->heading(get_string('coursereport_advice', 'theme_iqralight'), 3);
echo html_writer::alist([
    get_string('coursereport_advice_pdf', 'theme_iqralight'),
    get_string('coursereport_advice_images', 'theme_iqralight'),
    get_string('coursereport_advice_video', 'theme_iqralight'),
    get_string('coursereport_advice_text', 'theme_iqralight'),
]);

echo html_writer::tag('p', get_string('credit', 'theme_iqralight', 'https://iqratic.com'), ['class' => 'iqra-credit text-muted']);
echo $OUTPUT->footer();
