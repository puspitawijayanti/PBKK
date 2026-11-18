-- ShopEase - db_shopease (struktur + data awal)
-- Dibuat dari migrasi CodeIgniter 4 + DatabaseSeeder. Kompatibel MySQL 5.7+/8 dan MariaDB 10.4+.
-- Impor: mysql -u root < db_shopease.sql   (atau menu Import di phpMyAdmin)
-- Akun demo: admin@shopease.test / Admin123!  | operator@shopease.test / Operator123!
--            customer1@shopease.test & customer2@shopease.test / Customer123!
-- File bukti bayar contoh ada di writable/uploads/payments/ (nama file sesuai tabel payments).
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `db_shopease` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `db_shopease`;
DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES
(1,3,'Budi Santoso','081200000011','Jl. Melati No. 12, RT 03/RW 05, Kebayoran Baru','Jakarta Selatan','DKI Jakarta','12120',1,'2026-11-18 21:24:03','2026-11-18 21:24:03'),
(2,3,'Budi Santoso (Kantor)','081200000011','Gedung Contoh Lt. 5, Jl. Sudirman Kav. 10','Jakarta Pusat','DKI Jakarta','10220',0,'2026-11-18 21:24:03','2026-11-18 21:24:03'),
(3,4,'Siti Rahma','081200000012','Jl. Kenanga No. 7, Setiabudi','Bandung','Jawa Barat','40391',1,'2026-11-18 21:24:03','2026-11-18 21:24:03');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `quantity` int(11) unsigned NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_product` (`cart_id`,`product_id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES
(1,3,'2026-11-18 21:24:03','2026-11-18 21:24:03'),
(2,4,'2026-11-18 21:24:03','2026-11-18 21:24:03');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,'Elektronik','elektronik','Gadget dan perangkat elektronik harian.',1,'2026-11-18 21:24:03','2026-11-18 21:24:03'),
(2,'Fashion','fashion','Pakaian dan alas kaki untuk gaya sehari-hari.',1,'2026-11-18 21:24:03','2026-11-18 21:24:03'),
(3,'Aksesoris','aksesoris','Pelengkap penampilan dan kebutuhan harian.',1,'2026-11-18 21:24:03','2026-11-18 21:24:03'),
(4,'Rumah Tangga','rumah-tangga','Perlengkapan rumah dan dapur.',1,'2026-11-18 21:24:03','2026-11-18 21:24:03');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(64,'2026-11-01-000001','App\\Database\\Migrations\\CreateUsersTable','default','App',1795011843,1),
(65,'2026-11-01-000002','App\\Database\\Migrations\\CreateCategoriesTable','default','App',1795011843,1),
(66,'2026-11-01-000003','App\\Database\\Migrations\\CreateProductsTable','default','App',1795011843,1),
(67,'2026-11-01-000004','App\\Database\\Migrations\\CreateCartsTables','default','App',1795011843,1),
(68,'2026-11-01-000005','App\\Database\\Migrations\\CreateAddressesTable','default','App',1795011843,1),
(69,'2026-11-01-000006','App\\Database\\Migrations\\CreateOrdersTables','default','App',1795011843,1),
(70,'2026-11-01-000007','App\\Database\\Migrations\\CreatePaymentsShipmentsTables','default','App',1795011843,1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES
(1,1,1,'Wireless Headphone',299000.00,1,299000.00),
(2,1,15,'Mug Keramik',45000.00,2,90000.00),
(3,2,6,'Kaos Polos Premium',89000.00,2,178000.00),
(4,2,10,'Topi Baseball',65000.00,1,65000.00),
(5,3,2,'Smart Watch',450000.00,1,450000.00),
(6,4,5,'Sneakers Casual',350000.00,1,350000.00),
(7,4,13,'Botol Minum Stainless',85000.00,1,85000.00),
(8,5,3,'Power Bank 20000 mAh',235000.00,1,235000.00),
(9,6,7,'Jaket Hoodie',245000.00,1,245000.00),
(10,7,14,'Lampu Meja LED',120000.00,1,120000.00),
(11,7,15,'Mug Keramik',45000.00,1,45000.00),
(12,8,4,'Speaker Bluetooth Mini',275000.00,1,275000.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
DROP TABLE IF EXISTS `order_status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `old_status` varchar(30) DEFAULT NULL,
  `new_status` varchar(30) NOT NULL,
  `changed_by` int(11) unsigned DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_status_logs_changed_by_foreign` (`changed_by`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_status_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `order_status_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `order_status_logs` DISABLE KEYS */;
INSERT INTO `order_status_logs` VALUES
(1,1,NULL,'pending_payment',3,'Pesanan dibuat','2026-11-06 10:00:00'),
(2,1,'pending_payment','waiting_verification',3,'Bukti pembayaran diunggah','2026-11-06 10:30:00'),
(3,1,'waiting_verification','processing',1,'Pembayaran diverifikasi','2026-11-06 11:00:00'),
(4,1,'processing','shipped',2,'Dikirim via JNE, resi JNE0001000037','2026-11-06 11:30:00'),
(5,1,'shipped','completed',3,'Pesanan diterima/selesai','2026-11-06 12:00:00'),
(6,2,NULL,'pending_payment',4,'Pesanan dibuat','2026-11-09 10:00:00'),
(7,2,'pending_payment','waiting_verification',4,'Bukti pembayaran diunggah','2026-11-09 10:30:00'),
(8,2,'waiting_verification','processing',1,'Pembayaran diverifikasi','2026-11-09 11:00:00'),
(9,2,'processing','shipped',2,'Dikirim via JNE, resi JNE0001000074','2026-11-09 11:30:00'),
(10,2,'shipped','completed',4,'Pesanan diterima/selesai','2026-11-09 12:00:00'),
(11,3,NULL,'pending_payment',3,'Pesanan dibuat','2026-11-11 10:00:00'),
(12,3,'pending_payment','waiting_verification',3,'Bukti pembayaran diunggah','2026-11-11 10:30:00'),
(13,3,'waiting_verification','processing',1,'Pembayaran diverifikasi','2026-11-11 11:00:00'),
(14,3,'processing','shipped',2,'Dikirim via JNE, resi JNE0001000111','2026-11-11 11:30:00'),
(15,3,'shipped','completed',3,'Pesanan diterima/selesai','2026-11-11 12:00:00'),
(16,4,NULL,'pending_payment',4,'Pesanan dibuat','2026-11-13 10:00:00'),
(17,4,'pending_payment','waiting_verification',4,'Bukti pembayaran diunggah','2026-11-13 10:30:00'),
(18,4,'waiting_verification','processing',1,'Pembayaran diverifikasi','2026-11-13 11:00:00'),
(19,4,'processing','shipped',2,'Dikirim via JNE, resi JNE0001000148','2026-11-13 11:30:00'),
(20,5,NULL,'pending_payment',3,'Pesanan dibuat','2026-11-14 10:00:00'),
(21,5,'pending_payment','waiting_verification',3,'Bukti pembayaran diunggah','2026-11-14 10:30:00'),
(22,5,'waiting_verification','processing',1,'Pembayaran diverifikasi','2026-11-14 11:00:00'),
(23,6,NULL,'pending_payment',4,'Pesanan dibuat','2026-11-15 10:00:00'),
(24,6,'pending_payment','cancelled',4,'Dibatalkan oleh customer','2026-11-15 10:30:00'),
(25,7,NULL,'pending_payment',3,'Pesanan dibuat','2026-11-17 10:00:00'),
(26,7,'pending_payment','waiting_verification',3,'Bukti pembayaran diunggah','2026-11-17 10:30:00'),
(27,8,NULL,'pending_payment',4,'Pesanan dibuat','2026-11-18 10:00:00');
/*!40000 ALTER TABLE `order_status_logs` ENABLE KEYS */;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `address_id` int(11) unsigned DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `order_date` datetime NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `order_status` varchar(30) NOT NULL DEFAULT 'pending_payment',
  `shipping_address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `orders_address_id_foreign` (`address_id`),
  KEY `user_id` (`user_id`),
  KEY `order_status` (`order_status`),
  KEY `order_date` (`order_date`),
  CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(1,3,1,'SE-20261118-C666E8','2026-11-06 10:00:00',389000.00,15000.00,404000.00,'completed','Budi Santoso (081200000011) - Jl. Melati No. 12, RT 03/RW 05, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12120',NULL,'2026-11-06 10:00:00','2026-11-06 11:00:00'),
(2,4,3,'SE-20261118-7D7258','2026-11-09 10:00:00',243000.00,15000.00,258000.00,'completed','Siti Rahma (081200000012) - Jl. Kenanga No. 7, Setiabudi, Bandung, Jawa Barat 40391',NULL,'2026-11-09 10:00:00','2026-11-09 11:00:00'),
(3,3,1,'SE-20261118-5C6482','2026-11-11 10:00:00',450000.00,15000.00,465000.00,'completed','Budi Santoso (081200000011) - Jl. Melati No. 12, RT 03/RW 05, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12120',NULL,'2026-11-11 10:00:00','2026-11-11 11:00:00'),
(4,4,3,'SE-20261118-0E0B83','2026-11-13 10:00:00',435000.00,15000.00,450000.00,'shipped','Siti Rahma (081200000012) - Jl. Kenanga No. 7, Setiabudi, Bandung, Jawa Barat 40391',NULL,'2026-11-13 10:00:00','2026-11-13 11:00:00'),
(5,3,1,'SE-20261118-A7C232','2026-11-14 10:00:00',235000.00,15000.00,250000.00,'processing','Budi Santoso (081200000011) - Jl. Melati No. 12, RT 03/RW 05, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12120',NULL,'2026-11-14 10:00:00','2026-11-14 11:00:00'),
(6,4,3,'SE-20261118-AF7B84','2026-11-15 10:00:00',245000.00,15000.00,260000.00,'cancelled','Siti Rahma (081200000012) - Jl. Kenanga No. 7, Setiabudi, Bandung, Jawa Barat 40391',NULL,'2026-11-15 10:00:00','2026-11-15 11:00:00'),
(7,3,1,'SE-20261118-FE2042','2026-11-17 10:00:00',165000.00,15000.00,180000.00,'waiting_verification','Budi Santoso (081200000011) - Jl. Melati No. 12, RT 03/RW 05, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12120',NULL,'2026-11-17 10:00:00','2026-11-17 11:00:00'),
(8,4,3,'SE-20261118-9A3BF1','2026-11-18 10:00:00',275000.00,15000.00,290000.00,'pending_payment','Siti Rahma (081200000012) - Jl. Kenanga No. 7, Setiabudi, Bandung, Jawa Barat 40391',NULL,'2026-11-18 10:00:00','2026-11-18 11:00:00');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `method` varchar(50) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `proof_file` varchar(255) DEFAULT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'unpaid',
  `note` varchar(255) DEFAULT NULL,
  `verified_by` int(11) unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  KEY `payment_status` (`payment_status`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,1,'bank_transfer',404000.00,'seed-proof-53365031.png','verified',NULL,1,'2026-11-18 21:24:03','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(2,2,'bank_transfer',258000.00,'seed-proof-b68a6189.png','verified',NULL,1,'2026-11-18 21:24:03','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(3,3,'bank_transfer',465000.00,'seed-proof-175500e9.png','verified',NULL,1,'2026-11-18 21:24:03','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(4,4,'bank_transfer',450000.00,'seed-proof-aca466cc.png','verified',NULL,1,'2026-11-18 21:24:04','2026-11-18 21:24:04','2026-11-18 21:24:04'),
(5,5,'bank_transfer',250000.00,'seed-proof-132a69a0.png','verified',NULL,1,'2026-11-18 21:24:04','2026-11-18 21:24:04','2026-11-18 21:24:04'),
(6,6,'bank_transfer',260000.00,NULL,'unpaid',NULL,NULL,NULL,'2026-11-18 21:24:04','2026-11-18 21:24:04'),
(7,7,'bank_transfer',180000.00,'seed-proof-ed283009.png','submitted',NULL,NULL,NULL,'2026-11-18 21:24:04','2026-11-18 21:24:04'),
(8,8,'bank_transfer',290000.00,NULL,'unpaid',NULL,NULL,NULL,'2026-11-18 21:24:04','2026-11-18 21:24:04');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) unsigned NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `name` (`name`),
  KEY `status` (`status`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(1,1,'Wireless Headphone','wireless-headphone','Headphone Bluetooth 5.3 dengan noise reduction dan baterai hingga 30 jam.',299000.00,19,'seed-wireless-headphone.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(2,1,'Smart Watch','smart-watch','Jam tangan digital dengan pemantau detak jantung, langkah, dan notifikasi.',450000.00,9,'seed-smart-watch.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(3,1,'Power Bank 20000 mAh','power-bank-20000-mah','Power bank fast charging dengan dua port USB dan satu USB-C.',235000.00,24,'seed-power-bank-20000-mah.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:04'),
(4,1,'Speaker Bluetooth Mini','speaker-bluetooth-mini','Speaker portabel tahan percikan air dengan suara jernih dan bass kuat.',275000.00,13,'seed-speaker-bluetooth-mini.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:04'),
(5,2,'Sneakers Casual','sneakers-casual','Sepatu casual pria yang nyaman untuk aktivitas sehari-hari.',350000.00,14,'seed-sneakers-casual.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:04'),
(6,2,'Kaos Polos Premium','kaos-polos-premium','Kaos katun combed 30s, adem, lembut, dan tidak mudah melar.',89000.00,38,'seed-kaos-polos-premium.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(7,2,'Jaket Hoodie','jaket-hoodie','Hoodie fleece tebal dengan kantong depan dan tali serut.',245000.00,18,'seed-jaket-hoodie.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:04'),
(8,2,'Celana Chino','celana-chino','Celana chino slim fit dengan bahan stretch yang nyaman.',199000.00,22,'seed-celana-chino.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(9,3,'Tas Ransel Urban','tas-ransel-urban','Ransel anti air dengan slot laptop 15 inci dan banyak kompartemen.',275000.00,12,'seed-tas-ransel-urban.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(10,3,'Topi Baseball','topi-baseball','Topi baseball bordir dengan strap belakang yang dapat diatur.',65000.00,34,'seed-topi-baseball.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(11,3,'Dompet Kulit','dompet-kulit','Dompet kulit sintetis premium dengan banyak slot kartu.',129000.00,3,'seed-dompet-kulit.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(12,3,'Kacamata Hitam','kacamata-hitam','Kacamata hitam lensa polarized dengan proteksi UV400.',150000.00,17,'seed-kacamata-hitam.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(13,4,'Botol Minum Stainless','botol-minum-stainless','Botol minum 750 ml menjaga suhu dingin 24 jam dan panas 12 jam.',85000.00,29,'seed-botol-minum-stainless.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:04'),
(14,4,'Lampu Meja LED','lampu-meja-led','Lampu belajar LED dengan 3 tingkat kecerahan dan port USB.',120000.00,15,'seed-lampu-meja-led.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:04'),
(15,4,'Mug Keramik','mug-keramik','Mug keramik 350 ml, aman untuk microwave dan mesin cuci piring.',45000.00,47,'seed-mug-keramik.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:04'),
(16,4,'Set Tempat Makan','set-tempat-makan','Set tempat makan 3 sekat bebas BPA dengan penutup rapat.',175000.00,0,'seed-set-tempat-makan.jpg','active','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(17,1,'Earphone Kabel Lama','earphone-kabel-lama','Produk lama yang sudah tidak dijual (contoh produk nonaktif).',59000.00,8,'seed-earphone-kabel-lama.jpg','inactive','2026-11-18 21:24:03','2026-11-18 21:24:03');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
DROP TABLE IF EXISTS `shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `courier` varchar(100) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipment_status` varchar(30) NOT NULL DEFAULT 'pending',
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `tracking_number` (`tracking_number`),
  CONSTRAINT `shipments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `shipments` DISABLE KEYS */;
INSERT INTO `shipments` VALUES
(1,1,'JNE','JNE0001000037','delivered','2026-11-18 21:24:03','2026-11-18 21:24:03','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(2,2,'JNE','JNE0001000074','delivered','2026-11-18 21:24:03','2026-11-18 21:24:03','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(3,3,'JNE','JNE0001000111','delivered','2026-11-18 21:24:03','2026-11-18 21:24:03','2026-11-18 21:24:03','2026-11-18 21:24:03'),
(4,4,'JNE','JNE0001000148','shipped','2026-11-18 21:24:04',NULL,'2026-11-18 21:24:04','2026-11-18 21:24:04');
/*!40000 ALTER TABLE `shipments` ENABLE KEYS */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Administrator ShopEase','admin@shopease.test','$2y$10$nmyg0/HlmbK9BLXAWvOZ.Ou3MMdPJYH5UI.X36.9PH5T.CiZE/kR6','admin','081200000001',1,'2026-11-18 21:24:03','2026-11-18 21:24:03'),
(2,'Operator Toko','operator@shopease.test','$2y$10$SGtwbHqw.HXKSwRVtrVX6eTaE.6JVlDKhHwQrCbcsxvJ5HhAxpbwa','operator','081200000002',1,'2026-11-18 21:24:03','2026-11-18 21:24:03'),
(3,'Budi Santoso','customer1@shopease.test','$2y$10$ZQ9Av1vPA7WNgfCzRRm13uK9Q.OrC6Zy3uai5LpYYmiGL26AB9.Ui','customer','081200000011',1,'2026-11-18 21:24:03','2026-11-18 21:24:03'),
(4,'Siti Rahma','customer2@shopease.test','$2y$10$N6J7LfTJh5H.sNufGJY/cevaBjaGYEckQaPadqQrge3lNDupMTm16','customer','081200000012',1,'2026-11-18 21:24:03','2026-11-18 21:24:03');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

