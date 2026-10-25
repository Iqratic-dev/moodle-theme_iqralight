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
 * Serves the Iqralight service worker for the whole site.
 *
 * The script is public and session-less: it holds no user data. It is served
 * with Service-Worker-Allowed so that it can control every page of the site.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.RequireLogin.Missing -- Public script, no user data.
define('NO_MOODLE_COOKIES', true);
define('NO_DEBUG_DISPLAY', true);

require(__DIR__ . '/../../config.php');

$enabled = get_config('theme_iqralight', 'serviceworker') !== '0';
$root = rtrim($CFG->wwwroot, '/');
$scope = (string) parse_url($root, PHP_URL_PATH) . '/';
$version = implode('-', [(int) get_config('theme_iqralight', 'version'), (int) $CFG->themerev, (int) $CFG->jsrev]);

$values = [
    'IQRA_VERSION' => json_encode($version),
    'IQRA_ENABLED' => $enabled ? 'true' : 'false',
    'IQRA_OFFLINE_URL' => json_encode((new moodle_url('/theme/iqralight/offline.php'))->out(false)),
    'IQRA_ROOT' => json_encode($root),
    'IQRA_READING' => get_config('theme_iqralight', 'offlinereading') ? 'true' : 'false',
];
$script = file_get_contents(__DIR__ . '/sw/serviceworker.js');
$script = preg_replace('~/\* global IQRA_[^*]*\*/\n~', '', $script);
$script = "'use strict';\n" . str_replace(array_keys($values), array_values($values), $script);

header('Content-Type: application/javascript; charset=utf-8');
header('Service-Worker-Allowed: ' . $scope);
header('Cache-Control: no-cache, max-age=0');
header('X-Content-Type-Options: nosniff');
echo $script;
