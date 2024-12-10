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
 * Create a course and enroll users with external course and users data
 *
 * @package   local_webcourse
 * @category  local
 * @copyright 2024 Maxwell Souza (https://github.com/maxwell-hgr/moodle-local_webcourse/issues)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_webcourse\form;

use moodleform;

require_once($CFG->libdir . '/formslib.php');

class fetch_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'courseid', get_string('enter_course_id', 'local_webcourse'));
        $mform->setType('courseid', PARAM_INT);
        $mform->addRule('courseid', null, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('submit', 'local_webcourse'));
    }
}