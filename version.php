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

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_webcourse';
$plugin->version = 2024120300;
$plugin->requires = 2021051700; // Requires Moodle 3.11 or higher.
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.0';
