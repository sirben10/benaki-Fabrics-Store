# Benaki Fabrics — 1 Year Anniversary Web Application

A responsive single-page PHP/MySQL website with Tailwind CSS utilities, custom CSS, JavaScript, AJAX order/contact forms, automatic price calculation, and a five-design anniversary flyer popup rotator.

## Stack
- PHP 8+
- MySQL 5.7+/8+
- Tailwind CSS via official CDN for rapid utility styling
- Custom CSS in `assets/css/styles.css`
- Vanilla JavaScript + Fetch API AJAX in `assets/js/app.js`
- MySQLi prepared statements

## Setup
1. Create/import the database using `database/schema.sql`.
2. Edit `config/config.php`, or create `config/config.local.php` and load your credentials there if you prefer a custom deployment.
3. Put the project in Apache/XAMPP/WAMP/LAMP document root.
4. Open `index.php` in a PHP-enabled server. Do not open the PHP file directly from the filesystem.
5. Ensure `ajax/` can execute PHP and that the database user has INSERT permission on both tables.

## Database
Tables:
- `orders` — stores order requests, selected colours, unit price and server-recalculated total.
- `contact_messages` — stores contact form submissions.

## Pricing
- Jonkoso: ₦3,000/yard, ₦4,000/trouser length
- Crepe: ₦2,500/yard, ₦3,000/trouser length
- Stock: ₦4,000/yard, ₦5,000/trouser length
- Vintage: ₦2,000/yard, ₦2,000/trouser length (set as a configurable default because no trouser-length Vintage price was supplied)
- Chinos: ₦3,000/yard, ₦3,000/trouser length

## Anniversary popup
The homepage randomly selects one of five anniversary designs at page load and rotates to another design every 15 seconds. The supplied anniversary artwork is used as the primary visual reference/source, with four additional locally-rendered variations.

## Production hardening recommended
- Move database credentials to environment variables.
- Add CSRF tokens if the site is deployed with authenticated/admin features.
- Add rate limiting and CAPTCHA for public production traffic.
- Configure HTTPS and secure headers.
- Add an admin dashboard only behind authentication for managing orders/messages.
