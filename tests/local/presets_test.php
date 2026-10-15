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
 * Tests of the presets, and of the export and import of the configuration.
 *
 * @package    theme_iqralight
 * @category   test
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \theme_iqralight\local\presets
 */
final class presets_test extends \advanced_testcase {
    /**
     * Every shipped preset is valid.
     *
     * @dataProvider shipped_provider
     * @param string $name
     */
    public function test_shipped_presets_are_valid(string $name): void {
        [$values, , $errors] = presets::validate(presets::shipped($name));
        $this->assertSame([], $errors);
        $this->assertNotEmpty($values['theme_iqralight']);
    }

    /**
     * Names of the shipped presets.
     *
     * @return array
     */
    public static function shipped_provider(): array {
        return array_combine(presets::SHIPPED, array_map(fn($name) => [$name], presets::SHIPPED));
    }

    /**
     * A file with invalid values is refused as a whole.
     */
    public function test_invalid_values_are_refused(): void {
        [$values, , $errors] = presets::validate([
            'format' => presets::FORMAT,
            'theme_iqralight' => ['brandcolor' => '#F5D547', 'defaultmode' => 'turbo', 'unknown' => '1'],
            'filter_iqralight' => ['threshold' => '99999', 'excludedmodules' => 'forum,nosuchmodule'],
        ]);
        $this->assertCount(5, $errors);
        $this->assertSame([], $values);

        [, , $errors] = presets::validate(['theme_iqralight' => ['defaultmode' => 'auto']]);
        $this->assertCount(1, $errors);
    }

    /**
     * Applying a preset changes only what differs; applying it twice changes nothing.
     */
    public function test_diff_and_apply(): void {
        $this->resetAfterTest();
        set_config('defaultmode', 'auto', 'theme_iqralight');
        [$values] = presets::validate(presets::shipped('rural'));

        $settings = array_column(presets::diff($values), 'setting');
        $this->assertContains('theme_iqralight/defaultmode', $settings);

        presets::apply($values);
        $this->assertSame('economy', get_config('theme_iqralight', 'defaultmode'));
        $this->assertSame([], presets::diff($values));
    }

    /**
     * The exported configuration, logo included, restores the same site.
     */
    public function test_export_import_roundtrip(): void {
        $this->resetAfterTest();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        set_config('defaultmode', 'economy', 'theme_iqralight');
        presets::save_logo('logo.png', $png);
        $export = json_decode(json_encode(presets::export()), true);

        set_config('defaultmode', 'full', 'theme_iqralight');
        get_file_storage()->delete_area_files(\core\context\system::instance()->id, 'core_admin', 'logo', 0);

        [$values, $logo, $errors] = presets::validate($export);
        $this->assertSame([], $errors);
        presets::apply($values, $logo);
        $this->assertSame('economy', get_config('theme_iqralight', 'defaultmode'));
        $this->assertSame('/logo.png', get_config('core_admin', 'logo'));

        $export['logo']['content'] = base64_encode(str_repeat('x', presets::MAX_LOGO + 1));
        [, , $errors] = presets::validate($export);
        $this->assertCount(1, $errors);
    }
}
