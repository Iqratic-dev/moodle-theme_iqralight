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
 * Category profiles (RG-12.5): make parts of the site lighter than the rest.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use theme_iqralight\local\profiles;

admin_externalpage_setup('theme_iqralight_profiles');

$baseurl = new moodle_url('/theme/iqralight/profiles.php');
$categories = core_course_category::make_categories_list();

if (optional_param('save', 0, PARAM_BOOL) && confirm_sesskey()) {
    $submitted = [];
    foreach (array_keys($categories) as $categoryid) {
        $submitted[$categoryid] = optional_param('profile_' . $categoryid, 'site', PARAM_ALPHA);
    }
    profiles::save($submitted);
    redirect($baseurl, get_string('profilessaved', 'theme_iqralight'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$current = profiles::all();
$levels = [];
foreach (profiles::LEVELS as $level) {
    $levels[$level] = get_string('profile_' . $level, 'theme_iqralight');
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('profiles', 'theme_iqralight'));
echo html_writer::tag('p', get_string('profiles_intro', 'theme_iqralight'));

if (!$categories) {
    echo html_writer::tag('p', get_string('profiles_nocategory', 'theme_iqralight'));
} else {
    echo html_writer::start_tag('form', ['method' => 'post', 'action' => $baseurl->out(false)]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'save', 'value' => 1]);
    $table = new html_table();
    $table->head = [get_string('category'), get_string('profile', 'theme_iqralight')];
    foreach ($categories as $categoryid => $name) {
        $select = html_writer::select(
            $levels,
            'profile_' . $categoryid,
            $current[$categoryid] ?? 'site',
            false,
            ['id' => 'profile_' . $categoryid, 'class' => 'form-select']
        );
        $table->data[] = [html_writer::label(s($name), 'profile_' . $categoryid), $select];
    }
    echo html_writer::table($table);
    echo html_writer::tag('button', get_string('savechanges'), ['type' => 'submit', 'class' => 'btn btn-primary']);
    echo html_writer::end_tag('form');
}

echo html_writer::tag('p', get_string('credit', 'theme_iqralight', 'https://iqratic.com'), ['class' => 'iqra-credit text-muted']);
echo $OUTPUT->footer();
