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
 * Library of functions for webcourse plugin.
 *
 * @package   local_webcourse
 * @copyright 2024 Maxwell Souza <maxwell.hygor01@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/enrol/manual/locallib.php');

/**
 * Create a new course and enroll users.
 * @param string $fullname complete name of the course.
 * @param string $shortname it's the same name avoiding name collision.
 * @param int $categoryid id of the category the course will be registered.
 * @param array $participants participants data to enroll the users.
 * @param string $summary empty summary that will be filled on course creation.
 * @param string $format default value 'topics' for any course.
 * @return array Data of the created course and users not found.
 */
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

    $instances = enrol_get_instances($newcourse->id, true);
    $manualinstance = null;

    foreach ($instances as $instance) {
        if ($instance->enrol === 'manual') {
            $manualinstance = $instance;
            break;
        }
    }

    if (!$manualinstance) {
        throw new moodle_exception('noenrolmentplugin', 'error');
    }

    $notfoundusers = [];

    if (!empty($participants)) {
        foreach ($participants as $username) {
            $user = $DB->get_record('user', ['username' => $username]);
            if ($user) {
                $manualenrol->enrol_user($manualinstance, $user->id, 5);
            } else {
                $notfoundusers[] = [
                    'label' => __('Username: ' . $username),
                    'value' => __('User not found'),
                ];
            }
        }
    }

    return [$newcourse, $notfoundusers];
}

/**
 * Create the CSV file with missing users.
 * @param array $data collection of missing users.
 * @param string $coursename complete name of the course.
 */
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
        $formattedrow = [
            'label' => __('Username: ' . $row['username']),
            'value' => $row['reason'],
        ];

        fputcsv($output, [$formattedrow['label'], $formattedrow['value']]);
    }
    fclose($output);

    exit();
}
