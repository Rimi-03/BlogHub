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
- Tailwind CSS

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

- PHP 7.4+
- MySQL or MariaDB
- Apache or Nginx web server
- Composer is not required for this project

## Installation

1. Clone the repository:

```bash
git clone https://github.com/Rimi-03/BlogHub.git
cd BlogHub
```

2. Create a MySQL database named `db_bloghub`.

3. Update the database connection in `db.php` if needed:

```php
$conn = new mysqli("localhost", "root", "", "db_bloghub");
```

Use your actual database credentials and database name.

4. Import your schema and seed data into the database. The application expects tables including:

- `admin`
- `author`
- `blogs`
- `blog_images`
- `site_content`

5. Start a local PHP server from the project root:

```bash
php -S localhost:8000
```

6. Open the app in your browser:

```text
http://localhost:8000/index.php
```

## Admin Panel

The admin portal is available at:

```text
http://localhost:8000/admin/index.php
```

Use a valid admin account from the `admin` table in your database.

## Database Notes

The application reads several content blocks from the `site_content` table, such as:

- hero title
- hero description
- hero image
- footer text
- popular section title
- recent section title

The blog listing and single-post pages fetch blog data from the `blogs` table, joined with `author`.

## Search Behavior

The homepage includes an autocomplete-like search field. The frontend calls `search_suggestions.php` while typing, and the search form redirects to `index.php?page=search&q=...` when a valid query is submitted.

## License

This project does not currently include a license file. Please check with the repository owner before using it in production or redistributing it.

## Contribution

Contributions are welcome. If you want to improve the project, open a pull request with a clear description of the change.

## Notes

This project appears to be a self-contained blog CMS built for learning, demos, or small personal publishing workflows. Some setup details may vary depending on your local development environment and database schema.
