<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('courses', new admin_externalpage(
        'local_sigaa',
        get_string('pluginname', 'local_sigaa'),
        new moodle_url('/local/sigaa/index.php')
    ));
}
