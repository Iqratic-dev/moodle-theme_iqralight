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

/**
 * Renderer of the weeks course format in Iqralight: adds the learner labels of the course page.
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_weeks_renderer extends \format_weeks\output\renderer {
    /**
     * Renders a template after adding Iqralight's labels to the course content data.
     *
     * @param string $templatename
     * @param mixed $context
     * @return string
     */
    public function render_from_template($templatename, $context) {
        $context = \theme_iqralight\local\courseview::enrich($templatename, $context, $this->page);
        return parent::render_from_template($templatename, $context);
    }
}
