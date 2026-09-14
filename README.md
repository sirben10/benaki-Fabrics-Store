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
Photos support JPG, PNG and WEBP. Videos support MP4, WEBM and OGG. Admin uploads are stored under `assets/uploads/media/` and are automatically reduced to 2 MB for one image, 10 MB total for multiple images, 3 MB for one video, or 15 MB total for multiple videos. FFmpeg must be available on the server, or configured with `BENAKI_FFMPEG_BIN`. The public gallery is fully responsive and can filter Photos/Videos.

## Architecture
All SQL uses PDO prepared statements. The public site is multipage, while product pricing/order logic remains server validated.

## Shopping cart + Paystack checkout

The store now supports a full shopping flow: add multiple fabric lines to cart, update/remove items, checkout, server-side order creation, Paystack payment initialization, Paystack popup completion, server-side payment verification, and a protected printable receipt.

1. Run `database/schema.sql` for a fresh database, or run `database/shop_migration.sql` once against an existing installation.
2. Configure `PAYSTACK_PUBLIC_KEY` and `PAYSTACK_SECRET_KEY` in the server environment. Keep the secret key server-side only.
3. Use Paystack test keys for local testing, then switch to live keys after completing Paystack activation and HTTPS setup.
4. The backend initializes transactions and verifies the reference and amount on the server before marking an order paid. Paystack recommends server-side initialization and verification; the secret key must not be exposed in frontend code. See the official docs: https://paystack.com/docs/payments/accept-payments/ and https://paystack.com/docs/api/transaction/.
5. Receipts are available only with the order reference plus a random receipt token, and can be printed/saved as PDF from the browser.
