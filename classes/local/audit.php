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

use core\context;

/**
 * Lightness audit (RG-12.7): what weighs on learners' connections, and what to do about it.
 *
 * Everything is measured on the server from the files Moodle actually serves; nothing
 * is changed. Uninstalling a plugin or lightening a file stays the administrator's decision.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class audit {
    /** @var string[] File areas never listed: personal or technical files. */
    public const IGNORED_COMPONENTS = ['user', 'backup', 'core_backup', 'assignsubmission_file', 'assignfeedback_file',
        'question', 'qtype_essay', 'mod_forum', 'core_h5p', 'contentbank'];

    /**
     * JavaScript weight of each installed component, heaviest first.
     *
     * Moodle bundles the AMD modules of every installed plugin, used or not, into the
     * script each new visitor downloads; uninstalling unused plugins lightens it.
     *
     * @return array [rows, total raw bytes, total compressed bytes]
     */
    public static function javascript(): array {
        $bycomponent = [];
        $bundle = '';
        foreach (\core_requirejs::find_all_amd_modules(true) as $module => $path) {
            $content = is_readable($path) ? (string) file_get_contents($path) : '';
            $component = explode('/', $module)[0];
            $bycomponent[$component] = ($bycomponent[$component] ?? '') . $content;
            $bundle .= $content;
        }
        $rows = [];
        $manager = \core_plugin_manager::instance();
        foreach ($bycomponent as $component => $content) {
            $info = strpos($component, 'core') === 0 ? null : $manager->get_plugin_info($component);
            $rows[] = [
                'component' => $component,
                'raw' => strlen($content),
                'compressed' => strlen(gzencode($content, 6)),
                'standard' => $info === null || $info->is_standard(),
            ];
        }
        usort($rows, fn($a, $b) => $b['compressed'] <=> $a['compressed']);
        return [$rows, strlen($bundle), strlen(gzencode($bundle, 6))];
    }

    /**
     * Estimated weight of a learner's first visit, compressed, part by part.
     *
     * @param string $css Compiled stylesheet of the theme.
     * @return array[] Each with part and bytes.
     */
    public static function first_visit(string $css): array {
        global $CFG;
        [, , $amd] = self::javascript();
        $other = '';
        foreach (
            array_merge(
                glob($CFG->dirroot . '/lib/requirejs/require.min.js') ?: [],
                glob($CFG->dirroot . '/lib/jquery/jquery-*.min.js') ?: [],
                glob($CFG->dirroot . '/lib/polyfills/*.js') ?: []
            ) as $file
        ) {
            $other .= (string) file_get_contents($file);
        }
        $fonts = 0;
        foreach (['iqra-fa-solid-900.woff2', 'iqra-fa-regular-400.woff2'] as $font) {
            $path = __DIR__ . '/../../fonts/' . $font;
            $fonts += is_readable($path) ? (int) filesize($path) : 0;
        }
        return [
            ['part' => 'css', 'bytes' => strlen(gzencode($css, 6))],
            ['part' => 'fonts', 'bytes' => $fonts],
            ['part' => 'amd', 'bytes' => $amd],
            ['part' => 'otherjs', 'bytes' => strlen(gzencode($other, 6))],
        ];
    }

    /**
     * Icons of installed plugins missing from the light icon fonts.
     *
     * Each one makes browsers download the full Font Awesome font on the pages where it appears.
     *
     * @param string $css Compiled stylesheet of the theme.
     * @return string[] Icon keys, such as "mod_example:icon".
     */
    public static function uncovered_icons(string $css): array {
        $bycode = [];
        if (preg_match_all('~\.(fa-[a-z0-9-]+)\{--fa:"(.)"~u', $css, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $bycode[$match[1]] = \core_text::utf8ord($match[2]);
            }
        }
        $covered = self::covered_codepoints();
        $missing = [];
        $map = \core\output\icon_system::instance(\core\output\icon_system::FONTAWESOME)->get_icon_name_map();
        foreach ($map as $key => $classes) {
            foreach (preg_split('/\s+/', $classes) as $class) {
                if (isset($bycode[$class]) && !isset($covered[$bycode[$class]])) {
                    $missing[] = $key;
                    break;
                }
            }
        }
        sort($missing);
        return $missing;
    }

    /**
     * Codepoints covered by the light icon fonts, read from their unicode-range.
     *
     * @return array Codepoints as keys.
     */
    protected static function covered_codepoints(): array {
        $scss = (string) file_get_contents(__DIR__ . '/../../scss/iqra-icons.scss');
        $covered = [];
        if (preg_match_all('~U\+([0-9A-F]+)(?:-([0-9A-F]+))?~i', $scss, $ranges, PREG_SET_ORDER)) {
            foreach ($ranges as $range) {
                $start = hexdec($range[1]);
                $end = isset($range[2]) && $range[2] !== '' ? hexdec($range[2]) : $start;
                for ($code = $start; $code <= $end; $code++) {
                    $covered[$code] = true;
                }
            }
        }
        return $covered;
    }

    /**
     * Heaviest course files, of the site or of one course, excluding personal and technical files.
     *
     * @param int $limit
     * @param int|null $courseid Restrict to this course.
     * @return array[] Each with filename, bytes, courseid, coursename and activity (name or empty).
     */
    public static function heavy_files(int $limit = 20, ?int $courseid = null): array {
        global $DB;
        [$notin, $params] = $DB->get_in_or_equal(self::IGNORED_COMPONENTS, SQL_PARAMS_NAMED, 'c', false);
        $where = '';
        if ($courseid !== null) {
            $coursecontext = \core\context\course::instance($courseid);
            $where = 'AND (ctx.id = :coursectx OR ' . $DB->sql_like('ctx.path', ':coursepath') . ')';
            $params += ['coursectx' => $coursecontext->id, 'coursepath' => $coursecontext->path . '/%'];
        }
        $records = $DB->get_records_sql(
            "SELECT f.id, f.filename, f.filesize, f.contextid
               FROM {files} f
               JOIN {context} ctx ON ctx.id = f.contextid
              WHERE f.filename <> '.' AND f.filearea <> 'draft' AND f.component {$notin} AND f.filesize > :min {$where}
           ORDER BY f.filesize DESC",
            $params + ['min' => 102400],
            0,
            $limit * 3
        );
        $rows = [];
        foreach ($records as $record) {
            $context = context::instance_by_id($record->contextid, IGNORE_MISSING);
            $coursecontext = $context ? $context->get_course_context(false) : false;
            if (!$coursecontext || $coursecontext->instanceid == SITEID) {
                continue;
            }
            $course = get_course($coursecontext->instanceid);
            $activity = '';
            if ($context instanceof \core\context\module) {
                $cm = get_fast_modinfo($course)->get_cm($context->instanceid);
                $activity = $cm ? $cm->get_formatted_name() : '';
            }
            $rows[] = [
                'filename' => $record->filename,
                'bytes' => (int) $record->filesize,
                'courseid' => (int) $course->id,
                'coursename' => format_string($course->fullname, true, ['context' => $coursecontext]),
                'activity' => $activity,
            ];
            if (count($rows) >= $limit) {
                break;
            }
        }
        return $rows;
    }

    /**
     * Settings that matter for lightness, with their state.
     *
     * @return array[] Each with key and ok.
     */
    public static function checks(): array {
        global $CFG;
        require_once($CFG->libdir . '/filterlib.php');
        $filters = filter_get_global_states();
        $order = array_keys(array_filter($filters, fn($filter) => (int) $filter->active === TEXTFILTER_ON));
        $filteron = isset($filters['iqralight']) && (int) $filters['iqralight']->active === TEXTFILTER_ON;
        $mediaposition = array_search('mediaplugin', $order, true);
        return [
            ['key' => 'filter', 'ok' => $filteron],
            ['key' => 'filterorder', 'ok' => $filteron &&
                ($mediaposition === false || array_search('iqralight', $order, true) > $mediaposition)],
            ['key' => 'serviceworker', 'ok' => get_config('theme_iqralight', 'serviceworker') !== '0'],
            ['key' => 'cachejs', 'ok' => !empty($CFG->cachejs)],
            ['key' => 'slasharguments', 'ok' => !empty($CFG->slasharguments)],
            ['key' => 'designermode', 'ok' => empty($CFG->themedesignermode)],
            ['key' => 'completion', 'ok' => !empty($CFG->enablecompletion)],
            ['key' => 'passwordtoggle', 'ok' => (int) get_config('core', 'loginpasswordtoggle') !== TOGGLE_SENSITIVE_DISABLED],
        ];
    }

    /**
     * Dry run of the "tap to load" filter on a course: what would wait for a tap.
     *
     * @param int $courseid
     * @return array[] Each with activity, kind and bytes (null when unknown).
     */
    public static function dry_run(int $courseid): array {
        global $DB;
        if (!class_exists('\filter_iqralight\text_filter')) {
            return [];
        }
        $found = [];
        $course = get_course($courseid);
        foreach (get_fast_modinfo($course)->get_cms() as $cm) {
            $texts = [];
            $instance = $DB->get_record($cm->modname, ['id' => $cm->instance]);
            if (!$instance) {
                continue;
            }
            if (!empty($instance->intro)) {
                $texts[] = file_rewrite_pluginfile_urls(
                    $instance->intro,
                    'pluginfile.php',
                    $cm->context->id,
                    'mod_' . $cm->modname,
                    'intro',
                    null
                );
            }
            if ($cm->modname === 'page' && !empty($instance->content)) {
                $texts[] = file_rewrite_pluginfile_urls(
                    $instance->content,
                    'pluginfile.php',
                    $cm->context->id,
                    'mod_page',
                    'content',
                    $instance->revision
                );
            }
            $filter = new \filter_iqralight\text_filter($cm->context, []);
            foreach ($texts as $text) {
                $output = $filter->filter($text);
                preg_match_all('~data-filter-iqra="([a-z]+)"(?: data-iqra-bytes="(\d+)")?~', $output, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $found[] = [
                        'activity' => $cm->get_formatted_name(),
                        'kind' => $match[1],
                        'bytes' => isset($match[2]) && $match[2] !== '' ? (int) $match[2] : null,
                    ];
                }
            }
        }
        return $found;
    }

    /**
     * Blocks shown to learners (RG-12.4): every block adds markup, and often scripts, to each page.
     *
     * Counts the block instances on dashboards, course pages and activity pages, with the
     * compressed script weight of each block type.
     *
     * @param array $javascript Rows returned by javascript().
     * @return array[] Each with block, instances and script bytes, most frequent first.
     */
    public static function blocks(array $javascript): array {
        global $DB;
        $weights = array_column($javascript, 'compressed', 'component');
        $records = $DB->get_records_sql(
            "SELECT bi.blockname, COUNT(1) AS instances
               FROM {block_instances} bi
               JOIN {context} ctx ON ctx.id = bi.parentcontextid
              WHERE ctx.contextlevel IN (:user, :course, :module)
           GROUP BY bi.blockname
           ORDER BY COUNT(1) DESC",
            ['user' => CONTEXT_USER, 'course' => CONTEXT_COURSE, 'module' => CONTEXT_MODULE]
        );
        $rows = [];
        foreach ($records as $record) {
            $rows[] = [
                'block' => $record->blockname,
                'name' => get_string_manager()->string_exists('pluginname', 'block_' . $record->blockname)
                    ? get_string('pluginname', 'block_' . $record->blockname) : $record->blockname,
                'instances' => (int) $record->instances,
                'script' => (int) ($weights['block_' . $record->blockname] ?? 0),
            ];
        }
        return $rows;
    }
}
