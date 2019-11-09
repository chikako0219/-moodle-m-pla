<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The main sharedpanel configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_sharedpanel
 * @copyright  2016 NAGAOKA Chikako, KITA Toshihiro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once(__DIR__ . "/lib/Facebook/autoload.php");

/**
 * Module instance settings form
 */
class mod_sharedpanel_mod_form extends moodleform_mod
{

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $DB, $OUTPUT;

        $mform = $this->_form;
        $config = get_config('sharedpanel');

        $instanceid = $this->get_instance();
        if ($instanceid) {
            $instance = $DB->get_record('sharedpanel', ['id' => $instanceid], '*', MUST_EXIST);
        } else {
            $instance = null;
        }

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('sharedpanelname', 'sharedpanel'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'sharedpanelname', 'sharedpanel');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Display Style.
        $mform->addElement('header', 'displaystyleform', 'Display Style');
        $mform->setExpanded('displaystyleform');

        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'display_style', '', 'style1', 0, '');
        $radioarray[] = $mform->createElement('radio', 'display_style', '', 'style2', 1, '');
        $radioarray[] = $mform->createElement('radio', 'display_style', '', 'style3', 2, '');
        $mform->addGroup($radioarray, 'display_style', '', array(' '), false);
	$mform->setType('display_style', PARAM_INT); 

        // require mod_forms from diretory "mod_form".
		require 'mod_form/modform_twitter.php';
		require 'mod_form/modform_email.php';
		require 'mod_form/modform_facebook.php';
		require 'mod_form/modform_evernote.php';
		require 'mod_form/modform_line.php';

     	// Forms for new external services
		require 'mod_form/modform_pinterest.php';

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
	}
}
