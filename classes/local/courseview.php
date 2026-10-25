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
 * Course page for learners (F4): an action verb and the size of each activity,
 * and "Chapter N" above each section title.
 *
 * Works on the data Moodle exports for the course content, before it is rendered,
 * so that the course format and its templates keep working as they are.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courseview {
    /** @var string[] Activity types that have their own verb; others get "Open". */
    public const VERBS = ['page', 'book', 'imscp', 'resource', 'url', 'folder', 'quiz', 'assign', 'forum', 'lesson',
        'h5pactivity', 'glossary', 'choice', 'feedback', 'scorm', 'bigbluebuttonbn', 'workshop', 'data', 'wiki'];

    /** @var array Main files of resources, by course module id. */
    protected static $files = [];

    /**
     * Adds Iqralight's labels to the course content data, for learners only.
     *
     * @param string $template
     * @param mixed $context
     * @param \moodle_page $page
     * @return mixed
     */
    public static function enrich(string $template, $context, \moodle_page $page) {
        if (strpos($template, 'core_courseformat/local/content') !== 0 || !navigation::is_visible($page)) {
            return $context;
        }
        self::walk($context);
        return $context;
    }

    /**
     * Walks the exported data in place.
     *
     * @param mixed $node
     */
    protected static function walk(&$node): void {
        if (is_array($node)) {
            self::label($node);
            foreach ($node as &$child) {
                self::walk($child);
            }
        } else if ($node instanceof \stdClass) {
            $array = (array) $node;
            self::label($array);
            foreach ($array as $key => $child) {
                self::walk($child);
                $array[$key] = $child;
            }
            foreach ($array as $key => $value) {
                $node->$key = $value;
            }
        }
    }

    /**
     * Adds the labels to one activity name or one section header.
     *
     * @param array $data
     */
    protected static function label(array &$data): void {
        if (isset($data['modname'], $data['activityname']) && ($data['url'] ?? null) instanceof \moodle_url) {
            $data['iqraverb'] = self::verb((string) $data['modname'], (int) $data['url']->param('id'));
        }
        if (array_key_exists('headerdisplaymultipage', $data) && !empty($data['num'])) {
            $data['iqrachapter'] = get_string('chapter', 'theme_iqralight', (int) $data['num']);
        }
    }

    /**
     * The action verb of an activity, with its size when it is a file.
     *
     * @param string $modname
     * @param int $cmid
     * @return string
     */
    public static function verb(string $modname, int $cmid): string {
        if ($modname === 'resource' && $cmid) {
            $file = self::main_file($cmid);
            if ($file) {
                $key = strpos($file->mimetype, 'video/') === 0 ? 'verb_watch' : 'verb_resource';
                return get_string($key, 'theme_iqralight', display_size((int) $file->filesize));
            }
        }
        return get_string(in_array($modname, self::VERBS, true) ? 'verb_' . $modname : 'verb_default', 'theme_iqralight');
    }

    /**
     * Main file of a resource, as Moodle chooses it (highest sort order).
     *
     * The files of every resource of the course are read in one query, the first
     * time one of them is needed.
     *
     * @param int $cmid
     * @return \stdClass|null With filesize and mimetype.
     */
    protected static function main_file(int $cmid): ?\stdClass {
        global $DB;
        if (!array_key_exists($cmid, self::$files)) {
            $courseid = (int) $DB->get_field('course_modules', 'course', ['id' => $cmid]);
            self::$files[$cmid] = null;
            $records = $DB->get_recordset_sql(
                "SELECT f.id, cm.id AS cmid, f.filesize, f.mimetype
                   FROM {files} f
                   JOIN {context} ctx ON ctx.id = f.contextid AND ctx.contextlevel = :level
                   JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  WHERE cm.course = :course AND f.component = 'mod_resource' AND f.filearea = 'content'
                        AND f.filename <> '.'
               ORDER BY f.sortorder DESC, f.id ASC",
                ['level' => CONTEXT_MODULE, 'course' => $courseid]
            );
            foreach ($records as $record) {
                if (!isset(self::$files[$record->cmid])) {
                    self::$files[$record->cmid] = (object) ['filesize' => $record->filesize, 'mimetype' => $record->mimetype];
                }
            }
            $records->close();
        }
        return self::$files[$cmid];
    }
}
