<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('courses', new admin_externalpage(
        'local_sigaa',
        get_string('pluginname', 'local_sigaa'),
        new moodle_url('/local/sigaa/index.php')
    ));

    $settings = new admin_settingpage('local_sigaa_settings', get_string('pluginname', 'local_sigaa'));

    $settings->add(new admin_setting_configtext(
        'local_sigaa/endpoint',
        get_string('endpoint', 'local_sigaa'),
        get_string('endpoint_desc', 'local_sigaa'),
        'http://localhost:3000/courses/',
        PARAM_URL
    ));

    $ADMIN->add('localplugins', $settings);
}
