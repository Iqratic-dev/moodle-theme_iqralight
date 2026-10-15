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
 * Library functions for theme_iqralight.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Default brand colour: deep Iqralight green, 6:1 contrast with white text. */
define('THEME_IQRALIGHT_DEFAULT_BRAND', '#0B6E5A');

/**
 * Returns the main SCSS: Boost's default preset followed by the Iqralight layer.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_iqralight_get_main_scss_content($theme) {
    global $CFG;

    $scss = file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    $scss .= "\n" . file_get_contents(__DIR__ . '/scss/iqra.scss');
    // Light icon fonts: declared after Boost's full faces so that they take precedence.
    $scss .= "\n" . file_get_contents(__DIR__ . '/scss/iqra-icons.scss');
    // Dark mode: generated overrides of Moodle's fixed colours, then the dark scope.
    $scss .= "\n" . file_get_contents(__DIR__ . '/scss/iqra-dark-core.scss');
    $scss .= "\n" . file_get_contents(__DIR__ . '/scss/iqra-dark.scss');
    return $scss;
}

/**
 * Returns the variables injected before the main SCSS.
 *
 * Only the brand colour is configurable: every other shade is derived from it.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_iqralight_get_pre_scss($theme) {
    $brand = $theme->settings->brandcolor ?? '';
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $brand)) {
        $brand = THEME_IQRALIGHT_DEFAULT_BRAND;
    }

    $scss = '$primary: ' . $brand . ";\n";
    $scss .= '$iqra-brand: ' . $brand . ";\n";
    // System fonts only: nothing is downloaded.
    $scss .= '$font-family-sans-serif: system-ui, -apple-system, "Segoe UI", "Noto Sans", "Noto Sans Arabic", sans-serif;' . "\n";
    $scss .= '$body-bg: #F3F5F4;' . "\n";
    $scss .= '$body-color: #10201C;' . "\n";
    $scss .= '$border-radius: 0.875rem;' . "\n";
    $scss .= '$border-radius-sm: 0.625rem;' . "\n";
    $scss .= '$border-radius-lg: 1.25rem;' . "\n";
    $scss .= '$btn-border-radius: 0.875rem;' . "\n";
    $scss .= '$input-border-radius: 0.875rem;' . "\n";
    $scss .= '$card-border-radius: 1.125rem;' . "\n";
    $scss .= '$card-border-color: #E1E8E5;' . "\n";
    $scss .= '$link-color: ' . $brand . ";\n";
    return $scss;
}

/**
 * Returns extra SCSS appended after everything else.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_iqralight_get_extra_scss($theme) {
    return '';
}

/**
 * Declares the user preferences that the theme lets learners change themselves.
 *
 * @return array
 */
function theme_iqralight_user_preferences() {
    return [
        \theme_iqralight\local\mode::PREF_MODE => [
            'type' => PARAM_ALPHA,
            'null' => NULL_NOT_ALLOWED,
            'default' => 'auto',
            'choices' => \theme_iqralight\local\mode::MODES,
            'permissioncallback' => [\core_user::class, 'is_current_user'],
        ],
        \theme_iqralight\local\mode::PREF_TEXTONLY => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => 0,
            'permissioncallback' => [\core_user::class, 'is_current_user'],
        ],
        \theme_iqralight\local\mode::PREF_CONTRAST => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => 0,
            'permissioncallback' => [\core_user::class, 'is_current_user'],
        ],
        \theme_iqralight\local\mode::PREF_EASYREAD => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => 0,
            'permissioncallback' => [\core_user::class, 'is_current_user'],
        ],
    ];
}

/**
 * Shows the data the learner avoided this month on their own profile page.
 *
 * @param \core_user\output\myprofile\tree $tree
 * @param stdClass $user
 * @param bool $iscurrentuser
 * @param stdClass|null $course
 */
function theme_iqralight_myprofile_navigation(\core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    if (!$iscurrentuser) {
        return;
    }
    $sentence = \theme_iqralight\local\stats::learner_sentence((int) $user->id);
    if ($sentence !== null) {
        $tree->add_node(new \core_user\output\myprofile\node(
            'miscellaneous',
            'theme_iqralight_dataavoided',
            get_string('dataavoided', 'theme_iqralight'),
            null,
            null,
            $sentence
        ));
    }
}

/**
 * Adds the course lightness report to the course menu, for those who can edit the course.
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context $context
 */
function theme_iqralight_extend_navigation_course($navigation, $course, $context) {
    if (!has_capability('moodle/course:update', $context)) {
        return;
    }
    $navigation->add(
        get_string('coursereport', 'theme_iqralight'),
        new moodle_url('/theme/iqralight/coursereport.php', ['id' => $course->id]),
        navigation_node::TYPE_SETTING,
        null,
        'theme_iqralight_coursereport',
        new pix_icon('i/report', '')
    );
}
