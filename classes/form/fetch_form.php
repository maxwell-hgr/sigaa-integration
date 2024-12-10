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
 * Form config for local_webcourse.
 *
 * @package   local_webcourse
 * @copyright 2024 Maxwell Souza <maxwell.hygor01@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

namespace local_webcourse\form;
use moodleform;
require_once($CFG->libdir . '/formslib.php');


/**
 * Form for fetching a course based on the course ID.
 *
 * This class defines a custom form to capture a course ID. It extends the `moodleform` class from Moodle
 * and includes the creation of a text input field for the course ID with validation to ensure the field is filled.
 *
 * @package    local_webcourse
 * @subpackage form
 */
class fetch_form extends moodleform {

    /**
     * Defines the form elements.
     *
     * This function defines the elements and rules of the form. It adds a text input field for the course ID,
     * sets the field type to integer, and adds a validation rule to ensure the field is required.
     * It also adds action buttons to the form.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'courseid', get_string('enter_course_id', 'local_webcourse'));
        $mform->setType('courseid', PARAM_INT);
        $mform->addRule('courseid', null, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('submit', 'local_webcourse'));
    }
}
