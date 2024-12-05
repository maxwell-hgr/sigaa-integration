<?php

namespace local_sigaa\form;

use moodleform;

require_once($CFG->libdir . '/formslib.php');

class fetch_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'courseid', get_string('enter_course_id', 'local_sigaa'));
        $mform->setType('courseid', PARAM_INT);
        $mform->addRule('courseid', null, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('submit', 'local_sigaa'));
    }
}