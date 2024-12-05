<?php
require_once(__DIR__ . '/../../config.php');
require_once('/home/maxwell/Desktop/cead/html/moodle/course/lib.php');

defined('MOODLE_INTERNAL') || die();

function create_course_custom($fullname, $shortname, $categoryid, $summary = '', $format = 'topics') {
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

    return $newcourse;
}

