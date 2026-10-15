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
 * Category profiles (RG-12.5): parts of the site made lighter than the rest.
 *
 * A profile can only lighten: "light" makes data saver the default mode in the
 * category and its subcategories, "lighter" also shows text only by default. A
 * learner's own choice always comes first.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profiles {
    /** @var string[] Profile levels, from the site settings to the lightest. */
    public const LEVELS = ['site', 'light', 'lighter'];

    /**
     * Profiles set by the administrator, by category id.
     *
     * @return array
     */
    public static function all(): array {
        $profiles = json_decode((string) get_config('theme_iqralight', 'categoryprofiles'), true);
        if (!is_array($profiles)) {
            return [];
        }
        return array_filter($profiles, fn($level) => in_array($level, self::LEVELS, true) && $level !== 'site');
    }

    /**
     * Saves the profiles, keeping only valid categories and levels other than "site".
     *
     * @param array $profiles Level by category id.
     */
    public static function save(array $profiles): void {
        $clean = [];
        foreach ($profiles as $categoryid => $level) {
            if (
                (int) $categoryid > 0 && in_array($level, self::LEVELS, true) && $level !== 'site'
                    && \core_course_category::get((int) $categoryid, IGNORE_MISSING, true)
            ) {
                $clean[(int) $categoryid] = $level;
            }
        }
        set_config('categoryprofiles', $clean ? json_encode($clean) : '', 'theme_iqralight');
    }

    /**
     * Profile applying to a category: its own, or the nearest parent's.
     *
     * @param int $categoryid
     * @return string One of LEVELS.
     */
    public static function for_category(int $categoryid): string {
        $profiles = self::all();
        if (!$profiles || !$categoryid) {
            return 'site';
        }
        $category = \core_course_category::get($categoryid, IGNORE_MISSING, true);
        if (!$category) {
            return 'site';
        }
        foreach (array_reverse(array_filter(explode('/', $category->path))) as $id) {
            if (isset($profiles[(int) $id])) {
                return $profiles[(int) $id];
            }
        }
        return 'site';
    }

    /**
     * Profile applying to the course shown on the current page.
     *
     * @return string One of LEVELS.
     */
    public static function current(): string {
        global $PAGE;
        $course = $PAGE->course ?? null;
        if (empty($course->id) || $course->id == SITEID || empty($course->category)) {
            return 'site';
        }
        return self::for_category((int) $course->category);
    }
}
