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
 * Colour contrast helpers (WCAG 2.1), with no dependency on admin libraries.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contrast {
    /** @var float Minimum WCAG contrast ratio with white text (AA, normal text). */
    public const MIN_CONTRAST = 4.5;

    /**
     * WCAG contrast ratio between a colour and white.
     *
     * @param string $hex Colour as #RRGGBB.
     * @return float
     */
    public static function contrast_with_white(string $hex): float {
        return 1.05 / (self::luminance($hex) + 0.05);
    }

    /**
     * Darkens a colour step by step until white text on it is readable.
     *
     * @param string $hex Colour as #RRGGBB.
     * @return string The closest readable colour, as #RRGGBB.
     */
    public static function darken_until_readable(string $hex): string {
        [$r, $g, $b] = self::channels($hex);
        for ($factor = 1.0; $factor > 0; $factor -= 0.02) {
            $candidate = sprintf('#%02X%02X%02X', (int) round($r * $factor), (int) round($g * $factor), (int) round($b * $factor));
            if (self::contrast_with_white($candidate) >= self::MIN_CONTRAST) {
                return $candidate;
            }
        }
        return '#000000';
    }

    /**
     * Relative luminance of a colour (WCAG 2.1).
     *
     * @param string $hex
     * @return float
     */
    public static function luminance(string $hex): float {
        $linear = array_map(function ($channel) {
            $c = $channel / 255;
            return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        }, self::channels($hex));
        return 0.2126 * $linear[0] + 0.7152 * $linear[1] + 0.0722 * $linear[2];
    }

    /**
     * Red, green and blue channels of a #RRGGBB colour.
     *
     * @param string $hex
     * @return int[]
     */
    public static function channels(string $hex): array {
        $hex = ltrim($hex, '#');
        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }
}
