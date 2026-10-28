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

namespace theme_iqralight\output;

use theme_iqralight\local\mode;
use theme_iqralight\local\navigation;
use theme_iqralight\local\resume;
use theme_iqralight\local\stats;

/**
 * Core renderer for theme_iqralight.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
    /**
     * Adds the connection mode classes to the body element.
     *
     * @param string|array $additionalclasses
     * @return string
     */
    public function body_attributes($additionalclasses = []) {
        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }
        $classes = array_merge($additionalclasses, mode::body_classes());
        if (navigation::is_visible($this->page)) {
            $classes[] = 'iqra-has-bottomnav';
            if ($this->page->pagetype === 'my-index') {
                $classes[] = 'iqra-home';
            }
        }
        $dark = mode::dark_class($this->page);
        if ($dark !== null) {
            $classes[] = $dark;
        }
        return parent::body_attributes($classes);
    }

    /**
     * The logo of the top bar: the compact logo, or else the main logo, so that a site that
     * only uploaded its main logo (as the setup wizard does) still shows it.
     *
     * @param int $maxwidth
     * @param int $maxheight
     * @return \moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        return parent::get_compact_logo_url($maxwidth, $maxheight) ?: $this->get_logo_url(null, 100);
    }

    /**
     * Whether the top bar shows the logo.
     *
     * @return bool
     */
    public function should_display_navbar_logo() {
        return !empty($this->get_compact_logo_url());
    }

    /**
     * Adds the connection mode chip to the top bar, where Moodle places plugin items.
     *
     * Iqralight inherits Boost's layouts unchanged, for every Moodle version; it only adds its
     * own elements through the renderer.
     *
     * @return string
     */
    public function navbar_plugin_output() {
        $output = parent::navbar_plugin_output();
        if (navigation::is_visible($this->page)) {
            $output .= $this->render_from_template('theme_iqralight/modechip', mode::export_for_template());
        }
        return $output;
    }

    /**
     * On the learner's home page, adds the greeting and the "Resume" card above the content.
     *
     * @return string
     */
    public function main_content() {
        global $USER;
        $output = parent::main_content();
        if ($this->page->pagetype !== 'my-index' || !navigation::is_visible($this->page)) {
            return $output;
        }
        $home = $this->render_from_template('theme_iqralight/home', [
            'greeting' => get_string('greeting', 'theme_iqralight', format_string($USER->firstname)),
            'date' => userdate(time(), get_string('strftimedaydate', 'langconfig')),
            'saved' => stats::learner_sentence((int) $USER->id),
            'resume' => resume::for_user((int) $USER->id),
        ]);
        return $home . $output;
    }

    /**
     * Adds the learner's bottom navigation bar and loads the theme's script.
     *
     * @return string
     */
    public function standard_end_of_body_html() {
        $this->iqra_init_js();
        $output = '';
        if (navigation::is_visible($this->page)) {
            $output .= $this->render_from_template('theme_iqralight/bottomnav', navigation::export_for_template($this->page));
        }
        return $output . parent::standard_end_of_body_html();
    }

    /**
     * Loads the theme's script with the resolved mode; the script is a standard AMD module.
     */
    protected function iqra_init_js(): void {
        $this->page->requires->js_call_amd('theme_iqralight/iqra', 'init', [[
            'requested' => mode::requested(),
            'effective' => mode::effective(),
            'savedata' => mode::browser_saves_data(),
            'textonly' => mode::text_only(),
            'learnerkey' => $this->iqra_learner_key(),
            'clearoffline' => !isloggedin() && get_config('theme_iqralight', 'offlineclearonsessionend') !== '0',
            'offlinesubmit' => preg_match('/^(mod-quiz-|mod-assign-|login-)/', $this->page->pagetype) ? null :
                \theme_iqralight\local\wording::get('offlinesubmit'),
            'listenstrings' => strpos($this->page->pagetype, 'mod-quiz-attempt') === 0 ? null : [
                'listen' => get_string('listen', 'theme_iqralight'),
                'stop' => get_string('stoplistening', 'theme_iqralight'),
            ],
            'serviceworker' => $this->iqra_service_worker(),
            'stats' => \theme_iqralight\local\stats::enabled() && isloggedin() && !isguestuser()
                ? (new \moodle_url('/theme/iqralight/stats.php'))->out(false) : null,
            'canchoose' => isloggedin() && !isguestuser(),
            'labels' => [
                'economy' => get_string('modechip_economy', 'theme_iqralight'),
                'full' => get_string('modechip_full', 'theme_iqralight'),
            ],
        ]]);
        $this->iqra_offline_reading();
        if (mode::display_option(mode::PREF_EASYREAD) && strpos($this->page->pagetype, 'mod-quiz-attempt') !== 0) {
            $this->page->requires->js_call_amd('theme_iqralight/easyread', 'init', [[
                'listen' => get_string('listen', 'theme_iqralight'),
                'stop' => get_string('stoplistening', 'theme_iqralight'),
            ]]);
        }
    }

    /**
     * Where the service worker lives, or null when the administrator disabled it.
     *
     * @return array|null
     */
    protected function iqra_service_worker(): ?array {
        global $CFG;
        if (get_config('theme_iqralight', 'serviceworker') === '0') {
            return null;
        }
        return [
            'url' => (new \moodle_url('/theme/iqralight/sw.php'))->out(false),
            'scope' => (string) parse_url($CFG->wwwroot, PHP_URL_PATH) . '/',
        ];
    }

    /**
     * Large "Next" button at the bottom of each activity, for learners (RG-6.1 to RG-6.4).
     *
     * Moodle hides its previous and next links when the theme uses the course index.
     * Learners still need them to move on without going back to the course page, so
     * Iqralight renders its own; in editing mode, Moodle's behaviour is kept.
     *
     * @return string
     */
    public function activity_navigation() {
        $page = $this->page;
        if (
            !\theme_iqralight\local\navigation::is_visible($page)
            || $page->pagelayout !== 'incourse'
            || $page->context->contextlevel != CONTEXT_MODULE
            || !$page->cm
            || $page->cm->is_stealth()
        ) {
            return parent::activity_navigation();
        }

        $modules = [];
        foreach (get_fast_modinfo($page->course)->get_cms() as $module) {
            if ($module->uservisible && !$module->is_stealth() && !empty($module->url) && $module->is_of_type_that_can_display()) {
                $modules[] = $module;
            }
        }
        $ids = array_map(fn($module) => (int) $module->id, $modules);
        $position = array_search((int) $page->cm->id, $ids, true);
        if ($position === false) {
            return parent::activity_navigation();
        }
        $next = $modules[$position + 1] ?? null;
        $previous = $modules[$position - 1] ?? null;

        return $this->render_from_template('theme_iqralight/activitynav', [
            'next' => $next ? [
                'url' => (new \moodle_url($next->url, ['forceview' => 1]))->out(false),
                'label' => get_string('nextactivity', 'theme_iqralight', $next->get_formatted_name()),
            ] : null,
            'previous' => $previous ? [
                'url' => (new \moodle_url($previous->url, ['forceview' => 1]))->out(false),
                'label' => get_string('previousactivity', 'theme_iqralight'),
            ] : null,
            'courseurl' => (new \moodle_url('/course/view.php', ['id' => $page->course->id]))->out(false),
        ]);
    }

    /**
     * In data saver mode, profile pictures become initials on a coloured disc (RG-9.1).
     *
     * Moodle builds the link, size and accessible text as usual; only the image is
     * replaced, so nothing is downloaded.
     *
     * @param \user_picture $userpicture
     * @return string
     */
    protected function render_user_picture(\user_picture $userpicture) {
        $output = parent::render_user_picture($userpicture);
        if (mode::effective() !== 'economy' || strpos($output, '<img') === false) {
            return $output;
        }
        $user = $userpicture->user;
        $initials = \core_text::strtoupper(\core_text::substr((string) ($user->firstname ?? ''), 0, 1) .
            \core_text::substr((string) ($user->lastname ?? ''), 0, 1));
        $size = $userpicture->size === true || (int) $userpicture->size === 1 ? 100 : (int) ($userpicture->size ?: 35);
        $hue = crc32((string) ($user->id ?? $initials)) % 360;
        return preg_replace_callback('~<img\b[^>]*>~i', function ($match) use ($initials, $size, $hue) {
            preg_match('~\salt="([^"]*)"~i', $match[0], $alt);
            $attributes = [
                'class' => 'iqra-initials',
                'style' => "width: {$size}px; height: {$size}px; font-size: " . max(10, (int) round($size * 0.4)) . "px; " .
                    "background-color: hsl({$hue}, 45%, 35%);",
            ];
            if (!empty($alt[1])) {
                $attributes['role'] = 'img';
                $attributes['aria-label'] = html_entity_decode($alt[1], ENT_QUOTES, 'UTF-8');
            } else {
                $attributes['aria-hidden'] = 'true';
            }
            return \html_writer::span(s($initials), '', $attributes);
        }, $output, 1);
    }

    /**
     * A short key that names this learner's offline cache on the device, without revealing who they are.
     *
     * @return string|null
     */
    protected function iqra_learner_key(): ?string {
        global $USER, $CFG;
        if (!isloggedin() || isguestuser()) {
            return null;
        }
        return substr(sha1($USER->id . '/' . $CFG->siteidentifier), 0, 16);
    }

    /**
     * Loads the "Keep offline" button on pages, book chapters and PDFs shown in Moodle (EF-69).
     */
    protected function iqra_offline_reading(): void {
        $page = $this->page;
        if (
            !get_config('theme_iqralight', 'offlinereading') || get_config('theme_iqralight', 'serviceworker') === '0'
                || !\theme_iqralight\local\navigation::is_visible($page)
                || !in_array($page->pagetype, ['mod-page-view', 'mod-book-view', 'mod-resource-view'], true)
        ) {
            return;
        }
        $quota = (int) (get_config('theme_iqralight', 'offlinequota') ?: 50);
        $this->page->requires->js_call_amd('theme_iqralight/offline', 'init', [[
            'key' => $this->iqra_learner_key(),
            'quota' => $quota * 1048576,
            'root' => rtrim((new \moodle_url('/'))->out(false), '/'),
            'title' => $page->cm ? $page->cm->get_formatted_name() : format_string($page->title),
            'course' => format_string($page->course->fullname, true, ['context' => $page->context->get_course_context()]),
            'strings' => [
                'keep' => get_string('keepoffline', 'theme_iqralight'),
                'remove' => get_string('removeoffline', 'theme_iqralight'),
                'kept' => get_string('offlineavailable', 'theme_iqralight'),
                'saving' => get_string('offlinesaving', 'theme_iqralight'),
                'full' => \theme_iqralight\local\wording::get('offlinefull', $quota),
                'failed' => \theme_iqralight\local\wording::get('offlinefailed'),
            ],
        ]]);
    }
}
