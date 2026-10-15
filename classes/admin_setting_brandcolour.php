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

namespace theme_iqralight;

use theme_iqralight\local\contrast;

/**
 * Brand colour setting that refuses colours unreadable under white text.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_brandcolour extends \admin_setting_configcolourpicker {
    /**
     * Refuses a colour on which white text would be unreadable, then saves it.
     *
     * @param string $data
     * @return string Empty string on success, an error message otherwise.
     */
    public function write_setting($data) {
        $data = trim((string) $data);
        if ($data !== '') {
            if (!preg_match('/^#[0-9a-fA-F]{6}$/', $data)) {
                return get_string('brandcolor_format', 'theme_iqralight');
            }
            $ratio = contrast::contrast_with_white($data);
            if ($ratio < contrast::MIN_CONTRAST) {
                return get_string('brandcolor_contrast', 'theme_iqralight', (object) [
                    'ratio' => format_float($ratio, 1),
                    'suggestion' => contrast::darken_until_readable($data),
                ]);
            }
        }
        return parent::write_setting($data);
    }
}
