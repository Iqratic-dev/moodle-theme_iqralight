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
 * Applies an Iqralight preset from the command line, for hosts deploying several sites.
 *
 * Usage:
 *   php theme/iqralight/cli/install.php --list
 *   php theme/iqralight/cli/install.php --preset=rural
 *   php theme/iqralight/cli/install.php --file=/path/to/export.json [--dry-run]
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/filterlib.php');

use theme_iqralight\local\presets;

[$options, $unrecognised] = cli_get_params(
    ['list' => false, 'preset' => '', 'file' => '', 'dry-run' => false, 'help' => false],
    ['l' => 'list', 'p' => 'preset', 'f' => 'file', 'n' => 'dry-run', 'h' => 'help']
);

if ($unrecognised) {
    cli_error(get_string('cliunknowoption', 'admin', implode("\n  ", $unrecognised)));
}

if ($options['help'] || (!$options['list'] && $options['preset'] === '' && $options['file'] === '')) {
    cli_writeln(get_string('cli_help', 'theme_iqralight'));
    exit(0);
}

if ($options['list']) {
    foreach (presets::SHIPPED as $name) {
        cli_writeln(str_pad($name, 12) . get_string('preset_' . $name, 'theme_iqralight'));
    }
    exit(0);
}

if ($options['file'] !== '') {
    if (!is_readable($options['file'])) {
        cli_error(get_string('cli_unreadable', 'theme_iqralight', $options['file']));
    }
    $data = json_decode(file_get_contents($options['file']), true);
} else {
    $data = presets::shipped($options['preset']);
}

[$values, $logo, $errors] = presets::validate($data);
if ($errors) {
    cli_error(implode("\n", $errors));
}

foreach (presets::diff($values) as $change) {
    cli_writeln("{$change['setting']}: '{$change['current']}' -> '{$change['new']}'");
}
if ($logo) {
    cli_writeln("core_admin/logo: {$logo['filename']}");
}

if ($options['dry-run']) {
    cli_writeln(get_string('cli_dryrun', 'theme_iqralight'));
    exit(0);
}

presets::apply($values, $logo);
if (core_component::get_component_directory('filter_iqralight') !== null) {
    filter_set_global_state('iqralight', TEXTFILTER_ON);
}
cli_writeln(get_string('presetapplied', 'theme_iqralight'));
