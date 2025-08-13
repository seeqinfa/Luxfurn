-- Drop existing tables if needed (safe re-import)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;

-- Create orders table (with username right after order_id)
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,

    customer_first_name VARCHAR(50) NOT NULL,
    customer_last_name  VARCHAR(50) NOT NULL,
    customer_email      VARCHAR(100) NOT NULL,
    customer_phone      VARCHAR(20) NOT NULL,

    shipping_address VARCHAR(255) NOT NULL,
    shipping_city    VARCHAR(100) NOT NULL,
    shipping_state   VARCHAR(50)  NOT NULL,
    shipping_zip     VARCHAR(20)  NOT NULL,

    subtotal      DECIMAL(10,2) NOT NULL,
    tax_amount    DECIMAL(10,2) NOT NULL,
    shipping_fee  DECIMAL(10,2) NOT NULL,
    total_amount  DECIMAL(10,2) NOT NULL,

    order_status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    special_instructions TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Optional (uncomment if you have a `users(username)` table and want a FK)
-- ALTER TABLE orders
--   ADD CONSTRAINT fk_orders_username
--   FOREIGN KEY (username) REFERENCES users(username)
--   ON UPDATE CASCADE ON DELETE RESTRICT;

-- Create order_items table
CREATE TABLE order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    furniture_id INT NOT NULL,
    furniture_name VARCHAR(255) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (furniture_id) REFERENCES furnitures(furnitureID) ON DELETE RESTRICT
);

-- Indexes
CREATE INDEX idx_orders_username ON orders(username);
CREATE INDEX idx_orders_email    ON orders(customer_email);
CREATE INDEX idx_orders_status   ON orders(order_status);
CREATE INDEX idx_orders_created  ON orders(created_at);

CREATE INDEX idx_order_items_order     ON order_items(order_id);
CREATE INDEX idx_order_items_furniture ON order_items(furniture_id);
