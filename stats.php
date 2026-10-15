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
 * Receives the data counter reports sent by learners' browsers.
 *
 * Called with navigator.sendBeacon(): a POST request carrying the session key.
 * Amounts are capped; the request never returns data.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('NO_DEBUG_DISPLAY', true);

require(__DIR__ . '/../../config.php');

require_login(null, false, null, false, true);
require_sesskey();

if (!isguestuser() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    \theme_iqralight\local\stats::record(
        (int) $USER->id,
        required_param('cache', PARAM_INT),
        required_param('media', PARAM_INT)
    );
}

http_response_code(204);
