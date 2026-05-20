-- ============================================================
-- Misaki Notification System Migration
-- Run this in phpMyAdmin or MySQL CLI
-- ============================================================

-- 1. Admin notifications table (User → Admin: new order alerts)
CREATE TABLE IF NOT EXISTS `admin_notification` (
  `notif_id`   int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`   int(10) UNSIGNED NOT NULL,
  `message`    varchar(255) NOT NULL,
  `is_read`    tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notif_id`),
  KEY `fk_adminnotif_order` (`order_id`),
  CONSTRAINT `fk_adminnotif_order` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. User notifications table (Admin → User: "Ready for Pick up")
CREATE TABLE IF NOT EXISTS `user_notification` (
  `notif_id`   int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    int(10) UNSIGNED NOT NULL,
  `order_id`   int(10) UNSIGNED DEFAULT NULL,
  `message`    varchar(255) NOT NULL,
  `is_read`    tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notif_id`),
  KEY `fk_usernotif_user`  (`user_id`),
  KEY `fk_usernotif_order` (`order_id`),
  CONSTRAINT `fk_usernotif_user`  FOREIGN KEY (`user_id`)  REFERENCES `user`  (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_usernotif_order` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
