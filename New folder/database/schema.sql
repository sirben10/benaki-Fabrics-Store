CREATE DATABASE IF NOT EXISTS benaki_anniversary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE benaki_anniversary;

CREATE TABLE IF NOT EXISTS orders (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_ref VARCHAR(30) NOT NULL UNIQUE,
    fabric_type ENUM('Jonkoso','Crepe','Stock','Vintage','Chinos') NOT NULL,
    colors TEXT NOT NULL,
    measurement ENUM('yard','trouser_length') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    fullname VARCHAR(120) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    location VARCHAR(180) NOT NULL,
    description TEXT NULL,
    status ENUM('new','contacted','completed','cancelled') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_order_created (created_at),
    INDEX idx_order_phone (phone)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    fullname VARCHAR(120) NOT NULL,
    phone VARCHAR(40) NULL,
    email VARCHAR(150) NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_contact_created (created_at)
) ENGINE=InnoDB;
