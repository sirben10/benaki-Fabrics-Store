-- Run once on an existing Benaki Fabrics database.
ALTER TABLE orders
  ADD COLUMN email VARCHAR(160) NULL AFTER phone,
  ADD COLUMN payment_status ENUM('unpaid','pending','paid','failed','refunded') NOT NULL DEFAULT 'unpaid' AFTER status,
  ADD COLUMN paystack_reference VARCHAR(120) NULL AFTER payment_status,
  ADD COLUMN paystack_transaction_id BIGINT UNSIGNED NULL AFTER paystack_reference,
  ADD COLUMN paid_at DATETIME NULL AFTER paystack_transaction_id,
  ADD COLUMN receipt_token CHAR(64) NULL AFTER paid_at;

CREATE INDEX idx_orders_payment ON orders(payment_status,created_at);
CREATE UNIQUE INDEX uq_orders_paystack_ref ON orders(paystack_reference);
CREATE UNIQUE INDEX uq_orders_receipt_token ON orders(receipt_token);

CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NULL,
  product_name VARCHAR(120) NOT NULL,
  colors TEXT NOT NULL,
  measurement ENUM('yard','trouser_length') NOT NULL,
  quantity DECIMAL(10,2) NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  line_total DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_order_items_order(order_id),
  CONSTRAINT fk_order_items_order FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
