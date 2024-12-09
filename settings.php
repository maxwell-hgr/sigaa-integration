<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('courses', new admin_externalpage(
        'local_webcourse',
        get_string('pluginname', 'local_webcourse'),
        new moodle_url('/local/webcourse/index.php')
    ));

    $settings = new admin_settingpage('local_webcourse_settings', get_string('pluginname', 'local_webcourse'));

    $settings->add(new admin_setting_configtext(
        'local_webcourse/endpoint',
        get_string('endpoint', 'local_webcourse'),
        get_string('endpoint_desc', 'local_webcourse'),
        'http://localhost:3000/courses/',
        PARAM_URL
    ));

    $ADMIN->add('localplugins', $settings);
}
