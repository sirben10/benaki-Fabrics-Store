CREATE DATABASE IF NOT EXISTS benaki_fabrics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE benaki_fabrics;
CREATE TABLE IF NOT EXISTS products (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL UNIQUE, slug VARCHAR(140) NOT NULL UNIQUE,
 description VARCHAR(600) NULL, long_description TEXT NULL, details TEXT NULL, care_instructions TEXT NULL,
 image_path VARCHAR(255) NULL, yard_price DECIMAL(12,2) NOT NULL DEFAULT 0, trouser_price DECIMAL(12,2) NOT NULL DEFAULT 0,
 is_active TINYINT(1) NOT NULL DEFAULT 1, sort_order INT NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_products_active_sort(is_active,sort_order)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS hero_slides (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, eyebrow VARCHAR(120), title VARCHAR(220) NOT NULL, subtitle VARCHAR(600), image_path VARCHAR(255) NOT NULL,
 cta_text VARCHAR(100), cta_link VARCHAR(255), is_active TINYINT(1) NOT NULL DEFAULT 1, sort_order INT NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_slides_active_sort(is_active,sort_order)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS gallery_media (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 media_type ENUM('photo','video') NOT NULL DEFAULT 'photo', title VARCHAR(180) NOT NULL, description VARCHAR(600) NULL,
 media_path VARCHAR(255) NOT NULL, thumbnail_path VARCHAR(255) NULL, product_id BIGINT UNSIGNED NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1, sort_order INT NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_gallery_type_active(media_type,is_active,sort_order),
 CONSTRAINT fk_gallery_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS orders (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_ref VARCHAR(32) NOT NULL UNIQUE, product_id BIGINT UNSIGNED NULL, fabric_type VARCHAR(120) NOT NULL,
 colors TEXT NOT NULL, measurement ENUM('yard','trouser_length') NOT NULL, quantity DECIMAL(10,2) NOT NULL, unit_price DECIMAL(12,2) NOT NULL,
 subtotal DECIMAL(12,2) NOT NULL, discount DECIMAL(12,2) NOT NULL DEFAULT 0, total_amount DECIMAL(12,2) NOT NULL,
 fullname VARCHAR(120) NOT NULL, phone VARCHAR(50) NOT NULL, email VARCHAR(160) NULL, location VARCHAR(200) NOT NULL, description TEXT NULL,
 status ENUM('new','contacted','completed','cancelled') NOT NULL DEFAULT 'new', payment_status ENUM('unpaid','pending','paid','failed','refunded') NOT NULL DEFAULT 'unpaid', paystack_reference VARCHAR(120) NULL, paystack_transaction_id BIGINT UNSIGNED NULL, paid_at DATETIME NULL, receipt_token CHAR(64) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_orders_status_created(status,created_at), CONSTRAINT fk_orders_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE UNIQUE INDEX uq_orders_paystack_ref ON orders(paystack_reference);
CREATE UNIQUE INDEX uq_orders_receipt_token ON orders(receipt_token);
CREATE INDEX idx_orders_payment ON orders(payment_status,created_at);
CREATE TABLE IF NOT EXISTS order_items (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id BIGINT UNSIGNED NOT NULL, product_id BIGINT UNSIGNED NULL, product_name VARCHAR(120) NOT NULL, colors TEXT NOT NULL, measurement ENUM('yard','trouser_length') NOT NULL, quantity DECIMAL(10,2) NOT NULL, unit_price DECIMAL(12,2) NOT NULL, line_total DECIMAL(12,2) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_order_items_order(order_id), CONSTRAINT fk_order_items_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE, CONSTRAINT fk_order_items_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS contact_messages (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, fullname VARCHAR(120) NOT NULL, phone VARCHAR(50), email VARCHAR(160), message TEXT NOT NULL,
 status ENUM('unread','read') NOT NULL DEFAULT 'unread', read_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_messages_status_created(status,created_at)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS admin_users (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, username VARCHAR(80) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1,last_login_at DATETIME NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS admin_login_attempts (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,ip_address VARCHAR(45) NOT NULL,username VARCHAR(80),success TINYINT(1) NOT NULL DEFAULT 0,attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,INDEX idx_login_ip_time(ip_address,attempted_at)) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS store_settings(setting_key VARCHAR(80) PRIMARY KEY,setting_value TEXT NULL,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB;
INSERT IGNORE INTO products(name,slug,description,long_description,details,care_instructions,image_path,yard_price,trouser_price,is_active,sort_order) VALUES
('Stock','stock','Premium Stock fabric for quality everyday and occasion wear.','A dependable fabric choice with a refined finish for tailored looks, everyday outfits and special occasions.','Quality finish; versatile styling; available in multiple colours subject to stock.','Follow the fabric care label. Store clean and dry.','assets/img/fabric-1.jpg',4000,5000,1,1),
('Jonkoso','jonkoso','Rich, durable Jonkoso fabric with a premium finish.','Jonkoso combines a rich texture with a durable finish suitable for elegant traditional and contemporary outfits.','Soft texture; durable feel; suitable for tailored outfits.','Wash and iron according to garment-maker instructions.','assets/img/fabric-2.jpg',3000,4000,1,2),
('Crepe','crepe','Elegant crepe fabrics for stylish dresses and statement looks.','Smooth, elegant crepe options for fashion-forward dresses, flowing outfits and statement pieces.','Elegant drape; smooth feel; versatile for dresses.','Use gentle washing and moderate ironing.','assets/img/floral-crepe.jpg',2500,3000,1,3),
('Vintage','vintage','Classic vintage textile selections in beautiful shades.','Classic textile selections for customers who prefer timeless colours, textures and styling.','Classic look; versatile shades; suitable for smart outfits.','Keep folded and protected from moisture.','assets/img/fabric-4.jpg',2000,2000,1,4),
('Chinos','chinos','Smart, versatile chinos fabric for tailored outfits.','A practical and stylish textile for smart trousers, shirts and structured casual outfits.','Versatile; durable; suitable for structured garments.','Machine or hand wash according to garment instructions.','assets/img/fabric-5.jpg',3000,3000,1,5);
INSERT IGNORE INTO hero_slides(eyebrow,title,subtitle,image_path,cta_text,cta_link,is_active,sort_order) VALUES
('BENAKI FABRICS','Finest quality fabrics for every occasion.','Shop premium textile materials with dependable service from Calabar to South East and South South Nigeria.','assets/img/fabric-stack.jpg','Shop Fabrics','fabrics.php',1,1),
('1-YEAR ANNIVERSARY','One year of quality. A reason to celebrate.','Enjoy special anniversary savings on qualifying quantities while this campaign runs.','assets/img/anniversary-source.jpg','See Anniversary Offers','anniversary.php',1,2),
('OUR COLLECTION','Stock. Jonkoso. Crepe. Vintage. Chinos.','Explore fabrics for tailoring, fashion design, personal wear and production.','assets/img/floral-crepe.jpg','Explore Collection','fabrics.php',1,3),
('DELIVERY','Quality supply, made simple.','Tell us what you need and our team will confirm availability and delivery.','assets/img/fabric-3.jpg','Contact Us','contact.php',1,4);
INSERT IGNORE INTO store_settings(setting_key,setting_value) VALUES ('announcement','Quality fabrics. Exceptional service. Always.'),('about_title','A dependable fabric store built around quality.'),('about_text','Benaki Fabrics deals in the sales and supply of quality fabric materials from Calabar to customers across South East and South South Nigeria.'),('anniversary_enabled','1');
INSERT INTO gallery_media(media_type,title,description,media_path,is_active,sort_order)
SELECT 'photo','Benaki Fabrics Collection','A selection of quality fabrics from the Benaki collection.','assets/img/fabric-stack.jpg',1,1
WHERE NOT EXISTS (SELECT 1 FROM gallery_media WHERE media_path='assets/img/fabric-stack.jpg');
INSERT INTO gallery_media(media_type,title,description,media_path,is_active,sort_order)
SELECT 'photo','Premium Fabric Texture','Quality textile materials ready for your next outfit.','assets/img/fabric-2.jpg',1,2
WHERE NOT EXISTS (SELECT 1 FROM gallery_media WHERE media_path='assets/img/fabric-2.jpg');
INSERT INTO gallery_media(media_type,title,description,media_path,is_active,sort_order)
SELECT 'photo','Elegant Crepe','Smooth and elegant fabric selections.','assets/img/floral-crepe.jpg',1,3
WHERE NOT EXISTS (SELECT 1 FROM gallery_media WHERE media_path='assets/img/floral-crepe.jpg');
