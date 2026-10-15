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
 * Lightness audit for administrators (RG-12.7 and RG-12.10).
 *
 * Measures what weighs on learners' connections on this site. Nothing is changed.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use theme_iqralight\local\audit;

admin_externalpage_setup('theme_iqralight_audit');
core_php_time_limit::raise(120);

$courseid = optional_param('dryrun', 0, PARAM_INT);
$baseurl = new moodle_url('/theme/iqralight/audit.php');
$css = theme_config::load('iqralight')->get_css_cached_content() ?: theme_config::load('iqralight')->get_css_content();
$kb = fn(int $bytes): string => display_size($bytes);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('audit', 'theme_iqralight'));
echo html_writer::tag('p', get_string('audit_intro', 'theme_iqralight'));

// 1. First visit.
echo $OUTPUT->heading(get_string('audit_firstvisit', 'theme_iqralight'), 3);
$table = new html_table();
$table->head = [get_string('audit_part', 'theme_iqralight'), get_string('audit_size', 'theme_iqralight')];
$total = 0;
foreach (audit::first_visit($css) as $part) {
    $table->data[] = [get_string('audit_part_' . $part['part'], 'theme_iqralight'), $kb($part['bytes'])];
    $total += $part['bytes'];
}
$table->data[] = [
    html_writer::tag('strong', get_string('audit_total', 'theme_iqralight')),
    html_writer::tag('strong', $kb($total)),
];
echo html_writer::table($table);
echo html_writer::tag('p', get_string('audit_firstvisit_note', 'theme_iqralight'), ['class' => 'text-muted']);

// 2. Settings checklist.
echo $OUTPUT->heading(get_string('audit_checks', 'theme_iqralight'), 3);
$items = [];
foreach (audit::checks() as $check) {
    $icon = $check['ok'] ? $OUTPUT->pix_icon('i/valid', get_string('yes')) : $OUTPUT->pix_icon('i/invalid', get_string('no'));
    $text = get_string('audit_check_' . $check['key'], 'theme_iqralight');
    if (!$check['ok']) {
        $text .= ' ' . html_writer::span(get_string('audit_check_' . $check['key'] . '_fix', 'theme_iqralight'), 'text-muted');
    }
    $items[] = $icon . ' ' . $text;
}
echo html_writer::alist($items, ['class' => 'list-unstyled']);

// 3. JavaScript weight by plugin.
[$rows, $raw, $compressed] = audit::javascript();
echo $OUTPUT->heading(get_string('audit_javascript', 'theme_iqralight'), 3);
echo html_writer::tag('p', get_string('audit_javascript_intro', 'theme_iqralight', (object) [
    'count' => count($rows), 'raw' => $kb($raw), 'compressed' => $kb($compressed),
]));
$table = new html_table();
$table->head = [get_string('audit_component', 'theme_iqralight'), get_string('audit_type', 'theme_iqralight'),
    get_string('audit_size', 'theme_iqralight')];
foreach (array_slice($rows, 0, 25) as $row) {
    $table->data[] = [s($row['component']),
        get_string($row['standard'] ? 'audit_standard' : 'audit_thirdparty', 'theme_iqralight'), $kb($row['compressed'])];
}
echo html_writer::table($table);
echo html_writer::tag('p', get_string(
    'audit_javascript_advice',
    'theme_iqralight',
    (new moodle_url('/admin/plugins.php'))->out()
), ['class' => 'text-muted']);

// 3b. Blocks shown to learners.
echo $OUTPUT->heading(get_string('audit_blocks', 'theme_iqralight'), 3);
$blocks = audit::blocks($rows);
if (!$blocks) {
    echo html_writer::tag('p', get_string('audit_blocks_none', 'theme_iqralight'));
} else {
    echo html_writer::tag('p', get_string('audit_blocks_intro', 'theme_iqralight'));
    $table = new html_table();
    $table->head = [get_string('audit_block', 'theme_iqralight'), get_string('audit_instances', 'theme_iqralight'),
        get_string('audit_size', 'theme_iqralight')];
    foreach ($blocks as $block) {
        $table->data[] = [s($block['name']), $block['instances'], $block['script'] ? $kb($block['script']) : '-'];
    }
    echo html_writer::table($table);
}

// 4. Icons outside the light fonts.
$missing = audit::uncovered_icons($css);
echo $OUTPUT->heading(get_string('audit_icons', 'theme_iqralight'), 3);
if (!$missing) {
    echo $OUTPUT->notification(get_string('audit_icons_none', 'theme_iqralight'), 'success', false);
} else {
    echo html_writer::tag('p', get_string('audit_icons_some', 'theme_iqralight', count($missing)));
    echo html_writer::alist(array_map('s', array_slice($missing, 0, 30)));
}

// 5. Heaviest course files.
echo $OUTPUT->heading(get_string('audit_files', 'theme_iqralight'), 3);
$files = audit::heavy_files();
if (!$files) {
    echo html_writer::tag('p', get_string('audit_files_none', 'theme_iqralight'));
} else {
    $table = new html_table();
    $table->head = [get_string('audit_file', 'theme_iqralight'), get_string('audit_course', 'theme_iqralight'),
        get_string('audit_filesize', 'theme_iqralight')];
    foreach ($files as $file) {
        $table->data[] = [s($file['filename']),
            html_writer::link(new moodle_url('/course/view.php', ['id' => $file['courseid']]), $file['coursename']),
            $kb($file['bytes'])];
    }
    echo html_writer::table($table);
}

// 6. Dry run of the filter.
echo $OUTPUT->heading(get_string('audit_dryrun', 'theme_iqralight'), 3);
if (!class_exists('\filter_iqralight\text_filter')) {
    echo html_writer::tag('p', get_string('audit_dryrun_nofilter', 'theme_iqralight'));
} else {
    echo html_writer::tag('p', get_string('audit_dryrun_intro', 'theme_iqralight'));
    $courses = [];
    foreach (get_courses('all', 'c.fullname ASC', 'c.id, c.fullname') as $course) {
        if ($course->id != SITEID) {
            $courses[$course->id] = format_string($course->fullname);
        }
    }
    echo $OUTPUT->single_select(
        $baseurl,
        'dryrun',
        $courses,
        $courseid,
        ['' => 'choosedots'],
        null,
        ['label' => get_string('audit_course', 'theme_iqralight')]
    );
    if ($courseid && isset($courses[$courseid])) {
        $found = audit::dry_run($courseid);
        if (!$found) {
            echo html_writer::tag('p', get_string('audit_dryrun_nothing', 'theme_iqralight'));
        } else {
            $table = new html_table();
            $table->head = [get_string('audit_activity', 'theme_iqralight'), get_string('audit_kind', 'theme_iqralight'),
                get_string('audit_filesize', 'theme_iqralight')];
            foreach ($found as $item) {
                $table->data[] = [s($item['activity']), get_string('audit_kind_' . $item['kind'], 'theme_iqralight'),
                    $item['bytes'] === null ? get_string('unknownsize', 'filter_iqralight') : $kb($item['bytes'])];
            }
            echo html_writer::table($table);
        }
    }
}

echo html_writer::tag('p', get_string('credit', 'theme_iqralight', 'https://iqratic.com'), ['class' => 'iqra-credit text-muted']);
echo $OUTPUT->footer();
