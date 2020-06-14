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
 * Defines the view event.
 *
 * @package    mod_sharedpanel
 * @copyright  2014 Daniel Neis Araujo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_sharedpanel\event;
use core\event\base;

defined('MOODLE_INTERNAL') || die();

class card_created_twitter extends base {

    protected function init() {
        $this->data['objecttable'] = 'sharedpanel';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * @return string
     * @throws \coding_exception
     */
    public static function get_name() {
        return get_string('event_create_twitter', 'mod_sharedpanel');
    }

    public function get_description() {
        return "The user with id $this->userid created card with Twitter $this->objectid .";
        //return "The user with id '28' created card with Twitter $this->objectid .";

    }

    protected function validate_data() {
        parent::validate_data();
    }
}
