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

namespace theme_iqralight\local;

/**
 * Wording of Iqralight's own texts: a simpler variant for learners who chose easy reading (RG-14.2).
 *
 * A string "key_easy", when it exists, replaces "key" for them.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wording {
    /**
     * A string of the theme, simplified for easy reading when a simpler variant exists.
     *
     * @param string $key
     * @param mixed $a
     * @return string
     */
    public static function get(string $key, $a = null): string {
        if (mode::display_option(mode::PREF_EASYREAD) && get_string_manager()->string_exists($key . '_easy', 'theme_iqralight')) {
            return get_string($key . '_easy', 'theme_iqralight', $a);
        }
        return get_string($key, 'theme_iqralight', $a);
    }
}
