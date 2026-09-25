# BlogHub

BlogHub is a PHP-based blog platform with a public-facing blog homepage, category-style browsing, live search suggestions, and an admin dashboard for managing blog content. The project uses MySQL for data storage and Tailwind CSS for the frontend UI.

## Features

- Public blog homepage with hero section and featured content
- Search and filter blog posts by title, subtitle, and author
- Popular and recent blog sections
- Single post view with image gallery support
- Admin login and dashboard for managing site data
- Dynamic site content loaded from the database for reusable text blocks
- JavaScript live search suggestions without page reload

## Tech Stack

- PHP
- MySQL / MariaDB
- JavaScript
- Tailwind CSS via CDN

## Project Structure

```text
BlogHub/
├── admin/
│   ├── dashboard.php
│   ├── dashboard.js
│   ├── hash.php
│   ├── index.php
│   └── session_manager.php
├── uploads/
├── db.php
├── index.js
├── index.php
├── search_suggestions.php
├── README.md
└── ...
```

## Requirements

- PHP 7.4 or later
- MySQL or MariaDB
- A web server such as Apache, Nginx, XAMPP, or the PHP built-in development server
- A modern web browser

Composer and Node.js are not required.

## How to Run Locally

### Option 1: PHP built-in development server

1. Clone the repository and enter the project directory:

```bash
git clone https://github.com/Rimi-03/BlogHub.git
cd BlogHub
```

2. Make sure PHP and MySQL are installed and running.

3. Create a MySQL database named `db_bloghub`.

4. Import the BlogHub database schema and seed data into `db_bloghub`. The application expects tables such as:

   - `admin`
   - `author`
   - `blogs`
   - `blog_images`
   - `site_content`

5. Configure the database connection in `db.php`. The default configuration is:

```php
$conn = new mysqli("localhost", "root", "", "db_bloghub");
```

Change the host, username, password, or database name if your local MySQL setup uses different values.

6. From the project root, start the PHP server:

```bash
php -S localhost:8000
```

Keep this terminal window running.

7. Open the public website in a browser:

```text
http://localhost:8000/
```

The main entry point is `index.php`, so this URL also works:

```text
http://localhost:8000/index.php
```

8. Open the admin panel at:

```text
http://localhost:8000/admin/
```

### Option 2: XAMPP

1. Install XAMPP and start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Copy or clone this repository into XAMPP's web root:

```text
C:\xampp\htdocs\BlogHub
```

On macOS, the equivalent location is commonly:

```text
/Applications/XAMPP/htdocs/BlogHub
```

3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Create a database named `db_bloghub`.
5. Import the database schema and seed data into that database.
6. Check `db.php` and update the MySQL credentials if necessary.
7. Visit the application:

```text
http://localhost/BlogHub/
```

8. Visit the admin panel:

```text
http://localhost/BlogHub/admin/
```

## Database Configuration

The application connects to MySQL through `db.php`. Before running the project, verify these values:

```php
$conn = new mysqli("localhost", "root", "", "db_bloghub");
```

The database should contain the tables used by the application:

- `admin` — administrator login accounts
- `author` — blog authors
- `blogs` — blog posts and view counts
- `blog_images` — additional images attached to posts
- `site_content` — editable homepage and footer content

If no database dump is included in the repository, create the required schema and initial records before opening the application; otherwise, the pages will not be able to display posts or authenticate administrators.

## Admin Panel

The admin portal is available at:

```text
http://localhost:8000/admin/index.php
```

For XAMPP, use:

```text
http://localhost/BlogHub/admin/index.php
```

Log in with an administrator account stored in the `admin` table. Passwords are checked using PHP's password verification functions.

## Search Behavior

The homepage includes an autocomplete-style search field. As the user types, `index.js` requests suggestions from `search_suggestions.php`. A valid search redirects to:

```text
index.php?page=search&q=your-query
```

## License

This project does not currently include a license file. Please check with the repository owner before using it in production or redistributing it.

## Contribution

Contributions are welcome. If you want to improve the project, open a pull request with a clear description of the change.
