-- ================================================================
--  Labuyo Fisheries Corp. — Database Schema
--  Database: labuyo_db
--  Run: mysql -u root -p < database/schema.sql
-- ================================================================

CREATE DATABASE IF NOT EXISTS labuyo_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE labuyo_db;

-- ── products ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
    id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name          VARCHAR(120)    NOT NULL,
    description   TEXT            NULL,
    price_per_kg  DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    active        TINYINT(1)      NOT NULL DEFAULT 1
                    COMMENT '1 = shown in order form, 0 = hidden',
    created_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── orders ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    customer_name   VARCHAR(120)    NOT NULL,
    email           VARCHAR(180)    NOT NULL,
    phone           VARCHAR(30)     NOT NULL,
    address         TEXT            NOT NULL,
    product_id      INT UNSIGNED    NOT NULL,
    quantity_kg     DECIMAL(10, 2) NOT NULL,
    total_price     DECIMAL(12, 2) NOT NULL,
    delivery_date   DATE            NULL
                    COMMENT 'Customer preferred delivery date',
    notes           TEXT            NULL,
    status          ENUM(
                        'pending',
                        'confirmed',
                        'processing',
                        'delivered',
                        'cancelled'
                    )               NOT NULL DEFAULT 'pending',
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_orders_product
        FOREIGN KEY (product_id) REFERENCES products (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── contact_messages ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS contact_messages (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name        VARCHAR(120)  NOT NULL,
    email       VARCHAR(180)  NOT NULL,
    phone       VARCHAR(30)   NULL,
    message     TEXT          NOT NULL,
    is_read     TINYINT(1)    NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed: sample products ─────────────────────────────────────────
INSERT INTO products (name, description, price_per_kg, active) VALUES
    ('Fresh Bangus (Milkfish)',
     'Whole, freshly harvested milkfish from our Obando fishponds. Best for sinigang, paksiw, and grilling.',
     180.00, 1),
    ('Deboned Bangus',
     'Expertly cleaned and deboned milkfish — ready to cook straight from the pack.',
     250.00, 1),
    ('Smoked Bangus (Tinapa-style)',
     'Traditional smoked milkfish, perfect with garlic fried rice and vinegar.',
     300.00, 1),
    ('Marinated Bangus (Daing)',
     'Overnight-marinated, sun-dried bangus packed in vacuum-sealed bags for freshness.',
     320.00, 1);
