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
 * Settings for theme_iqralight.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingiqralight', get_string('configtitle', 'theme_iqralight'));

    // Appearance.
    $page = new admin_settingpage('theme_iqralight_appearance', get_string('appearance', 'theme_iqralight'));

    $page->add(new admin_setting_heading(
        'theme_iqralight/quickstart',
        '',
        get_string('credit', 'theme_iqralight', 'https://iqratic.com') . ' ' .
        get_string('wizardlinks', 'theme_iqralight', (object) [
            'setup' => (new moodle_url('/theme/iqralight/setup.php'))->out(),
            'presets' => (new moodle_url('/theme/iqralight/presets.php'))->out(),
        ])
    ));

    $setting = new \theme_iqralight\admin_setting_brandcolour(
        'theme_iqralight/brandcolor',
        get_string('brandcolor', 'theme_iqralight'),
        get_string('brandcolor_desc', 'theme_iqralight'),
        THEME_IQRALIGHT_DEFAULT_BRAND
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $page->add(new admin_setting_description(
        'theme_iqralight/logos',
        get_string('logos', 'theme_iqralight'),
        get_string('logos_desc', 'theme_iqralight', (new moodle_url('/admin/settings.php', ['section' => 'logos']))->out())
    ));

    $page->add(new admin_setting_configselect(
        'theme_iqralight/darkmode',
        get_string('darkmode', 'theme_iqralight'),
        get_string('darkmode_desc', 'theme_iqralight'),
        'auto',
        [
            'auto' => get_string('darkmode_auto', 'theme_iqralight'),
            'off' => get_string('darkmode_off', 'theme_iqralight'),
            'on' => get_string('darkmode_on', 'theme_iqralight'),
        ]
    ));

    $settings->add($page);

    // Lightness.
    $page = new admin_settingpage('theme_iqralight_lightness', get_string('lightness', 'theme_iqralight'));

    $page->add(new admin_setting_configselect(
        'theme_iqralight/defaultmode',
        get_string('defaultmode', 'theme_iqralight'),
        get_string('defaultmode_desc', 'theme_iqralight'),
        'auto',
        [
            'auto' => get_string('mode_auto', 'theme_iqralight'),
            'economy' => get_string('mode_economy', 'theme_iqralight'),
            'full' => get_string('mode_full', 'theme_iqralight'),
        ]
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_iqralight/savedata',
        get_string('savedata', 'theme_iqralight'),
        get_string('savedata_desc', 'theme_iqralight'),
        1
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_iqralight/autotextonly',
        get_string('autotextonly', 'theme_iqralight'),
        get_string('autotextonly_desc', 'theme_iqralight'),
        1
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_iqralight/offlinereading',
        get_string('offlinereading', 'theme_iqralight'),
        get_string('offlinereading_desc', 'theme_iqralight'),
        0
    ));

    $page->add(new admin_setting_configtext(
        'theme_iqralight/offlinequota',
        get_string('offlinequota', 'theme_iqralight'),
        get_string('offlinequota_desc', 'theme_iqralight'),
        50,
        '/^(?:[1-9]\d|1\d{2}|200)$/',
        4
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_iqralight/serviceworker',
        get_string('serviceworker', 'theme_iqralight'),
        get_string('serviceworker_desc', 'theme_iqralight'),
        1
    ));

    $settings->add($page);

    // Data counter.
    $page = new admin_settingpage('theme_iqralight_counter', get_string('counter', 'theme_iqralight'));

    $page->add(new admin_setting_configcheckbox(
        'theme_iqralight/stats',
        get_string('stats', 'theme_iqralight'),
        get_string('stats_desc', 'theme_iqralight'),
        1
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_iqralight/statsanonymous',
        get_string('statsanonymous', 'theme_iqralight'),
        get_string('statsanonymous_desc', 'theme_iqralight'),
        0
    ));

    $page->add(new admin_setting_configtext(
        'theme_iqralight/statsretention',
        get_string('statsretention', 'theme_iqralight'),
        get_string('statsretention_desc', 'theme_iqralight'),
        \theme_iqralight\local\stats::DEFAULT_RETENTION,
        '/^(?:[1-9]\d?|[12]\d{2}|3[0-5]\d|36[0-5])$/',
        4
    ));

    $page->add(new admin_setting_configtext(
        'theme_iqralight/mbprice',
        get_string('mbprice', 'theme_iqralight'),
        get_string('mbprice_desc', 'theme_iqralight'),
        '',
        '/^(?:\d{1,6}(?:\.\d{1,4})?)?$/',
        8
    ));

    $page->add(new admin_setting_configtext(
        'theme_iqralight/mbcurrency',
        get_string('mbcurrency', 'theme_iqralight'),
        get_string('mbcurrency_desc', 'theme_iqralight'),
        '',
        PARAM_TEXT,
        8
    ));

    $settings->add($page);

    // Advanced: the administrator's own SCSS, as in Boost.
    $page = new admin_settingpage('theme_iqralight_advanced', get_string('advanced', 'theme_iqralight'));
    $setting = new admin_setting_scsscode(
        'theme_iqralight/scsspre',
        get_string('rawscsspre', 'theme_iqralight'),
        get_string('rawscsspre_desc', 'theme_iqralight'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $setting = new admin_setting_scsscode(
        'theme_iqralight/scss',
        get_string('rawscss', 'theme_iqralight'),
        get_string('rawscss_desc', 'theme_iqralight'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    $settings->add($page);
}

$ADMIN->add('themes', new admin_externalpage(
    'theme_iqralight_setup',
    get_string('setup', 'theme_iqralight'),
    new moodle_url('/theme/iqralight/setup.php'),
    'moodle/site:config'
));

$ADMIN->add('themes', new admin_externalpage(
    'theme_iqralight_presets',
    get_string('presets', 'theme_iqralight'),
    new moodle_url('/theme/iqralight/presets.php'),
    'moodle/site:config'
));

$ADMIN->add('themes', new admin_externalpage(
    'theme_iqralight_profiles',
    get_string('profiles', 'theme_iqralight'),
    new moodle_url('/theme/iqralight/profiles.php'),
    'moodle/site:config'
));

$ADMIN->add('themes', new admin_externalpage(
    'theme_iqralight_audit',
    get_string('audit', 'theme_iqralight'),
    new moodle_url('/theme/iqralight/audit.php'),
    'moodle/site:config'
));

$ADMIN->add('themes', new admin_externalpage(
    'theme_iqralight_stats',
    get_string('report_title', 'theme_iqralight'),
    new moodle_url('/theme/iqralight/stats_report.php'),
    'moodle/site:config'
));
