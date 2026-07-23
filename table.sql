/* students */
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    picture VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

/* content_categories */
CREATE TABLE `content_categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` CHAR(32) NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `color` VARCHAR(30) DEFAULT '#2563eb',
    `icon` VARCHAR(100) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_content_categories_uuid` (`uuid`),
    UNIQUE KEY `uk_content_categories_title` (`title`),
    KEY `idx_content_categories_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* social_accounts */
CREATE TABLE `social_accounts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` CHAR(32) NOT NULL,
    `platform` ENUM('Facebook', 'Instagram', 'X', 'LinkedIn', 'YouTube', 'Threads') NOT NULL,
    `account_name` VARCHAR(255) NOT NULL,
    `account_username` VARCHAR(255) NULL,
    `account_email` VARCHAR(255) NULL,
    `account_url` VARCHAR(500) NULL,
    `page_name` VARCHAR(255) NULL,
    `page_url` VARCHAR(500) NULL,
    `access_token` LONGTEXT NULL,
    `refresh_token` LONGTEXT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_social_accounts_uuid` (`uuid`),
    KEY `idx_platform` (`platform`),
    KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* contents */
CREATE TABLE `contents` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` CHAR(36) NOT NULL,
    `content_category_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `original_text` LONGTEXT NOT NULL,
    `image_path` VARCHAR(255) NULL,
    `ai_summary` LONGTEXT NULL,
    `uploaded_by` BIGINT UNSIGNED NULL,
    `status` ENUM('Pending', 'Processing', 'Completed', 'Failed') NOT NULL DEFAULT 'Pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_contents_uuid` (`uuid`),
    KEY `idx_content_category` (`content_category_id`),
    KEY `idx_status` (`status`),
    KEY `idx_uploaded_by` (`uploaded_by`),
    CONSTRAINT `fk_contents_category`
        FOREIGN KEY (`content_category_id`)
        REFERENCES `content_categories`(`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* social_posts */
CREATE TABLE social_posts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,
    caption LONGTEXT NOT NULL,
    image_path VARCHAR(255) NULL,
    video_path VARCHAR(255) NULL,
    hashtags TEXT NULL,
    keywords TEXT NULL,
    status ENUM('Draft', 'Ready', 'Published') NOT NULL DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uk_social_posts_uuid (uuid),
    KEY idx_status (status)
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

/* social_post_contents */
CREATE TABLE social_post_contents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    social_post_id BIGINT UNSIGNED NOT NULL,
    content_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_social_post (social_post_id),
    KEY idx_content (content_id),

    CONSTRAINT fk_spc_post
        FOREIGN KEY (social_post_id)
        REFERENCES social_posts(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_spc_content
        FOREIGN KEY (content_id)
        REFERENCES contents(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

/* users */
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    sys_id VARCHAR(36) NOT NULL UNIQUE,
    user_type ENUM('Person','Company') NOT NULL,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(30) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    picture VARCHAR(255) DEFAULT NULL,
    email_verified_at DATETIME DEFAULT NULL,
    last_login_at DATETIME DEFAULT NULL,
    status ENUM('Active','Inactive','Blocked') NOT NULL DEFAULT 'Active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_sys_id (sys_id),
    INDEX idx_status (status),
    INDEX idx_user_type (user_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Default Categories */
INSERT INTO content_categories (uuid, title, description, color, icon, is_active)
VALUES
    (MD5(UUID()), 'Trending', 'Trending marketing contents', '#ef4444', 'fire', 1),
    (MD5(UUID()), 'Offer', 'Offers & Discounts', '#10b981', 'tag', 1),
    (MD5(UUID()), 'Educational', 'Educational contents', '#3b82f6', 'book-open', 1),
    (MD5(UUID()), 'Awareness', 'Awareness campaigns', '#8b5cf6', 'megaphone', 1),
    (MD5(UUID()), 'News', 'Latest news', '#f59e0b', 'newspaper', 1),
    (MD5(UUID()), 'Others', 'Miscellaneous', '#6b7280', 'folder', 1);