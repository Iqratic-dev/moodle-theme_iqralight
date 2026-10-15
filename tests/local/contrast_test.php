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
 * Tests of the colour contrast helpers.
 *
 * @package    theme_iqralight
 * @category   test
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \theme_iqralight\local\contrast
 */
final class contrast_test extends \basic_testcase {
    /**
     * Contrast of reference colours with white text.
     */
    public function test_contrast_with_white(): void {
        $this->assertEqualsWithDelta(1.0, contrast::contrast_with_white('#ffffff'), 0.01);
        $this->assertEqualsWithDelta(21.0, contrast::contrast_with_white('#000000'), 0.01);
        $this->assertGreaterThanOrEqual(contrast::MIN_CONTRAST, contrast::contrast_with_white('#0B6E5A'));
        $this->assertLessThan(contrast::MIN_CONTRAST, contrast::contrast_with_white('#F5D547'));
    }

    /**
     * Light colours become readable shades of themselves.
     *
     * @dataProvider light_colours_provider
     * @param string $colour
     */
    public function test_darken_until_readable(string $colour): void {
        $readable = contrast::darken_until_readable($colour);
        $this->assertMatchesRegularExpression('/^#[0-9A-F]{6}$/', $readable);
        $this->assertGreaterThanOrEqual(contrast::MIN_CONTRAST, contrast::contrast_with_white($readable));
    }

    /**
     * Colours too light for white text.
     *
     * @return array
     */
    public static function light_colours_provider(): array {
        return [
            'yellow' => ['#F5D547'],
            'light green' => ['#7ED957'],
            'sky blue' => ['#87CEEB'],
            'white' => ['#FFFFFF'],
        ];
    }
}
