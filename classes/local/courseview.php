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

use core\context\module as context_module;

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
                $key = strpos($file->get_mimetype(), 'video/') === 0 ? 'verb_watch' : 'verb_resource';
                return get_string($key, 'theme_iqralight', display_size($file->get_filesize()));
            }
        }
        return get_string(in_array($modname, self::VERBS, true) ? 'verb_' . $modname : 'verb_default', 'theme_iqralight');
    }

    /**
     * Main file of a resource, as Moodle chooses it (highest sort order).
     *
     * @param int $cmid
     * @return \stored_file|null
     */
    protected static function main_file(int $cmid): ?\stored_file {
        if (!array_key_exists($cmid, self::$files)) {
            $context = context_module::instance($cmid, IGNORE_MISSING);
            $files = $context ? get_file_storage()->get_area_files(
                $context->id,
                'mod_resource',
                'content',
                0,
                'sortorder DESC, id ASC',
                false
            ) : [];
            self::$files[$cmid] = $files ? reset($files) : null;
        }
        return self::$files[$cmid];
    }
}
