# Beauty Salon Management System
by Vivacity Design Web Agency - March / May 2025

A simple management system for beauty salons built with PHP, HTML, CSS, and JavaScript. This system uses JSON files for data storage, eliminating the need for a database.

## Features

- Client Management
- Appointment Scheduling
- Service Management
- Staff Management
- Reporting and Analytics
- User Authentication

## Requirements

- PHP 7.4 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. Download or clone the repository to your web server directory
2. Ensure the web server has write permissions to the `data` directory
3. Set up proper file permissions:
   ```
   chmod 755 -R beauty_salon
   chmod 777 -R beauty_salon/data
   ```
4. (optional) set up your OpenAI api key in file `marketing.txt` in the `data` directory
5. Access the application through your web browser

## Default Login

- **Username:** admin
- **Password:** 654321

It's recommended to change the default password after first login.

## Directory Structure

```
beauty_salon/
├── assets/           # CSS, JavaScript, and image files
├── data/             # JSON data files
├── includes/         # Core PHP files
├── pages/            # Application pages
└── index.php         # Main entry point
```

## Data Storage

All data is stored in JSON and TXT files located in the `data` directory:

- `clients.json` - Client information
- `appointments.json` - Appointment information
- `services.json` - Service details
- `staff.json` - Staff members information
- `users.json` - User accounts for authentication
- `marketing.txt` - OpenAI api key (mandatory for marketing page)

## Security Considerations

- The `.htaccess` file included restricts direct access to JSON files and PHP includes
- For production, enable HTTPS by uncommenting the relevant section in the `.htaccess` file
- Change the default admin password immediately

## Licensing

This project is produced by Vivacity Design (Alessandro Demontis) and is provided to the user under payment of a one-time fee.

## Support

For support or questions, please write to info@vivacitydesign.net.
