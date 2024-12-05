<?php

require_once(__DIR__ . '/../../config.php');
require_once('lib.php');

use local_sigaa\form\fetch_form;

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_url(new moodle_url('/local/sigaa/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_sigaa'));
$PAGE->set_heading(get_string('pluginname', 'local_sigaa'));

$mform = new fetch_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php', ['section' => 'courses']));
} else if ($data = $mform->get_data()) {
    $course_id = intval($data->courseid);
    $url = "http://localhost:3000/courses/{$course_id}";

    $response = file_get_contents($url);
    $course_data = json_decode($response, true);

    if (isset($course_data['name']) && isset($course_data['participants'])) {

        echo $OUTPUT->header();
        echo html_writer::tag('h2', get_string('course_name', 'local_sigaa') . ': ' . $course_data['name']);
        echo html_writer::tag('p', get_string('participants_count', 'local_sigaa') . ': ' . count($course_data['participants']));

        $participants = isset($course_data['participants']) ? $course_data['participants'] : [];

        $confirm_url = new moodle_url('/local/sigaa/index.php', [
            'confirm' => 1,
            'coursename' => $course_data['name'],
            'courseid' => $course_id,
            'participants' => json_encode($participants)
        ]);
        echo html_writer::tag('p', html_writer::link($confirm_url, get_string('confirmcreate', 'local_sigaa'), ['class' => 'btn btn-primary']));
        echo $OUTPUT->footer();
        die();
    } else {
        echo $OUTPUT->header();
        echo html_writer::tag('p', get_string('no_course_found', 'local_sigaa'));
        echo $OUTPUT->footer();
        die();
    }
}

if (optional_param('confirm', 0, PARAM_INT) === 1) {
    $coursename = required_param('coursename', PARAM_TEXT);
    $courseid = required_param('courseid', PARAM_INT);
    $participants = json_decode(optional_param('participants', '[]', PARAM_RAW)); // Recupere os participantes da URL

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
        echo html_writer::tag('h2', get_string('coursecreated', 'local_sigaa') . ': ' . $newcourse->fullname);

        if (!empty($not_found_users)) {
            $count_not_found = count($not_found_users);
            echo html_writer::tag('p', get_string('usersnotfound', 'local_sigaa') . ": {$count_not_found}");

            // Codificar os usuários não encontrados para envio pela URL
            $csv_data = urlencode(json_encode($not_found_users));
            $csv_url = new moodle_url('/local/sigaa/index.php', [
                'downloadcsv' => 1,
                'coursename' => $newcourse->fullname,
                'data' => $csv_data
            ]);

            // Botão para baixar o CSV.
            echo html_writer::tag(
                'p',
                html_writer::link($csv_url, get_string('downloadcsv', 'local_sigaa'), ['class' => 'btn btn-secondary'])
            );
        }

        echo $OUTPUT->footer();
    } catch (Exception $e) {
        echo $OUTPUT->header();
        echo html_writer::tag('p', get_string('coursecreationerror', 'local_sigaa') . ': ' . $e->getMessage());
        echo $OUTPUT->footer();
    }

    die();
}

// Gerar CSV quando o botão for clicado.
if (optional_param('downloadcsv', 0, PARAM_INT) === 1) {
    $coursename = required_param('coursename', PARAM_TEXT);
    $not_found_users = json_decode(urldecode(required_param('data', PARAM_RAW)), true);

    if (!empty($not_found_users)) {
        generate_csv($not_found_users, $coursename);
    } else {
        redirect(new moodle_url('/local/sigaa/index.php'), get_string('nocsvdata', 'local_sigaa'), null, \core\output\notification::NOTIFY_WARNING);
    }

    die();
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();