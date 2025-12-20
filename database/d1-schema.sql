-- D1 Compatible Schema
-- Generated based on Laravel migrations

-- Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `email` TEXT NOT NULL UNIQUE,
    `email_verified_at` TEXT,
    `password` TEXT NOT NULL,
    `remember_token` TEXT,
    `role` TEXT DEFAULT 'User',
    `image` TEXT,
    `status` TEXT DEFAULT 'Active',
    `created_at` TEXT,
    `updated_at` TEXT
);

-- Password Reset Tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` TEXT PRIMARY KEY,
    `token` TEXT NOT NULL,
    `created_at` TEXT
);

-- Sessions Table
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` TEXT PRIMARY KEY,
    `user_id` INTEGER,
    `ip_address` TEXT,
    `user_agent` TEXT,
    `payload` TEXT NOT NULL,
    `last_activity` INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS `sessions_user_id_index` ON `sessions` (`user_id`);
CREATE INDEX IF NOT EXISTS `sessions_last_activity_index` ON `sessions` (`last_activity`);

-- Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `description` TEXT,
    `image` TEXT,
    `status` TEXT DEFAULT 'Active',
    `created_at` TEXT,
    `updated_at` TEXT
);

-- Products Table
CREATE TABLE IF NOT EXISTS `products` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `description` TEXT,
    `price` REAL NOT NULL,
    `stock` INTEGER DEFAULT 0,
    `category_id` INTEGER NOT NULL,
    `status` TEXT DEFAULT 'Active',
    `image` TEXT,
    `created_at` TEXT,
    `updated_at` TEXT,
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
);

-- Sliders Table
CREATE TABLE IF NOT EXISTS `sliders` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `title` TEXT NOT NULL,
    `description` TEXT,
    `image` TEXT NOT NULL,
    `status` INTEGER DEFAULT 1,
    `created_at` TEXT,
    `updated_at` TEXT
);

-- Orders Table
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `user_id` INTEGER NOT NULL,
    `total_amount` REAL NOT NULL,
    `status` TEXT DEFAULT 'Pending',
    `payment_method` TEXT DEFAULT 'Cash',
    `created_at` TEXT,
    `updated_at` TEXT,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `order_id` INTEGER NOT NULL,
    `product_id` INTEGER NOT NULL,
    `quantity` INTEGER NOT NULL,
    `price` REAL NOT NULL,
    `created_at` TEXT,
    `updated_at` TEXT,
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
);

-- Jobs Table
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `queue` TEXT NOT NULL,
    `payload` TEXT NOT NULL,
    `attempts` INTEGER NOT NULL,
    `reserved_at` INTEGER,
    `available_at` INTEGER NOT NULL,
    `created_at` INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS `jobs_queue_index` ON `jobs` (`queue`);

-- Job Batches Table
CREATE TABLE IF NOT EXISTS `job_batches` (
    `id` TEXT PRIMARY KEY,
    `name` TEXT NOT NULL,
    `total_jobs` INTEGER NOT NULL,
    `pending_jobs` INTEGER NOT NULL,
    `failed_jobs` INTEGER NOT NULL,
    `failed_job_ids` TEXT NOT NULL,
    `options` TEXT,
    `cancelled_at` INTEGER,
    `created_at` INTEGER NOT NULL,
    `finished_at` INTEGER
);

-- Failed Jobs Table
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `uuid` TEXT NOT NULL UNIQUE,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` TEXT NOT NULL,
    `exception` TEXT NOT NULL,
    `failed_at` TEXT DEFAULT (datetime('now'))
);

-- Cache Table
CREATE TABLE IF NOT EXISTS `cache` (
    `key` TEXT PRIMARY KEY,
    `value` TEXT NOT NULL,
    `expiration` INTEGER NOT NULL
);

-- Cache Locks Table
CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` TEXT PRIMARY KEY,
    `owner` TEXT NOT NULL,
    `expiration` INTEGER NOT NULL
);
