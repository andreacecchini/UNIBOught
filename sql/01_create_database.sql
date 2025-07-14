CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `surname` VARCHAR(50) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `telephone_number` VARCHAR(10)
);

CREATE TABLE IF NOT EXISTS `clients` (
    `user_id` INT UNSIGNED PRIMARY KEY,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `vendors` (
    `user_id` INT UNSIGNED PRIMARY KEY,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `products` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT(UUID()),
    `vendor_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `image_name` VARCHAR(255),
    `image_alt` VARCHAR(1000),
    `quantity` INT UNSIGNED NOT NULL DEFAULT 0,
    `valid` TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`user_id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS `product_category` (
    `product_id` VARCHAR(36) NOT NULL,
    `category_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`product_id`, `category_id`),
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_id` INT UNSIGNED NOT NULL,
    `order_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expected_pickup_date` DATETIME NOT NULL,
    `status` ENUM(
        'pending',
        'processing',
        'shipped',
        'completed',
        'cancelled'
    ) NOT NULL DEFAULT 'pending',
    `isPaid` TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `order_details` (
    `order_id` INT UNSIGNED NOT NULL,
    `product_id` VARCHAR(36) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL,
    `purchase_unit_price` DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (`order_id`, `product_id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_id` INT UNSIGNED NOT NULL,
    `product_id` VARCHAR(36) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `review_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `message` TEXT NOT NULL,
    `sent_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('read', 'unread') NOT NULL DEFAULT 'unread',
    `reference` VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `cart_items` (
    `client_id` INT UNSIGNED NOT NULL,
    `product_id` VARCHAR(36) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`client_id`, `product_id`),
    FOREIGN KEY (`client_id`) REFERENCES `clients` (`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
);