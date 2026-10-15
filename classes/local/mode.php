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
 * Resolves the connection mode of the current request.
 *
 * Order of precedence: the learner's own choice, the browser's Save-Data
 * header, the network measure stored by the theme's script, then the site
 * default. Without any information, the light mode is used (EF-04).
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mode {
    /** @var string Preference holding the learner's choice. */
    public const PREF_MODE = 'theme_iqralight_mode';
    /** @var string Preference holding the "text only" option. */
    public const PREF_TEXTONLY = 'theme_iqralight_textonly';
    /** @var string Preference holding the high contrast display (UX-12). */
    public const PREF_CONTRAST = 'theme_iqralight_contrast';
    /** @var string Preference holding the easy reading display (UX-13). */
    public const PREF_EASYREAD = 'theme_iqralight_easyread';
    /** @var string[] Page types covered by the dark mode; every other page stays light. */
    public const DARK_PAGES = ['my-index', 'course-view-', 'mod-page-view', 'mod-forum-view', 'mod-assign-view',
        'mod-quiz-view', 'mod-book-view', 'message-index', 'user-profile', 'grade-report-overview-index',
        'calendar-view', 'user-preferences', 'login-index'];
    /** @var string Cookie set by the theme's script after measuring the network. */
    public const COOKIE_NETWORK = 'iqralight_net';
    /** @var string[] Modes a learner can request. */
    public const MODES = ['auto', 'economy', 'full'];

    /**
     * The mode requested by the learner, or the site default.
     *
     * @return string One of self::MODES.
     */
    public static function requested(): string {
        if (isloggedin() && !isguestuser()) {
            $pref = get_user_preferences(self::PREF_MODE, null);
            if (in_array($pref, self::MODES, true)) {
                return $pref;
            }
        }
        // A category profile can only lighten (RG-12.5).
        if (profiles::current() !== 'site') {
            return 'economy';
        }
        $default = get_config('theme_iqralight', 'defaultmode');
        return in_array($default, self::MODES, true) ? $default : 'auto';
    }

    /**
     * The mode actually applied to this page: 'economy' or 'full'.
     *
     * @return string
     */
    public static function effective(): string {
        $requested = self::requested();
        if ($requested !== 'auto') {
            return $requested;
        }
        if (self::browser_saves_data()) {
            return 'economy';
        }
        return self::measured_network() === 'fast' ? 'full' : 'economy';
    }

    /**
     * Whether only text is shown, each image loading on a tap (EF-07).
     *
     * Requires filter_iqralight, which prepares the images. Applies in data saver mode only,
     * when the learner asks for it or on a very slow connection.
     *
     * @return bool
     */
    public static function text_only(): bool {
        if (self::effective() !== 'economy') {
            return false;
        }
        $choice = isloggedin() && !isguestuser() ? get_user_preferences(self::PREF_TEXTONLY, null) : null;
        if ($choice !== null) {
            // The learner's own choice comes first, in both directions.
            return (bool) $choice;
        }
        if (profiles::current() === 'lighter') {
            return true;
        }
        return self::measured_network() === 'veryslow' && get_config('theme_iqralight', 'autotextonly') !== '0';
    }

    /**
     * Whether the browser sent the Save-Data header (EF-05).
     *
     * @return bool
     */
    public static function browser_saves_data(): bool {
        if (get_config('theme_iqralight', 'savedata') === '0') {
            return false;
        }
        $header = $_SERVER['HTTP_SAVE_DATA'] ?? '';
        return strtolower(trim(clean_param($header, PARAM_ALPHA))) === 'on';
    }

    /**
     * The network class measured by the theme's script, if any.
     *
     * @return string 'veryslow', 'slow', 'fast' or '' when unknown.
     */
    public static function measured_network(): string {
        $value = clean_param($_COOKIE[self::COOKIE_NETWORK] ?? '', PARAM_ALPHA);
        return in_array($value, ['veryslow', 'slow', 'fast'], true) ? $value : '';
    }

    /**
     * CSS classes describing the mode, added to the body element.
     *
     * @return string[]
     */
    public static function body_classes(): array {
        $classes = ['iqra', 'iqra-mode-' . self::effective()];
        if (self::text_only()) {
            $classes[] = 'iqra-textonly';
        }
        if (self::display_option(self::PREF_CONTRAST)) {
            $classes[] = 'iqra-contrast';
        }
        if (self::display_option(self::PREF_EASYREAD)) {
            $classes[] = 'iqra-easyread';
        }
        return $classes;
    }

    /**
     * Whether a display option is switched on by the current learner.
     *
     * @param string $preference One of the PREF_CONTRAST or PREF_EASYREAD constants.
     * @return bool
     */
    public static function display_option(string $preference): bool {
        return isloggedin() && !isguestuser() && (bool) get_user_preferences($preference, false);
    }

    /**
     * Dark mode class for a page, or null when the page stays light.
     *
     * Only the page types listed in DARK_PAGES go dark, never in editing mode, and never
     * with the high contrast display, which is designed for a light page.
     *
     * @param \moodle_page $page
     * @return string|null 'iqra-dark-auto' (follows the device) or 'iqra-dark-on'.
     */
    public static function dark_class(\moodle_page $page): ?string {
        $setting = get_config('theme_iqralight', 'darkmode') ?: 'auto';
        if ($setting === 'off' || self::display_option(self::PREF_CONTRAST)) {
            return null;
        }
        if (isloggedin() && $page->user_is_editing()) {
            return null;
        }
        foreach (self::DARK_PAGES as $type) {
            if ($page->pagetype === $type || (substr($type, -1) === '-' && strpos($page->pagetype, $type) === 0)) {
                return $setting === 'on' ? 'iqra-dark-on' : 'iqra-dark-auto';
            }
        }
        return null;
    }

    /**
     * Data for the mode chip and its dialog.
     *
     * @return array
     */
    public static function export_for_template(): array {
        $requested = self::requested();
        $effective = self::effective();
        $choices = [];
        foreach (self::MODES as $mode) {
            $choices[] = [
                'value' => $mode,
                'label' => get_string('mode_' . $mode, 'theme_iqralight'),
                'help' => wording::get('mode_' . $mode . '_help'),
                'checked' => $mode === $requested,
            ];
        }
        return [
            'label' => get_string('modechip_' . $effective, 'theme_iqralight'),
            'economy' => $effective === 'economy',
            'choices' => $choices,
            'canchoose' => isloggedin() && !isguestuser(),
            'intro' => wording::get('modeintro'),
            'textonlyhelp' => wording::get('textonly_help'),
            'textonly' => (bool) (get_user_preferences(self::PREF_TEXTONLY, null) ?? (profiles::current() === 'lighter')),
            'contrast' => self::display_option(self::PREF_CONTRAST),
            'easyread' => self::display_option(self::PREF_EASYREAD),
            'hastextonly' => \core_component::get_component_directory('filter_iqralight') !== null,
        ];
    }
}
