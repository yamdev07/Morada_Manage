-- ============================================================
--  MORADA LODGE : mise a jour de la base, a executer a la main
-- ============================================================
--  A utiliser quand `php artisan migrate` n'est pas accessible
--  (hebergement sans SSH, fichiers PHP autonomes non servis).
--
--  COMMENT FAIRE
--    1. phpMyAdmin > votre base > onglet SQL
--    2. Coller TOUT ce fichier
--    3. Executer
--
--  FAITES UNE SAUVEGARDE AVANT (onglet Exporter).
--
--  Relancable sans risque : chaque etape verifie d'abord si la
--  colonne ou la table existe deja. Aucune procedure stockee,
--  aucune commande DELIMITER : compatible hebergement mutualise.
-- ============================================================

-- ============================================================
--  ETAPE 1 : menus
--  `is_available` est la colonne qui fait planter la page d'accueil.
-- ============================================================

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'menus' AND COLUMN_NAME = 'is_available');
SET @s := IF(@x = 0,
    'ALTER TABLE `menus` ADD COLUMN `is_available` TINYINT(1) NOT NULL DEFAULT 1 AFTER `image`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'menus' AND COLUMN_NAME = 'deleted_at');
SET @s := IF(@x = 0,
    'ALTER TABLE `menus` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;


-- ============================================================
--  ETAPE 2 : commandes du restaurant
-- ============================================================

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'order_number');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `order_number` VARCHAR(255) NULL DEFAULT NULL AFTER `id`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'guest_name');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `guest_name` VARCHAR(255) NULL DEFAULT NULL AFTER `customer_id`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'transaction_id');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `transaction_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `room_id`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'order_type');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `order_type` VARCHAR(255) NOT NULL DEFAULT ''dining'' AFTER `transaction_id`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'table_number');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `table_number` VARCHAR(255) NULL DEFAULT NULL AFTER `order_type`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'notes');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `notes` TEXT NULL DEFAULT NULL AFTER `status`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'payment_method');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `payment_method` VARCHAR(255) NULL DEFAULT NULL AFTER `notes`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'paid_at');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `paid_at` TIMESTAMP NULL DEFAULT NULL AFTER `payment_method`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'served_at');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `served_at` TIMESTAMP NULL DEFAULT NULL AFTER `paid_at`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders' AND COLUMN_NAME = 'created_by');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD COLUMN `created_by` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `served_at`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;


-- Numeroter les commandes deja enregistrees
UPDATE `restaurant_orders`
SET `order_number` = CONCAT('CMD-', LPAD(`id`, 5, '0'))
WHERE `order_number` IS NULL OR `order_number` = '';

-- Index unique sur le numero
SET @x := (SELECT COUNT(*) FROM information_schema.STATISTICS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_orders'
             AND INDEX_NAME = 'restaurant_orders_order_number_unique');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_orders` ADD UNIQUE `restaurant_orders_order_number_unique` (`order_number`)',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ============================================================
--  ETAPE 3 : reservations de table
-- ============================================================

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_reservations' AND COLUMN_NAME = 'email');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_reservations` ADD COLUMN `email` VARCHAR(255) NULL DEFAULT NULL AFTER `phone`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_reservations' AND COLUMN_NAME = 'source');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_reservations` ADD COLUMN `source` VARCHAR(255) NOT NULL DEFAULT ''site'' AFTER `status`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'restaurant_reservations' AND COLUMN_NAME = 'handled_by');
SET @s := IF(@x = 0,
    'ALTER TABLE `restaurant_reservations` ADD COLUMN `handled_by` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `source`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;


ALTER TABLE `restaurant_reservations`
    MODIFY `status` ENUM('pending','confirmed','seated','completed','cancelled','no_show')
    NOT NULL DEFAULT 'pending';

-- ============================================================
--  ETAPE 4 : categories de la carte
-- ============================================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `sort_order` INT(11) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @s := 'SELECT 1';
SET @x := (SELECT COUNT(*) FROM information_schema.COLUMNS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'menus' AND COLUMN_NAME = 'category_id');
SET @s := IF(@x = 0,
    'ALTER TABLE `menus` ADD COLUMN `category_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `name`',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;


-- Reprendre les categories saisies en texte, si l'ancienne colonne existe encore
SET @aCat := (SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'menus' AND COLUMN_NAME = 'category');

SET @s := IF(@aCat > 0,
    "INSERT IGNORE INTO `categories` (`name`, `slug`, `sort_order`, `created_at`, `updated_at`)
     SELECT DISTINCT
        CASE `category`
            WHEN 'entree'  THEN 'Entrees'
            WHEN 'plat'    THEN 'Plats'
            WHEN 'dessert' THEN 'Desserts'
            WHEN 'boisson' THEN 'Boissons'
            ELSE CONCAT(UPPER(LEFT(`category`,1)), SUBSTRING(`category`,2))
        END,
        LOWER(`category`), 0, NOW(), NOW()
     FROM `menus` WHERE `category` IS NOT NULL AND `category` <> ''",
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s := IF(@aCat > 0,
    "UPDATE `menus` m JOIN `categories` c ON c.`slug` = LOWER(m.`category`)
     SET m.`category_id` = c.`id` WHERE m.`category_id` IS NULL",
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- Cle etrangere
SET @x := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'menus'
             AND CONSTRAINT_NAME = 'menus_category_id_foreign');
SET @s := IF(@x = 0,
    'ALTER TABLE `menus` ADD CONSTRAINT `menus_category_id_foreign`
     FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL',
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- Retirer l'ancienne colonne texte, les donnees sont transferees
SET @s := IF(@aCat > 0, 'ALTER TABLE `menus` DROP COLUMN `category`', 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ============================================================
--  ETAPE 5 : marquer les migrations comme faites
--  Sans ca, un futur `php artisan migrate` tenterait de les rejouer.
-- ============================================================
SET @lot := (SELECT IFNULL(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_23_090000_upgrade_restaurant_module', @lot FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `migrations`
                  WHERE `migration` = '2026_09_23_090000_upgrade_restaurant_module');

SET @lot := (SELECT IFNULL(MAX(`batch`), 0) FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_23_120000_create_restaurant_categories', @lot FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `migrations`
                  WHERE `migration` = '2026_09_23_120000_create_restaurant_categories');

-- ============================================================
--  TERMINE
--  Videz ensuite le cache des vues : supprimez par FTP le contenu de
--  storage/framework/views/ (gardez le fichier .gitignore).
-- ============================================================
SELECT 'Mise a jour terminee' AS resultat;
