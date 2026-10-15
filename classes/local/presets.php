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

use core\context\system as context_system;


/**
 * Presets: ready-made configurations, and export or import of the whole configuration.
 *
 * Every value is checked against the same rules as the settings pages before
 * anything is written; nothing is applied when a single value is invalid.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class presets {
    /** @var string[] Presets shipped with the theme, in display order. */
    public const SHIPPED = ['rural', 'sharedroom', 'university', 'prudent'];

    /** @var string Format marker of preset files. */
    public const FORMAT = 'iqralight-preset';

    /** @var int Largest logo accepted, in bytes (RG-12.3). */
    public const MAX_LOGO = 51200;

    /**
     * Settings a preset may contain, by component.
     *
     * @return array
     */
    public static function settings(): array {
        return [
            'theme_iqralight' => ['brandcolor', 'darkmode', 'defaultmode', 'offlinereading', 'offlinequota', 'savedata',
                'autotextonly', 'serviceworker', 'stats',
                'statsanonymous', 'statsretention', 'mbprice', 'mbcurrency'],
            'filter_iqralight' => ['threshold', 'excludedmodules'],
        ];
    }

    /**
     * Reads a preset shipped with the theme.
     *
     * @param string $name
     * @return array
     */
    public static function shipped(string $name): array {
        if (!in_array($name, self::SHIPPED, true)) {
            throw new \moodle_exception('presetunknown', 'theme_iqralight', '', $name);
        }
        return json_decode(file_get_contents(__DIR__ . '/../../presets/' . $name . '.json'), true);
    }

    /**
     * Checks a preset and returns its clean values.
     *
     * @param mixed $data Decoded JSON.
     * @return array [values by component, logo or null, list of errors]
     */
    public static function validate($data): array {
        $errors = [];
        $values = [];
        if (!is_array($data) || ($data['format'] ?? '') !== self::FORMAT) {
            return [[], null, [get_string('preseterror_format', 'theme_iqralight')]];
        }
        foreach (self::settings() as $component => $names) {
            foreach ((array) ($data[$component] ?? []) as $name => $value) {
                if (!in_array($name, $names, true) || !is_scalar($value)) {
                    $errors[] = get_string('preseterror_unknown', 'theme_iqralight', "{$component}/{$name}");
                    continue;
                }
                $value = trim((string) $value);
                if (!self::valid($component, $name, $value)) {
                    $errors[] = get_string('preseterror_value', 'theme_iqralight', "{$component}/{$name}");
                    continue;
                }
                $values[$component][$name] = $value;
            }
        }
        $logo = null;
        if (!empty($data['logo'])) {
            [$logo, $logoerror] = self::validate_logo($data['logo']);
            if ($logoerror !== null) {
                $errors[] = $logoerror;
            }
        }
        return [$values, $logo, $errors];
    }

    /**
     * Whether a single value is acceptable.
     *
     * @param string $component
     * @param string $name
     * @param string $value
     * @return bool
     */
    protected static function valid(string $component, string $name, string $value): bool {
        switch ("{$component}/{$name}") {
            case 'theme_iqralight/brandcolor':
                return preg_match('/^#[0-9a-fA-F]{6}$/', $value) &&
                    contrast::contrast_with_white($value) >= contrast::MIN_CONTRAST;
            case 'theme_iqralight/defaultmode':
                return in_array($value, mode::MODES, true);
            case 'theme_iqralight/darkmode':
                return in_array($value, ['auto', 'off', 'on'], true);
            case 'theme_iqralight/offlinequota':
                return ctype_digit($value) && (int) $value >= 10 && (int) $value <= 200;
            case 'theme_iqralight/statsretention':
                return ctype_digit($value) && (int) $value >= 1 && (int) $value <= 365;
            case 'theme_iqralight/mbprice':
                return (bool) preg_match('/^(?:\d{1,6}(?:\.\d{1,4})?)?$/', $value);
            case 'theme_iqralight/mbcurrency':
                return \core_text::strlen($value) <= 10 && clean_param($value, PARAM_TEXT) === $value;
            case 'filter_iqralight/threshold':
                return ctype_digit($value) && (int) $value >= 50 && (int) $value <= 2048;
            case 'filter_iqralight/excludedmodules':
                $modules = \core_component::get_plugin_list('mod');
                return $value === '' || !array_diff(explode(',', $value), array_keys($modules));
            default:
                return in_array($value, ['0', '1'], true);
        }
    }

    /**
     * Checks an embedded logo.
     *
     * @param mixed $logo
     * @return array [clean logo or null, error or null]
     */
    protected static function validate_logo($logo): array {
        $filename = clean_param($logo['filename'] ?? '', PARAM_FILE);
        $content = is_string($logo['content'] ?? null) ? base64_decode($logo['content'], true) : false;
        if ($filename === '' || $content === false || !preg_match('/\.(png|jpe?g)$/i', $filename)) {
            return [null, get_string('preseterror_logo', 'theme_iqralight')];
        }
        if (strlen($content) > self::MAX_LOGO) {
            return [null, get_string('logotoolarge', 'theme_iqralight', display_size(strlen($content)))];
        }
        return [['filename' => $filename, 'content' => $content], null];
    }

    /**
     * Differences between the clean values of a preset and the current configuration.
     *
     * @param array $values
     * @return array[] Each with setting, current and new.
     */
    public static function diff(array $values): array {
        $changes = [];
        foreach ($values as $component => $settings) {
            foreach ($settings as $name => $value) {
                $current = (string) get_config($component, $name);
                if ($current !== $value) {
                    $changes[] = ['setting' => "{$component}/{$name}", 'current' => $current, 'new' => $value];
                }
            }
        }
        return $changes;
    }

    /**
     * Applies clean values, and the logo when there is one.
     *
     * @param array $values
     * @param array|null $logo
     */
    public static function apply(array $values, ?array $logo = null): void {
        foreach ($values as $component => $settings) {
            foreach ($settings as $name => $value) {
                set_config($name, $value, $component);
            }
        }
        if ($logo !== null) {
            self::save_logo($logo['filename'], $logo['content']);
        }
        theme_reset_all_caches();
    }

    /**
     * Stores a logo exactly where Moodle's own Logos setting keeps it.
     *
     * @param string $filename
     * @param string $content
     */
    public static function save_logo(string $filename, string $content): void {
        $fs = get_file_storage();
        $context = context_system::instance();
        $fs->delete_area_files($context->id, 'core_admin', 'logo', 0);
        $fs->create_file_from_string([
            'contextid' => $context->id,
            'component' => 'core_admin',
            'filearea' => 'logo',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename,
        ], $content);
        set_config('logo', '/' . $filename, 'core_admin');
    }

    /**
     * The whole current configuration, as a preset.
     *
     * @return array
     */
    public static function export(): array {
        $data = ['format' => self::FORMAT, 'version' => 1];
        foreach (self::settings() as $component => $names) {
            foreach ($names as $name) {
                $value = get_config($component, $name);
                if ($value !== false) {
                    $data[$component][$name] = (string) $value;
                }
            }
        }
        $files = get_file_storage()->get_area_files(context_system::instance()->id, 'core_admin', 'logo', 0, 'id', false);
        $file = reset($files);
        if ($file && $file->get_filesize() <= self::MAX_LOGO) {
            $data['logo'] = ['filename' => $file->get_filename(), 'content' => base64_encode($file->get_content())];
        }
        return $data;
    }
}
