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
 * First-time setup wizard: colour, logo, profile, then Moodle settings worth enabling.
 *
 * Native Moodle settings are only changed when the administrator ticks them (RG-12.8).
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use theme_iqralight\local\presets;

admin_externalpage_setup('theme_iqralight_setup');

$step = min(4, max(1, optional_param('step', 1, PARAM_INT)));
$baseurl = new moodle_url('/theme/iqralight/setup.php');
$context = context_system::instance();
$native = [
    'homepage' => (int) $CFG->defaulthomepage === HOMEPAGE_MYCOURSES,
    'completion' => !empty($CFG->enablecompletion),
];

$form = new \theme_iqralight\form\setup(new moodle_url($baseurl, ['step' => $step]), ['step' => $step, 'native' => $native]);

if ($step === 1) {
    $current = (string) get_config('theme_iqralight', 'brandcolor');
    $default = in_array(strtoupper($current), \theme_iqralight\form\setup::COLOURS, true) ? strtoupper($current) : 'other';
    $form->set_data(['brandcolor' => $current === '' ? \theme_iqralight\form\setup::COLOURS[0] : $default,
        'customcolour' => $default === 'other' ? $current : '']);
} else if ($step === 2) {
    $draft = file_get_submitted_draft_itemid('logo');
    file_prepare_draft_area($draft, $context->id, 'core_admin', 'logo', 0, ['maxfiles' => 1, 'subdirs' => 0]);
    $form->set_data(['logo' => $draft]);
}

if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php', ['section' => 'themesettingiqralight']));
}

if ($data = $form->get_data()) {
    switch ($step) {
        case 1:
            $colour = $data->brandcolor === 'other' ? trim($data->customcolour) : $data->brandcolor;
            set_config('brandcolor', $colour, 'theme_iqralight');
            theme_reset_all_caches();
            break;
        case 2:
            file_save_draft_area_files($data->logo, $context->id, 'core_admin', 'logo', 0, ['maxfiles' => 1, 'subdirs' => 0]);
            $files = get_file_storage()->get_area_files($context->id, 'core_admin', 'logo', 0, 'id', false);
            $file = reset($files);
            set_config('logo', $file ? '/' . $file->get_filename() : '', 'core_admin');
            theme_reset_all_caches();
            break;
        case 3:
            if ($data->profile !== '') {
                [$values, $logo] = presets::validate(presets::shipped($data->profile));
                presets::apply($values);
            }
            break;
        default:
            if (!$native['homepage'] && !empty($data->homepage)) {
                set_config('defaulthomepage', HOMEPAGE_MYCOURSES);
            }
            if (!$native['completion'] && !empty($data->completion)) {
                set_config('enablecompletion', 1);
            }
            redirect(
                new moodle_url('/admin/settings.php', ['section' => 'themesettingiqralight']),
                get_string('setup_done', 'theme_iqralight'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
    }
    redirect(new moodle_url($baseurl, ['step' => $step + 1]));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('setup', 'theme_iqralight'));
$form->display();
echo html_writer::tag('p', get_string('credit', 'theme_iqralight', 'https://iqratic.com'), ['class' => 'iqra-credit text-muted']);
echo $OUTPUT->footer();
