USE benaki_fabrics;
ALTER TABLE products ADD COLUMN long_description TEXT NULL AFTER description;
ALTER TABLE products ADD COLUMN details TEXT NULL AFTER long_description;
ALTER TABLE products ADD COLUMN care_instructions TEXT NULL AFTER details;
CREATE TABLE IF NOT EXISTS gallery_media (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, media_type ENUM('photo','video') NOT NULL DEFAULT 'photo', title VARCHAR(180) NOT NULL, description VARCHAR(600) NULL,
 media_path VARCHAR(255) NOT NULL, thumbnail_path VARCHAR(255) NULL, product_id BIGINT UNSIGNED NULL, is_active TINYINT(1) NOT NULL DEFAULT 1, sort_order INT NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_gallery_type_active(media_type,is_active,sort_order), CONSTRAINT fk_gallery_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
