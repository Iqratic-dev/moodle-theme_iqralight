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
 * Presets: apply a ready-made configuration, export the current one, or import one.
 *
 * Every change is previewed before it is applied.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use theme_iqralight\local\presets;

admin_externalpage_setup('theme_iqralight_presets');

$preset = optional_param('preset', '', PARAM_ALPHA);
$action = optional_param('action', '', PARAM_ALPHA);
$baseurl = new moodle_url('/theme/iqralight/presets.php');

if ($action === 'export') {
    require_sesskey();
    $json = json_encode(presets::export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    send_file($json, 'iqra-config-' . date('Ymd') . '.json', 0, 0, true, true, 'application/json');
}

if ($action === 'apply') {
    require_sesskey();
    $data = $preset !== '' ? presets::shipped($preset) : json_decode(base64_decode(required_param('payload', PARAM_RAW)), true);
    [$values, $logo, $errors] = presets::validate($data);
    if ($errors) {
        throw new moodle_exception('preseterror_format', 'theme_iqralight');
    }
    presets::apply($values, $logo);
    redirect($baseurl, get_string('presetapplied', 'theme_iqralight'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$form = new \theme_iqralight\form\import_preset($baseurl);
$preview = null;
$payload = '';
if ($preset !== '') {
    $preview = presets::shipped($preset);
} else if ($form->is_submitted() && $form->is_validated()) {
    $content = $form->get_file_content('presetfile');
    $preview = json_decode((string) $content, true);
    $payload = base64_encode((string) $content);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('presets', 'theme_iqralight'));

if ($preview !== null) {
    [$values, $logo, $errors] = presets::validate($preview);
    if ($errors) {
        echo $OUTPUT->notification(html_writer::alist(array_map('s', $errors)), 'error');
    } else {
        $changes = presets::diff($values);
        echo $OUTPUT->heading(get_string('presetpreview', 'theme_iqralight'), 3);
        if (!$changes && !$logo) {
            echo html_writer::tag('p', get_string('presetnochange', 'theme_iqralight'));
        } else {
            $table = new html_table();
            $table->head = [get_string('setting', 'theme_iqralight'), get_string('currentvalue', 'theme_iqralight'),
                get_string('newvalue', 'theme_iqralight')];
            foreach ($changes as $change) {
                $table->data[] = [s($change['setting']), s($change['current']), html_writer::tag('strong', s($change['new']))];
            }
            if ($logo) {
                $table->data[] = ['core_admin/logo', '', s($logo['filename'])];
            }
            echo html_writer::table($table);
            $params = ['action' => 'apply', 'sesskey' => sesskey()];
            $params += $preset !== '' ? ['preset' => $preset] : ['payload' => $payload];
            echo $OUTPUT->single_button(
                new moodle_url($baseurl, $params),
                get_string('presetapply', 'theme_iqralight'),
                'post',
                ['type' => single_button::BUTTON_PRIMARY]
            );
        }
        echo html_writer::link($baseurl, get_string('cancel'));
    }
} else {
    echo html_writer::tag('p', get_string('presets_intro', 'theme_iqralight'));
    foreach (presets::SHIPPED as $name) {
        echo $OUTPUT->box_start('generalbox iqra-preset');
        echo $OUTPUT->heading(get_string('preset_' . $name, 'theme_iqralight'), 4);
        echo html_writer::tag('p', get_string('preset_' . $name . '_desc', 'theme_iqralight'));
        echo html_writer::link(
            new moodle_url($baseurl, ['preset' => $name]),
            get_string('presetpreviewlink', 'theme_iqralight'),
            ['class' => 'btn btn-secondary']
        );
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->heading(get_string('exportimport', 'theme_iqralight'), 3);
    echo $OUTPUT->single_button(
        new moodle_url($baseurl, ['action' => 'export', 'sesskey' => sesskey()]),
        get_string('exportconfig', 'theme_iqralight'),
        'post'
    );
    $form->display();
}

echo html_writer::tag('p', get_string('credit', 'theme_iqralight', 'https://iqratic.com'), ['class' => 'iqra-credit text-muted']);
echo $OUTPUT->footer();
