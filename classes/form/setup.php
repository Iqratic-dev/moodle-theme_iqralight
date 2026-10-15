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

namespace theme_iqralight\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use theme_iqralight\local\contrast;
use theme_iqralight\local\presets;

/**
 * One step of the first-time setup wizard (RG-12.8).
 *
 * @package    theme_iqralight
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setup extends \moodleform {
    /** @var string[] Accessible colours offered in step 1, all at least 4.5:1 with white text. */
    public const COLOURS = ['#0B6E5A', '#1F4E99', '#8A3B12', '#6A2C82', '#A1263A', '#3D4A5C'];

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;
        $step = (int) $this->_customdata['step'];
        $mform->addElement('hidden', 'step', $step);
        $mform->setType('step', PARAM_INT);
        $mform->addElement('html', \html_writer::tag(
            'p',
            get_string('setup_step', 'theme_iqralight', (object) ['step' => $step, 'total' => 4]),
            ['class' => 'text-muted']
        ));

        switch ($step) {
            case 1:
                $mform->addElement('header', 'colourhdr', get_string('setup_colour', 'theme_iqralight'));
                $mform->addElement('static', 'colourintro', '', get_string('setup_colour_intro', 'theme_iqralight'));
                $swatches = [];
                foreach (self::COLOURS as $colour) {
                    $label = \html_writer::span('', 'iqra-swatch', ['style' => "background-color: {$colour}"]) . ' ' . $colour;
                    $swatches[] = $mform->createElement('radio', 'brandcolor', '', $label, $colour);
                }
                $other = get_string('setup_colour_other', 'theme_iqralight');
                $swatches[] = $mform->createElement('radio', 'brandcolor', '', $other, 'other');
                $mform->addGroup($swatches, 'swatches', get_string('brandcolor', 'theme_iqralight'), '<br>', false);
                $mform->addElement('text', 'customcolour', get_string('setup_colour_custom', 'theme_iqralight'), ['size' => 8]);
                $mform->setType('customcolour', PARAM_TEXT);
                $mform->hideIf('customcolour', 'brandcolor', 'neq', 'other');
                break;

            case 2:
                $mform->addElement('header', 'logohdr', get_string('setup_logo', 'theme_iqralight'));
                $mform->addElement('static', 'logointro', '', get_string('setup_logo_intro', 'theme_iqralight'));
                $mform->addElement(
                    'filemanager',
                    'logo',
                    get_string('logos', 'theme_iqralight'),
                    null,
                    ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg'], 'subdirs' => 0,
                    'maxbytes' => presets::MAX_LOGO]
                );
                break;

            case 3:
                $mform->addElement('header', 'profilehdr', get_string('setup_profile', 'theme_iqralight'));
                $mform->addElement('static', 'profileintro', '', get_string('setup_profile_intro', 'theme_iqralight'));
                $choices = [];
                foreach (presets::SHIPPED as $name) {
                    $label = \html_writer::tag('strong', get_string('preset_' . $name, 'theme_iqralight')) . ' : ' .
                        get_string('preset_' . $name . '_desc', 'theme_iqralight');
                    $choices[] = $mform->createElement('radio', 'profile', '', $label, $name);
                }
                $choices[] = $mform->createElement('radio', 'profile', '', get_string('setup_profile_keep', 'theme_iqralight'), '');
                $mform->addGroup($choices, 'profiles', '', '<br>', false);
                $mform->setDefault('profile', '');
                break;

            default:
                $mform->addElement('header', 'nativehdr', get_string('setup_native', 'theme_iqralight'));
                $mform->addElement('static', 'nativeintro', '', get_string('setup_native_intro', 'theme_iqralight'));
                foreach ($this->_customdata['native'] as $name => $active) {
                    $mform->addElement(
                        'advcheckbox',
                        $name,
                        get_string('setup_' . $name, 'theme_iqralight'),
                        get_string($active ? 'setup_alreadyactive' : 'setup_' . $name . '_desc', 'theme_iqralight')
                    );
                    if ($active) {
                        $mform->setDefault($name, 1);
                        $mform->freeze($name);
                    }
                }
        }

        $this->add_action_buttons(true, get_string($step < 4 ? 'setup_next' : 'setup_finish', 'theme_iqralight'));
    }

    /**
     * Validation of the colour step: same contrast rule as the settings page.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ((int) $data['step'] === 1) {
            $choice = $data['brandcolor'] ?? '';
            $colour = $choice === 'other' ? trim((string) ($data['customcolour'] ?? '')) : $choice;
            if (!preg_match('/^#[0-9a-fA-F]{6}$/', $colour)) {
                $errors['customcolour'] = get_string('brandcolor_format', 'theme_iqralight');
            } else if (contrast::contrast_with_white($colour) < contrast::MIN_CONTRAST) {
                $errors['customcolour'] = get_string('brandcolor_contrast', 'theme_iqralight', (object) [
                    'ratio' => format_float(contrast::contrast_with_white($colour), 1),
                    'suggestion' => contrast::darken_until_readable($colour),
                ]);
            }
        }
        return $errors;
    }
}
