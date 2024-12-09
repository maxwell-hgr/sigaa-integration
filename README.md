# Moodle Plugin: Local WEBCOURSE

## Description
This plugin integrates Moodle with an external microservice to create courses and enroll users. It validates users, generates a CSV file for non-existent users, and automates course setup.

## Installation
1. Copy the plugin folder to `moodle_root/local/`.
2. Access the Moodle admin panel to finalize installation.
3. Configure the external API endpoint in the plugin settings.

## Features
- Fetches course data from a microservice.
- Creates courses automatically.
- Enrolls users based on usernames.
- Generates a CSV for users not found in Moodle.

## Requirements
- Moodle 4.x or higher
- PHP 7.3 or higher
- Admin permission

## Usage
1. Set the correct endpoint for your requisition.
2. Access the plugin page in Moodle: `/local/webcourse/index.php`.
3. Input the course ID from the external microservice.
3. Confirm course creation.
4. Download the CSV of missing users, if applicable.

## Error Handling
- Displays errors for invalid inputs or missing API responses.
- Warns about missing users with a downloadable CSV report.

## File Structure
- `index.php`: Handles user interaction and workflow.
- `lib.php`: Helper functions for course creation and enrollment.
- `lang/en/local_webcourse.php`: Language strings.

## License
This project is licensed under the [GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.html).

## Author
Maxwell H. S. Souza
