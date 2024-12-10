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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/enrol/manual/locallib.php');

defined('MOODLE_INTERNAL') || die();

function local_webcourse_create_course($fullname, $shortname, $categoryid, $participants = [], $summary = '', $format = 'topics') {
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

    $not_found_users = [];

    if (!empty($participants)) {
        foreach ($participants as $username) {
            $user = $DB->get_record('user', array('username' => $username));
            if ($user) {
                $manualenrol->enrol_user($manualinstance, $user->id, 5); // 5 -> student role
            } else {
                $not_found_users[] = [
                    'username' => $username,
                    'reason' => 'User not found'
                ];
            }
        }
    }

    return [$newcourse, $not_found_users];
}

function local_webcourse_generate_csv($data, $coursename) {
    if (ob_get_length()) {
        ob_end_clean();
    }

    $filename = "users_not_found_{$coursename}_" . time() . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['Username', 'Reason']);

    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    fclose($output);

    exit();
}
