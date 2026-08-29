# Benaki Fabrics Store — Multipage PDO Web Application

A professional responsive PHP/MySQL/Tailwind/JavaScript fabric store with a secure admin area.

## Public pages
- `index.php` — storefront homepage and database-driven hero slider
- `fabrics.php` — full fabric catalogue
- `fabric.php?slug=jonkoso` — individual fabric details and ordering
- `about.php` — company/about page
- `gallery.php` — responsive photos and videos gallery
- `anniversary.php` — strategic 1-year anniversary campaign
- `contact.php` — AJAX contact form

## Admin
- Dashboard
- Fabrics / Products CRUD
- Hero Slides CRUD
- Gallery Media CRUD (photos and videos)
- Orders
- Contact Messages
- Secure PDO database access

## Installation
1. Put the project in `htdocs`.
2. Import `database/schema.sql` into MySQL.
3. Update `config/config.php` or environment variables.
4. Run `admin/setup.php` once to create the first admin account, then delete/disable setup.
5. Ensure `assets/uploads/media`, `assets/uploads/products`, and `assets/uploads/slides` are writable by PHP.

## Existing database
If upgrading the previous application, run `database/migration.sql` once. If your previous migration already added the new product columns

## Media
Photos support JPG, PNG and WEBP. Videos support MP4, WEBM and OGG. Admin uploads are stored under `assets/uploads/media/`. The public gallery is fully responsive and can filter Photos/Videos.

## Architecture
All SQL uses PDO prepared statements. The public site is multipage, while product pricing/order logic remains server validated.
