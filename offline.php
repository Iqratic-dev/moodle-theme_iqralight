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
 * Offline page, cached by the service worker and shown when a page cannot be reached.
 *
 * Self-contained: inline styles, no external resource, no user data.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.RequireLogin.Missing -- Public page, no user data.
define('NO_MOODLE_COOKIES', true);

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$brand = get_config('theme_iqralight', 'brandcolor');
if (!preg_match('/^#[0-9a-fA-F]{6}$/', (string) $brand)) {
    $brand = THEME_IQRALIGHT_DEFAULT_BRAND;
}
$lang = current_language();
$dir = get_string('thisdirection', 'langconfig');

$context = [
    'lang' => $lang,
    'dir' => $dir,
    'brand' => $brand,
    'sitename' => format_string($SITE->shortname, true, ['context' => context_system::instance()]),
    'title' => get_string('offline_title', 'theme_iqralight'),
    'message' => get_string('offline_message', 'theme_iqralight'),
    'retry' => get_string('offline_retry', 'theme_iqralight'),
    'reading' => (bool) get_config('theme_iqralight', 'offlinereading'),
    'indexurl' => json_encode(
        rtrim($CFG->wwwroot, '/') . '/theme/iqralight/offline-index.json',
        JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ),
    'keptlabel' => get_string('offline_kept', 'theme_iqralight'),
    'nonelabel' => get_string('offline_none', 'theme_iqralight'),
];

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, max-age=0');
echo $OUTPUT->render_from_template('theme_iqralight/offline', $context);
