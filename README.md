# Moodle Plugin: Local WEBCOURSE

## Description

The **Local WEBCOURSE** plugin integrates Moodle with an external microservice to create courses and enroll users. It validates users, generates a CSV file for users not found in Moodle, and automates course setup based on the data fetched from the microservice.

## Installation

### Installation Steps:
1. **Copy the plugin folder** to the `moodle_root/local/` directory.
2. **Access the Moodle Admin Panel** to complete the installation of the plugin.
3. **Configure the plugin**:
   - Go to **Site Administration** > **Courses** and find the **WEBCOURSE** plugin.
   - Set the **external API endpoint** where the plugin will fetch course data and participants.

Once completed, the plugin will be ready for use.

## Features

- **Automatic Course Creation**: The plugin fetches course data from the external microservice and automatically creates courses in Moodle.
- **User Enrollment**: The plugin checks if users exist in Moodle:
  - Users found in Moodle are automatically enrolled in the course.
  - Users not found are recorded in a CSV file for manual enrollment.
- **CSV Generation**: A CSV file will be generated for participants who were not found in Moodle. This file can be downloaded and used for manually adding users.

## Endpoint Data Format

The plugin interacts with an external microservice that provides course and participant information in the following JSON format:

```json
{
    "courses": [
        {
            "id": 1,
            "name": "Exemple name - Geography Course",
            "participants": ["username01", "username02", "username03", "username04"]
        },
        {
            "id": 2,
            "name": "Exemple name - Physics Course",
            "participants": ["username11", "username12", "username13", "username13"]
        }
    ]
}
````

Each course contains the following properties:

- **id**: The unique identifier for the course.
- **name**: The name of the course.
- **participants**: A list of usernames that should be enrolled in the course.

The plugin checks if these usernames already exist in Moodle:
- If they do, they will be automatically enrolled in the corresponding course.
- If not, they will be recorded in the CSV file for manual enrollment.

## Configuration

- The **default role** for users being automatically enrolled is `student`, but this can be customized in the plugin's configuration settings.
- You can set the **endpoint URL** for the microservice and specify which **course category** the courses should be created in. This configuration can be accessed under **Plugins** > **Local Plugins**.

## Usage

1. **Set the correct external API endpoint**: Navigate to the plugin settings and input the URL of the API endpoint.
2. **Create Courses**: Input the course data from the external microservice or upload the course information.
3. **Confirm course creation**: After configuring, the plugin will automatically create courses based on the data received from the microservice.
4. **Download CSV**: If any participants are not found in Moodle, the plugin will generate a CSV file with the missing users, which you can download and use for manual enrollment.

## Accessing the Plugin

- The plugin can be found under **Site Administration** > **Courses**.
- You can configure and manage it through **Plugins** > **Local Plugins**.

## Error Handling

- Errors will be displayed for invalid inputs or missing API responses.
- If any users are missing from Moodle, the plugin will generate a **CSV report** containing these users to facilitate manual registration.

## File Structure

- **`index.php`**: Main file responsible for handling user interactions and the workflow.
- **`lib.php`**: Helper functions for creating courses and enrolling users.
- **`lang/en/local_webcourse.php`**: Language file for strings used in the plugin.

## License

This project is licensed under the [GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.html).

## Author

Maxwell H. S. Souza
