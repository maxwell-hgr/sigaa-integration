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
require_once('lib.php');

use local_webcourse\form\fetch_form;

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_url(new moodle_url('/local/webcourse/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_webcourse'));
$PAGE->set_heading(get_string('pluginname', 'local_webcourse'));

$mform = new fetch_form();

$endpoint = get_config('local_webcourse', 'endpoint');

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php', ['section' => 'courses']));
} else if ($data = $mform->get_data()) {
    $course_id = clean_param($data->courseid, PARAM_INT); // Sanitiza o ID do curso
    $url = "{$endpoint}{$course_id}";

    $response = file_get_contents($url);
    $course_data = json_decode($response, true);

    if (isset($course_data['name']) && isset($course_data['participants'])) {
        $coursename = clean_param($course_data['name'], PARAM_TEXT); // Sanitiza o nome do curso
        $participants = array_map(fn($p) => clean_param($p, PARAM_USERNAME), $course_data['participants']); // Sanitiza os usernames

        echo $OUTPUT->header();
        echo html_writer::tag('h2', get_string('course_name', 'local_webcourse') . ': ' . $coursename);
        echo html_writer::tag('p', get_string('participants_count', 'local_webcourse') . ': ' . count($participants));

        $confirm_url = new moodle_url('/local/webcourse/index.php', [
            'confirm' => 1,
            'coursename' => $coursename,
            'courseid' => $course_id,
            'participants' => json_encode($participants)
        ]);
        echo html_writer::tag('p', html_writer::link($confirm_url, get_string('confirmcreate', 'local_webcourse'), ['class' => 'btn btn-primary']));
        echo $OUTPUT->footer();
        die();
    } else {
        echo $OUTPUT->header();
        echo html_writer::tag('p', get_string('no_course_found', 'local_webcourse'));
        echo $OUTPUT->footer();
        die();
    }
}

if (optional_param('confirm', 0, PARAM_INT) === 1) {
    $coursename = clean_param(required_param('coursename', PARAM_TEXT), PARAM_TEXT);
    $courseid = clean_param(required_param('courseid', PARAM_INT), PARAM_INT);
    $participants = json_decode(optional_param('participants', '[]', PARAM_RAW));

    $participants = array_map(fn($p) => clean_param($p, PARAM_USERNAME), $participants);

    try {
        list($newcourse, $not_found_users) = create_course_custom(
            $coursename,
            $coursename,
            1,
            $participants,
            'Curso criado automaticamente',
            'topics'
        );

        echo $OUTPUT->header();
        echo html_writer::tag('h2', get_string('coursecreated', 'local_webcourse') . ': ' . $newcourse->fullname);

        if (!empty($not_found_users)) {
            $count_not_found = count($not_found_users);
            echo html_writer::tag('p', get_string('usersnotfound', 'local_webcourse') . ": {$count_not_found}");

            $csv_data = urlencode(json_encode($not_found_users));
            $csv_url = new moodle_url('/local/webcourse/index.php', [
                'downloadcsv' => 1,
                'coursename' => clean_param($newcourse->fullname, PARAM_TEXT),
                'data' => $csv_data
            ]);

            echo html_writer::tag(
                'p',
                html_writer::link($csv_url, get_string('downloadcsv', 'local_webcourse'), ['class' => 'btn btn-secondary'])
            );
        }

        echo $OUTPUT->footer();
    } catch (Exception $e) {
        echo $OUTPUT->header();
        echo html_writer::tag('p', get_string('coursecreationerror', 'local_webcourse') . ': ' . $e->getMessage());
        echo $OUTPUT->footer();
    }

    die();
}

if (optional_param('downloadcsv', 0, PARAM_INT) === 1) {
    $coursename = clean_param(required_param('coursename', PARAM_TEXT), PARAM_TEXT);
    $not_found_users = json_decode(urldecode(required_param('data', PARAM_RAW)), true);

    if (!empty($not_found_users)) {
        generate_csv($not_found_users, $coursename);
    } else {
        redirect(new moodle_url('/local/webcourse/index.php'), get_string('nocsvdata', 'local_webcourse'), null, \core\output\notification::NOTIFY_WARNING);
    }

    die();
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
