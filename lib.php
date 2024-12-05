<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/enrol/manual/locallib.php');

defined('MOODLE_INTERNAL') || die();

function create_course_custom($fullname, $shortname, $categoryid, $participants = [], $summary = '', $format = 'topics') {
    global $DB;

    $category = \core_course_category::get($categoryid, IGNORE_MISSING);
    if (!$category) {
        throw new moodle_exception('invalidcategoryid', 'error');
    }

    $course = new stdClass();
    $course->fullname = $fullname;
    $course->shortname = $shortname;
    $course->summary = $summary;
    $course->category = $categoryid;
    $course->format = $format;
    $course->visible = 1;

    $newcourse = create_course($course);

    $manualenrol = enrol_get_plugin('manual');

    $instance = enrol_get_instances($newcourse->id, true);

    $manualinstance = null;
    foreach ($instance as $inst) {
        if ($inst->enrol === 'manual') {
            $manualinstance = $inst;
            break;
        }
    }

    if (!$manualinstance) {
        throw new moodle_exception('noenrolmentplugin', 'error');
    }

    if (!empty($participants)) {
        foreach ($participants as $username) {
            echo '<script>console.log("Username: ' . addslashes($username) . '");</script>';

            $user = $DB->get_record('user', array('username' => $username));

            if ($user) {
                $manualenrol->enrol_user($manualinstance, $user->id, 5); // 5 -> student role
                echo '<script>console.log("Usuário ' . addslashes($username) . ' inscrito com sucesso.");</script>';
            } else {
                echo '<script>console.log("Usuário com username ' . addslashes($username) . ' não encontrado.");</script>';
            }
        }
    } else {
        echo '<script>console.log("participants is empty");</script>';
    }

    return $newcourse;
}
