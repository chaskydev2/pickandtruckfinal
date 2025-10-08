-- MySQL dump 10.13  Distrib 8.3.0, for macos12.6 (x86_64)
--
-- Host: localhost    Database: pickn6
-- ------------------------------------------------------
-- Server version	8.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `administrators`
--

DROP TABLE IF EXISTS `administrators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administrators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','editor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'editor',
  PRIMARY KEY (`id`),
  UNIQUE KEY `administrators_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administrators`
--

LOCK TABLES `administrators` WRITE;
/*!40000 ALTER TABLE `administrators` DISABLE KEYS */;
INSERT INTO `administrators` VALUES (2,'mario','marioreque81@gmail.com',NULL,'$2y$12$pd89VUep/akla1YJwNRerui6kr2zKL2diGx3hRQpvUEmdqf22vY/m',1,NULL,'2025-02-18 22:55:28','2025-02-18 22:55:28','admin'),(3,'mario','chaskyceo@gmail.com',NULL,'$2y$12$MygPU6jW/qIRZw9Gcms8l.0VSkGuaWs3tmLOTTYzArx4fUVLbnqfq',1,NULL,'2025-03-02 21:28:36','2025-03-02 21:28:36','admin'),(4,'admin','admin@admin.com',NULL,'$2y$12$gBZ/UB47LTjVTrkHUDNsrOHkiSSE7U1LGpBpbgG2dsLKX0zRfn0L.',1,NULL,'2025-03-02 22:29:41','2025-03-02 22:29:41','admin');
/*!40000 ALTER TABLE `administrators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bids`
--

DROP TABLE IF EXISTS `bids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bids` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `bideable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bideable_id` bigint unsigned NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `comentario` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('pendiente','aceptado','rechazado','en_proceso','finalizado','terminado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bids_bideable_type_bideable_id_index` (`bideable_type`,`bideable_id`),
  KEY `bids_user_id_foreign` (`user_id`),
  CONSTRAINT `bids_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bids`
--

LOCK TABLES `bids` WRITE;
/*!40000 ALTER TABLE `bids` DISABLE KEYS */;
INSERT INTO `bids` VALUES (2,6,'App\\Models\\OfertaCarga',2,1000.00,'2025-02-27 00:00:00',NULL,'terminado','2025-02-26 17:27:06','2025-02-27 19:35:56'),(3,6,'App\\Models\\OfertaRuta',3,1400.00,'3333-02-12 00:00:00',NULL,'rechazado','2025-02-26 23:46:42','2025-02-27 00:04:08'),(4,6,'App\\Models\\OfertaCarga',1,213123.00,'2025-02-26 20:17:14',NULL,'pendiente','2025-02-27 00:17:14','2025-02-27 00:17:14'),(5,6,'App\\Models\\OfertaRuta',2,213213.00,'2025-02-26 22:16:38',NULL,'terminado','2025-02-27 02:16:38','2025-02-27 19:05:15'),(6,6,'App\\Models\\OfertaRuta',4,231.85,'2025-02-27 15:23:11',NULL,'aceptado','2025-02-27 19:23:11','2025-02-27 19:23:24'),(7,4,'App\\Models\\OfertaRuta',5,2132.57,'2025-03-05 13:22:36',NULL,'aceptado','2025-03-05 17:22:36','2025-03-06 04:51:17'),(8,6,'App\\Models\\OfertaCarga',3,2323.00,'2025-03-05 13:37:26',NULL,'pendiente','2025-03-05 17:37:26','2025-03-05 17:37:26'),(9,6,'App\\Models\\OfertaRuta',6,231.83,'2025-03-05 13:38:14',NULL,'terminado','2025-03-05 17:38:14','2025-03-05 17:57:01'),(10,6,'App\\Models\\OfertaRuta',7,213123.00,'2025-03-05 13:58:26',NULL,'aceptado','2025-03-05 17:58:26','2025-03-05 23:07:32'),(11,4,'App\\Models\\OfertaCarga',4,21321.00,'2025-03-05 14:11:16',NULL,'terminado','2025-03-05 18:11:16','2025-03-05 23:07:20'),(12,4,'App\\Models\\OfertaRuta',8,23123.00,'2025-03-05 17:57:02',NULL,'pendiente','2025-03-05 21:57:02','2025-03-05 21:57:02'),(13,6,'App\\Models\\OfertaCarga',5,21313.00,'2025-03-05 20:33:35',NULL,'aceptado','2025-03-06 00:33:35','2025-03-06 00:33:55'),(14,4,'App\\Models\\OfertaCarga',3,2323.00,'2025-03-05 23:32:22',NULL,'pendiente','2025-03-06 03:32:22','2025-03-06 03:32:22'),(15,4,'App\\Models\\OfertaRuta',9,123123.00,'2025-03-06 00:00:00',NULL,'pendiente','2025-03-06 04:05:50','2025-03-06 04:24:12'),(16,6,'App\\Models\\OfertaRuta',10,21311.90,'2026-03-23 00:00:00',NULL,'pendiente','2025-03-06 04:50:12','2025-03-06 04:50:12'),(17,6,'App\\Models\\OfertaCarga',7,30000.00,'2026-03-18 00:00:00','comentario de prueba','en_proceso','2025-03-06 05:15:57','2025-03-06 05:26:20'),(18,7,'App\\Models\\OfertaCarga',8,12313.00,'2023-02-23 00:00:00','s','aceptado','2025-03-06 08:36:28','2025-03-06 12:16:23'),(19,4,'App\\Models\\OfertaRuta',11,2323.00,'3321-02-23 00:00:00',NULL,'aceptado','2025-03-06 08:58:35','2025-03-06 10:36:18'),(20,6,'App\\Models\\OfertaCarga',9,1313.00,'3344-02-23 00:00:00',NULL,'aceptado','2025-03-06 09:01:26','2025-03-06 10:24:19'),(21,4,'App\\Models\\OfertaCarga',9,1313.00,'3344-02-23 00:00:00',NULL,'pendiente','2025-03-06 09:13:35','2025-03-06 09:13:35'),(22,6,'App\\Models\\OfertaCarga',8,12313.00,'2023-02-23 00:00:00',NULL,'pendiente','2025-03-06 09:26:42','2025-03-06 09:26:42'),(23,7,'App\\Models\\OfertaRuta',12,2323.00,'2323-03-12 00:00:00',NULL,'aceptado','2025-03-06 09:54:44','2025-03-06 10:49:26'),(24,6,'App\\Models\\OfertaRuta',12,2323.00,'2323-03-12 00:00:00',NULL,'pendiente','2025-03-06 09:55:33','2025-03-06 09:55:33'),(25,4,'App\\Models\\OfertaRuta',13,12313.00,'2332-03-23 00:00:00',NULL,'aceptado','2025-03-06 10:15:15','2025-03-06 10:15:48'),(26,6,'App\\Models\\OfertaRuta',11,2323.00,'3321-02-23 00:00:00',NULL,'pendiente','2025-03-06 10:29:59','2025-03-06 10:29:59'),(27,7,'App\\Models\\OfertaRuta',14,213123.00,'2025-03-14 00:00:00',NULL,'aceptado','2025-03-06 10:30:32','2025-03-06 11:03:30'),(28,4,'App\\Models\\OfertaRuta',14,213123.00,'2025-03-14 00:00:00',NULL,'terminado','2025-03-06 10:31:48','2025-03-06 10:49:03'),(29,7,'App\\Models\\OfertaCarga',11,2323.00,'2025-03-20 00:00:00',NULL,'terminado','2025-03-06 10:56:33','2025-03-06 11:08:23'),(30,4,'App\\Models\\OfertaRuta',15,2332.00,'2025-03-14 00:00:00',NULL,'aceptado','2025-03-06 10:57:23','2025-03-06 10:57:44'),(31,7,'App\\Models\\OfertaRuta',16,2112.00,'2333-03-12 00:00:00',NULL,'aceptado','2025-03-06 18:18:07','2025-03-06 18:19:29'),(32,6,'App\\Models\\OfertaRuta',18,300.00,'2025-03-20 00:00:00','x98765432x98765432x98765432x98765432x98765432x98765432x98765432x98765432x98765432x98765432x98765432x98765432x98765432x98765432x98765432','rechazado','2025-03-06 19:40:19','2025-03-06 19:42:24'),(33,4,'App\\Models\\OfertaRuta',18,350.00,'2025-03-20 00:00:00','x98765432x98765432x98765432 x98765432x98765432    x98765432x98765432','terminado','2025-03-06 19:41:56','2025-03-06 19:54:20'),(34,6,'App\\Models\\OfertaRuta',17,99.00,'2025-03-05 00:00:00',NULL,'pendiente','2025-03-06 19:45:59','2025-03-06 19:45:59'),(35,17,'App\\Models\\OfertaCarga',11,2323.00,'2025-03-20 00:00:00',NULL,'pendiente','2025-03-27 09:00:11','2025-03-27 09:00:11'),(36,17,'App\\Models\\OfertaRuta',19,4300.00,'2025-03-12 00:00:00',NULL,'pendiente','2025-03-27 09:22:44','2025-03-27 09:22:44'),(37,4,'App\\Models\\OfertaCarga',12,233.00,'2026-06-12 00:00:00',NULL,'pendiente','2025-03-27 09:33:20','2025-03-27 09:33:20'),(38,17,'App\\Models\\OfertaCarga',15,1231.00,'2025-05-07 00:00:00',NULL,'pendiente','2025-03-27 11:18:10','2025-03-27 11:18:10'),(39,17,'App\\Models\\OfertaRuta',17,345.00,'2025-03-05 00:00:00',NULL,'pendiente','2025-03-27 13:04:22','2025-03-27 13:04:22'),(40,16,'App\\Models\\OfertaCarga',14,123.00,'2025-12-12 00:00:00',NULL,'pendiente','2025-03-27 14:52:20','2025-03-27 14:52:20'),(41,16,'App\\Models\\OfertaCarga',13,23.00,'2025-12-29 00:00:00',NULL,'aceptado','2025-03-27 14:58:26','2025-03-27 14:58:58');
/*!40000 ALTER TABLE `bids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('marioreque@marioreque.com|127.0.0.1','i:1;',1743048939),('marioreque@marioreque.com|127.0.0.1:timer','i:1743048939;',1743048939),('marioreque88@gmail.com|127.0.0.1','i:1;',1743031338),('marioreque88@gmail.com|127.0.0.1:timer','i:1743031338;',1743031338);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargo_types`
--

DROP TABLE IF EXISTS `cargo_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargo_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargo_types`
--

LOCK TABLES `cargo_types` WRITE;
/*!40000 ALTER TABLE `cargo_types` DISABLE KEYS */;
INSERT INTO `cargo_types` VALUES (1,'Carga General','Carga general','2025-02-18 22:46:34','2025-02-18 22:46:34'),(2,'Productos Perecederos','Productos perecederos','2025-02-18 22:46:34','2025-02-18 22:46:34'),(3,'Materiales Peligrosos','Materiales peligrosos','2025-02-18 22:46:34','2025-02-18 22:46:34');
/*!40000 ALTER TABLE `cargo_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bid_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `publicador_id` bigint unsigned DEFAULT NULL,
  `bideante_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chats_bid_id_foreign` (`bid_id`),
  KEY `chats_publicador_id_foreign` (`publicador_id`),
  KEY `chats_bideante_id_foreign` (`bideante_id`),
  CONSTRAINT `chats_bid_id_foreign` FOREIGN KEY (`bid_id`) REFERENCES `bids` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chats_bideante_id_foreign` FOREIGN KEY (`bideante_id`) REFERENCES `users` (`id`),
  CONSTRAINT `chats_publicador_id_foreign` FOREIGN KEY (`publicador_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chats`
--

LOCK TABLES `chats` WRITE;
/*!40000 ALTER TABLE `chats` DISABLE KEYS */;
INSERT INTO `chats` VALUES (4,9,'2025-03-05 17:38:32','2025-03-05 17:38:32',NULL,NULL),(5,2,'2025-03-05 17:57:21','2025-03-05 17:57:21',NULL,NULL),(6,11,'2025-03-05 18:42:48','2025-03-05 18:42:48',NULL,NULL),(7,10,'2025-03-05 23:07:32','2025-03-05 23:07:32',NULL,NULL),(8,13,'2025-03-06 00:33:55','2025-03-06 00:33:55',NULL,NULL),(9,7,'2025-03-06 04:51:17','2025-03-06 04:51:17',NULL,NULL),(10,17,'2025-03-06 05:17:19','2025-03-06 05:17:19',NULL,NULL),(11,18,'2025-03-06 08:36:53','2025-03-06 08:36:53',NULL,NULL),(12,25,'2025-03-06 10:24:00','2025-03-06 10:24:00',NULL,NULL),(13,20,'2025-03-06 10:24:22','2025-03-06 10:24:22',NULL,NULL),(14,19,'2025-03-06 10:36:26','2025-03-06 10:36:26',NULL,NULL),(15,28,'2025-03-06 10:48:21','2025-03-06 10:48:21',NULL,NULL),(16,23,'2025-03-06 10:49:30','2025-03-06 10:49:30',NULL,NULL),(17,29,'2025-03-06 10:57:04','2025-03-06 10:57:04',NULL,NULL),(18,30,'2025-03-06 10:57:47','2025-03-06 10:57:47',NULL,NULL),(19,27,'2025-03-06 11:03:33','2025-03-06 11:03:33',NULL,NULL),(20,31,'2025-03-06 18:19:43','2025-03-06 18:19:43',NULL,NULL),(21,33,'2025-03-06 19:42:50','2025-03-06 19:42:50',NULL,NULL),(22,38,'2025-03-27 11:18:10','2025-03-27 11:18:10',NULL,NULL),(23,39,'2025-03-27 13:04:22','2025-03-27 13:04:22',NULL,NULL),(24,40,'2025-03-27 14:52:21','2025-03-27 14:52:21',NULL,NULL),(25,41,'2025-03-27 14:58:26','2025-03-27 14:58:26',NULL,NULL);
/*!40000 ALTER TABLE `chats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sitio_web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verificada` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empresas_user_id_foreign` (`user_id`),
  CONSTRAINT `empresas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,4,'CHASKY MARKETING','http://localhost:8000/empresas/logos/67e47450daeaf_4_1743025232.jpg','DASDASDASD','13123123123','2312312',NULL,0,'2025-03-27 01:16:16','2025-03-27 01:40:32'),(2,16,'chasky','http://127.0.0.1:8000/empresas/logos/67e48c4aea43f_16_1743031370.png','adasdadsa','iuu909u098098','ssadassad','asdasda',0,'2025-03-27 03:22:26','2025-03-27 03:22:50'),(3,17,'monoridisoso','http://127.0.0.1:8000/empresas/logos/67e491d8ae25d_17_1743032792.png','adasdasd','123123213','asdasdasdas','adasdadasd',0,'2025-03-27 03:46:11','2025-03-27 03:46:32');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (13,'default','{\"uuid\":\"aa3ecc47-0696-43ce-9126-9e2fabd7d2f0\",\"displayName\":\"App\\\\Notifications\\\\BidStatusChanged\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:34:\\\"App\\\\Notifications\\\\BidStatusChanged\\\":3:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:11;s:9:\\\"relations\\\";a:1:{i:0;s:8:\\\"bideable\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:9:\\\"\\u0000*\\u0000status\\\";s:9:\\\"terminado\\\";s:2:\\\"id\\\";s:36:\\\"b94e492c-76fa-4276-b2aa-ac18cdd1d499\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1741201640,1741201640),(14,'default','{\"uuid\":\"45676596-97bf-40e1-9f24-e04da96677d9\",\"displayName\":\"App\\\\Notifications\\\\BidStatusChanged\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:34:\\\"App\\\\Notifications\\\\BidStatusChanged\\\":3:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:11;s:9:\\\"relations\\\";a:1:{i:0;s:8:\\\"bideable\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:9:\\\"\\u0000*\\u0000status\\\";s:9:\\\"terminado\\\";s:2:\\\"id\\\";s:36:\\\"b94e492c-76fa-4276-b2aa-ac18cdd1d499\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"}}',0,NULL,1741201640,1741201640),(15,'default','{\"uuid\":\"548beef3-f986-4ce3-a620-16b94a605bce\",\"displayName\":\"App\\\\Notifications\\\\BidAccepted\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidAccepted\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:2:{i:0;s:8:\\\"bideable\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"4f254be5-bd53-4156-950c-8322fb7b2dd8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1741201652,1741201652),(16,'default','{\"uuid\":\"1c03bbe9-cef1-4cfb-972a-3c815ec6f338\",\"displayName\":\"App\\\\Notifications\\\\BidAccepted\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidAccepted\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:2:{i:0;s:8:\\\"bideable\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"4f254be5-bd53-4156-950c-8322fb7b2dd8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"}}',0,NULL,1741201652,1741201652),(17,'default','{\"uuid\":\"95c1f285-168e-46c9-8fbc-782ecb8afacf\",\"displayName\":\"App\\\\Notifications\\\\BidReceived\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:4;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidReceived\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:13;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"5f25a74f-e78c-4dff-983a-c566cc0c8d07\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1741206815,1741206815),(18,'default','{\"uuid\":\"e00c9dd6-0cfc-46dd-8230-d15a7de2b564\",\"displayName\":\"App\\\\Notifications\\\\BidReceived\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:4;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidReceived\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:13;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"5f25a74f-e78c-4dff-983a-c566cc0c8d07\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"}}',0,NULL,1741206815,1741206815),(19,'default','{\"uuid\":\"1d278fd5-50c7-4441-90f9-a5f94f247fc4\",\"displayName\":\"App\\\\Notifications\\\\BidAccepted\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidAccepted\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:13;s:9:\\\"relations\\\";a:2:{i:0;s:8:\\\"bideable\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"94f1773d-585a-4abc-a068-842f2e3d5c19\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1741206835,1741206835),(20,'default','{\"uuid\":\"5b498ef2-3f9b-4b61-8e27-4e289fae9c15\",\"displayName\":\"App\\\\Notifications\\\\BidAccepted\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidAccepted\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:13;s:9:\\\"relations\\\";a:2:{i:0;s:8:\\\"bideable\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"94f1773d-585a-4abc-a068-842f2e3d5c19\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"}}',0,NULL,1741206835,1741206835),(21,'default','{\"uuid\":\"3496bafd-cc66-4904-a0d6-7c986f2fbd26\",\"displayName\":\"App\\\\Notifications\\\\BidReceived\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:4;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidReceived\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:38;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"2c87c31b-d235-40a3-bcde-66c04149cecd\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1743059891,1743059891),(22,'default','{\"uuid\":\"c4952de6-4439-43e2-a7be-c8a75f1aea41\",\"displayName\":\"App\\\\Notifications\\\\BidReceived\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidReceived\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:39;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"423e3504-557a-4001-b718-76888e7d2a89\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1743066262,1743066262),(23,'default','{\"uuid\":\"606cb69c-d13a-40e4-b906-819d4507c7bc\",\"displayName\":\"App\\\\Notifications\\\\BidReceived\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:17;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidReceived\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:40;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"55438f8a-4575-4969-87d6-847a62d865af\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1743072741,1743072741),(24,'default','{\"uuid\":\"d246e0e6-3cfd-44a3-b226-9e986ca1dcf4\",\"displayName\":\"App\\\\Notifications\\\\BidReceived\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:17;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:29:\\\"App\\\\Notifications\\\\BidReceived\\\":2:{s:6:\\\"\\u0000*\\u0000bid\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:14:\\\"App\\\\Models\\\\Bid\\\";s:2:\\\"id\\\";i:41;s:9:\\\"relations\\\";a:2:{i:0;s:8:\\\"bideable\\\";i:1;s:4:\\\"user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"548ae412-0a2a-4a20-a221-e49b996bde3c\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"}}',0,NULL,1743073106,1743073106);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chat_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_chat_id_foreign` (`chat_id`),
  KEY `messages_user_id_foreign` (`user_id`),
  CONSTRAINT `messages_chat_id_foreign` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (13,4,6,'hola',1,0,'2025-03-05 17:42:37','2025-03-05 17:45:10'),(14,4,6,'El servicio ha sido marcado como terminado por mario',0,0,'2025-03-05 17:57:01','2025-03-05 17:57:01'),(15,6,4,'DSAD',1,0,'2025-03-05 19:46:23','2025-03-05 19:46:26'),(16,6,6,'ADSA',1,0,'2025-03-05 19:46:32','2025-03-05 19:46:35'),(17,6,4,'El servicio ha sido marcado como terminado por Mario Roberto Reque Santivañez',0,0,'2025-03-05 23:07:20','2025-03-05 23:07:20'),(18,8,4,'adad',1,0,'2025-03-06 02:25:25','2025-03-06 03:06:51'),(19,8,4,'que tal',1,0,'2025-03-06 03:06:41','2025-03-06 03:06:51'),(20,8,6,'metal',0,0,'2025-03-06 03:06:54','2025-03-06 03:06:54'),(21,7,4,'ASDA',0,0,'2025-03-06 04:14:30','2025-03-06 04:14:30'),(22,9,7,'hola',0,0,'2025-03-06 04:51:43','2025-03-06 04:51:43'),(23,11,4,'Hola puedesndndnd',1,0,'2025-03-06 08:36:58','2025-03-06 08:37:24'),(24,11,7,'sdadas',1,0,'2025-03-06 08:37:28','2025-03-06 09:21:38'),(25,11,4,'sada',1,0,'2025-03-06 08:38:25','2025-03-06 08:38:33'),(26,11,7,'El estado del trabajo ha cambiado de pendiente a en_camino',1,0,'2025-03-06 08:38:58','2025-03-06 09:21:38'),(27,11,7,'El estado del trabajo ha cambiado de en_camino a completado. El servicio ha sido marcado como terminado.',1,0,'2025-03-06 08:41:57','2025-03-06 09:21:38'),(28,14,7,'sdasd',1,0,'2025-03-06 10:36:29','2025-03-06 10:36:58'),(29,14,4,'sadad',1,0,'2025-03-06 10:37:01','2025-03-06 11:59:13'),(30,15,4,'El transportista ha marcado el servicio como terminado.',0,0,'2025-03-06 10:49:03','2025-03-06 10:49:03'),(31,18,6,'asa',1,0,'2025-03-06 10:57:51','2025-03-06 12:07:50'),(32,17,6,'El cliente ha marcado el servicio como terminado.',0,0,'2025-03-06 11:08:23','2025-03-06 11:08:23'),(33,16,7,'sadad',1,0,'2025-03-06 12:07:35','2025-03-06 12:08:02'),(34,18,4,'asdasd',0,0,'2025-03-06 12:07:56','2025-03-06 12:07:56'),(35,16,4,'ddddd',1,0,'2025-03-06 12:08:05','2025-03-06 12:15:48'),(36,16,7,'dddd',1,0,'2025-03-06 12:08:17','2025-03-06 12:08:35'),(37,16,7,'serserttt',1,0,'2025-03-06 12:08:32','2025-03-06 12:08:35'),(38,16,4,'wecos',1,0,'2025-03-06 12:14:53','2025-03-06 12:15:48'),(39,16,4,'qwq',1,0,'2025-03-06 12:15:07','2025-03-06 12:15:48'),(40,16,7,'adad',1,0,'2025-03-06 12:15:54','2025-03-06 18:16:17'),(41,11,4,'qwq',1,0,'2025-03-06 12:16:41','2025-03-06 18:19:02'),(42,11,7,'sdada',1,0,'2025-03-06 12:17:02','2025-03-06 18:16:11'),(43,11,7,'asd',1,0,'2025-03-06 12:17:58','2025-03-06 18:16:11'),(44,11,7,'asd',1,0,'2025-03-06 12:18:00','2025-03-06 18:16:11'),(45,11,4,'sadada',1,0,'2025-03-06 12:18:15','2025-03-06 18:19:02'),(46,13,6,'hola',0,0,'2025-03-06 18:09:56','2025-03-06 18:09:56'),(47,18,6,'slo',0,0,'2025-03-06 18:12:49','2025-03-06 18:12:49'),(48,18,4,'dedo',0,0,'2025-03-06 18:12:55','2025-03-06 18:12:55'),(49,20,4,'adasd',0,0,'2025-03-06 18:19:46','2025-03-06 18:19:46'),(50,21,7,'hola como stamos',0,0,'2025-03-06 19:43:00','2025-03-06 19:43:00'),(51,21,4,'El transportista ha marcado el servicio como terminado.',0,0,'2025-03-06 19:44:17','2025-03-06 19:44:17'),(52,14,7,'x98765432',0,0,'2025-03-06 19:53:37','2025-03-06 19:53:37'),(53,21,4,'El transportista ha marcado el servicio como terminado.',0,0,'2025-03-06 19:54:20','2025-03-06 19:54:20'),(54,22,17,'Oferta de $1,231.00 enviada.',0,0,'2025-03-27 11:18:10','2025-03-27 11:18:10'),(55,23,17,'Oferta de $345.00 enviada.',0,0,'2025-03-27 13:04:22','2025-03-27 13:04:22'),(56,24,16,'Oferta de $123.00 enviada.',0,0,'2025-03-27 14:52:21','2025-03-27 14:52:21'),(57,25,16,'Oferta de $23.00 enviada.',0,0,'2025-03-27 14:58:26','2025-03-27 14:58:26'),(58,25,16,'asda',0,0,'2025-03-27 14:59:13','2025-03-27 14:59:13'),(59,25,16,'asa',0,0,'2025-03-27 14:59:23','2025-03-27 14:59:23'),(60,25,17,'adsa',0,0,'2025-03-27 14:59:39','2025-03-27 14:59:39');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_02_15_000000_create_bids_table',1),(5,'2025_02_13_042638_add_user_verified_to_users_table',1),(6,'2025_02_13_045820_create_truck_types_table',1),(7,'2025_02_13_045821_create_cargo_types_table',1),(8,'2025_02_13_153704_oferta_rutas',1),(9,'2025_02_14_000000_create_administrators_table',1),(10,'2025_02_14_220914_ofertas_carga',1),(11,'2025_02_15_000000_add_role_to_administrators_table',2),(12,'2025_02_15_000001_create_required_documents_table',3),(13,'2024_02_20_000000_create_user_documents_table',4),(14,'2024_02_21_000000_create_chats_table',5),(15,'2024_02_22_000000_create_notifications_table',5),(16,'2024_02_20_000000_add_terminado_status_to_bids_table',6),(17,'2023_06_01_000001_update_foreign_keys_for_user_deletion',7),(18,'2023_07_01_000001_add_work_status_to_bids_table',8),(19,'2024_02_27_000000_add_work_status_to_bids_table',8),(20,'2023_08_01_000001_add_deletion_scheduled_at_to_users_table',9),(21,'2024_04_15_000000_add_process_status_to_bids_table',10),(23,'2024_02_21_000001_add_participants_to_chats',11),(24,'2024_03_05_000000_fix_messages_table',12),(25,'2024_03_05_000001_fix_chats_table',12),(26,'2024_06_15_000001_create_empresas_table',13);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  KEY `notifications_notifiable_id_notifiable_type_index` (`notifiable_id`,`notifiable_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('02411bf8-bcbf-47d6-89c9-1633d6ffeff5','App\\Notifications\\WorkStatusChanged','App\\Models\\User',6,'{\"message\":\"El estado del servicio ha cambiado a terminado\",\"bid_id\":28,\"new_status\":\"terminado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/28\"}',NULL,'2025-03-06 10:49:03','2025-03-06 10:49:03'),('03a77f7f-2892-4e30-8ed6-63407bbe1316','App\\Notifications\\NewChatMessage','App\\Models\\User',7,'{\"message\":\"Nuevo mensaje de edge: sadada\",\"chat_id\":11,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/11\"}','2025-03-06 18:19:02','2025-03-06 12:18:15','2025-03-06 18:19:02'),('04e565b2-30d4-4ff7-9aed-d3d67cb2a5e4','App\\Notifications\\NewChatMessage','App\\Models\\User',6,'{\"message\":\"Nuevo mensaje de Mario Roberto Reque Santiva\\u00f1ez\",\"chat_id\":8,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/8\"}','2025-03-06 03:06:51','2025-03-06 03:06:41','2025-03-06 03:06:51'),('05e8395a-6cf7-42ea-ae0f-075f177236a2','App\\Notifications\\BidReceived','App\\Models\\User',4,'{\"message\":\"Has recibido una oferta de $2,323.00\",\"bid_id\":23,\"bideable_id\":12,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":7,\"from_user_name\":\"Chrome\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/12\"}','2025-03-06 18:16:28','2025-03-06 09:54:45','2025-03-06 18:16:28'),('0a57b649-6ce9-43bc-b1df-7cf2673a6b03','App\\Notifications\\BidReceived','App\\Models\\User',6,'{\"id\":12,\"type\":\"bid\",\"message\":\"Nueva oferta recibida de Mario Roberto Reque Santiva\\u00f1ez\",\"details\":\"Oferta de ruta: Combate de los Pozos 2133, Ciudad Aut\\u00f3noma de Buenos Aires, Argentina - 25 de Mayo, La Pampa, Argentina\",\"amount\":\"23123.00\",\"url\":\"http:\\/\\/localhost\\/ofertas\\/8\"}','2025-03-05 21:57:08','2025-03-05 21:57:04','2025-03-05 21:57:08'),('0c7b63c0-87b2-4139-b8a6-118e7245f175','App\\Notifications\\BidReceived','App\\Models\\User',7,'{\"message\":\"Has recibido una oferta de $99.00\",\"bid_id\":34,\"bideable_id\":17,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":6,\"from_user_name\":\"Safari\",\"url\":\"http:\\/\\/127.0.0.1:8001\\/ofertas\\/17\"}','2025-03-06 19:46:23','2025-03-06 19:45:59','2025-03-06 19:46:23'),('115deab1-e088-4d8b-8fe4-c7bc0195cc57','App\\Notifications\\BidReceived','App\\Models\\User',4,'{\"message\":\"Has recibido una oferta de $2,323.00\",\"bid_id\":24,\"bideable_id\":12,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":6,\"from_user_name\":\"Safari\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/12\"}','2025-03-06 18:16:28','2025-03-06 09:55:33','2025-03-06 18:16:28'),('13b7fbab-b11a-4440-a9e1-013d64d03886','App\\Notifications\\BidReceived','App\\Models\\User',6,'{\"message\":\"Has recibido una oferta de $2,332.00\",\"bid_id\":30,\"bideable_id\":15,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":4,\"from_user_name\":\"edge\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/15\"}','2025-03-06 10:57:41','2025-03-06 10:57:23','2025-03-06 10:57:41'),('15ba013e-78ff-4859-9695-719524749506','App\\Notifications\\BidStatusChanged','App\\Models\\User',16,'{\"message\":\"Tu oferta de $23.00 ha sido aceptada.\",\"bid_id\":41,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/41\"}','2025-03-27 14:59:06','2025-03-27 14:58:58','2025-03-27 14:59:06'),('17c0f6c9-5bd5-4785-9d21-0c325d8bbab3','App\\Notifications\\BidStatusChanged','App\\Models\\User',4,'{\"message\":\"Tu oferta de $2,332.00 ha sido aceptada.\",\"bid_id\":30,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/30\"}','2025-03-06 18:16:28','2025-03-06 10:57:44','2025-03-06 18:16:28'),('196a3783-5d65-4858-999f-c254bb15ca18','App\\Notifications\\BidStatusChanged','App\\Models\\User',6,'{\"message\":\"Tu oferta ha sido aceptada\",\"bid_id\":2,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/2\"}','2025-03-05 17:57:21','2025-02-26 17:27:20','2025-03-05 17:57:21'),('1b4f6e03-9810-4449-8ce9-cc4219856489','App\\Notifications\\WorkStatusChanged','App\\Models\\User',7,'{\"message\":\"El estado del servicio ha cambiado a terminado\",\"bid_id\":33,\"new_status\":\"terminado\",\"url\":\"http:\\/\\/127.0.0.1:8001\\/work-progress\\/33\"}','2025-03-06 19:54:42','2025-03-06 19:54:20','2025-03-06 19:54:42'),('1ecace60-acc8-4efc-bb9e-0a2030df3b79','App\\Notifications\\BidReceived','App\\Models\\User',4,'{\"title\":\"Nueva oferta recibida\",\"message\":\"Has recibido una oferta de $21,311.90\",\"bid_id\":16,\"bideable_id\":10,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":6,\"from_user_name\":\"mario\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/10\"}','2025-03-06 09:21:40','2025-03-06 04:50:12','2025-03-06 09:21:40'),('1fc8bd65-0636-4044-be09-d7a94a58347d','App\\Notifications\\BidReceived','App\\Models\\User',7,'{\"message\":\"Has recibido una oferta de $1,313.00\",\"bid_id\":20,\"bideable_id\":9,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"from_user_id\":6,\"from_user_name\":\"Safari\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas_carga\\/9\"}','2025-03-06 09:01:40','2025-03-06 09:01:26','2025-03-06 09:01:40'),('2296db0f-ca82-4867-8e7e-fffef7ce5828','App\\Notifications\\BidStatusChanged','App\\Models\\User',6,'{\"message\":\"Tu oferta ha sido rechazada\",\"bid_id\":2,\"status\":\"terminado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/bid\\/2\"}','2025-03-05 17:26:36','2025-02-27 19:35:56','2025-03-05 17:26:36'),('2d2d1c09-e0c1-49cb-b553-9f010c304887','App\\Notifications\\NewChatMessage','App\\Models\\User',7,'{\"message\":\"Nuevo mensaje de edge: sadad\",\"chat_id\":14,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/14\"}','2025-03-06 11:59:12','2025-03-06 10:37:01','2025-03-06 11:59:12'),('2e6c57a5-7266-40e4-ab37-6a1b1aa59096','App\\Notifications\\NewChatMessage','App\\Models\\User',6,'{\"message\":\"Nuevo mensaje de mario\",\"chat_id\":3,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/3\"}','2025-02-28 00:15:25','2025-02-28 00:14:57','2025-02-28 00:15:25'),('2fc1469d-5934-4fbc-9c7b-de2473c1dba2','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Chrome: serserttt\",\"chat_id\":16,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/16\"}','2025-03-06 18:16:23','2025-03-06 12:08:32','2025-03-06 18:16:23'),('3097ed4b-c146-46b5-886b-4cb5d470b47b','App\\Notifications\\WorkStatusChanged','App\\Models\\User',4,'{\"message\":\"El estado del servicio ha cambiado a en camino\",\"bid_id\":18,\"new_status\":\"en_camino\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/18\"}','2025-03-06 09:21:35','2025-03-06 08:38:58','2025-03-06 09:21:35'),('321553ad-7143-41af-8494-47948b7fbd73','App\\Notifications\\NewChatMessage','App\\Models\\User',7,'{\"message\":\"Nuevo mensaje de edge: ddddd\",\"chat_id\":16,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/16\"}','2025-03-06 18:19:09','2025-03-06 12:08:05','2025-03-06 18:19:09'),('32b410d6-e71e-4d28-b84f-8a79dfe29cca','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Chrome: sdada\",\"chat_id\":11,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/11\"}','2025-03-06 18:16:11','2025-03-06 12:17:02','2025-03-06 18:16:11'),('32f06ff2-88a1-4a01-8313-bae838a9d00e','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Chrome: dddd\",\"chat_id\":16,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/16\"}','2025-03-06 18:16:28','2025-03-06 12:08:17','2025-03-06 18:16:28'),('3924f03e-ae17-4eda-862e-74c4c5c0a764','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Chrome: adad\",\"chat_id\":16,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/16\"}','2025-03-06 18:16:17','2025-03-06 12:15:54','2025-03-06 18:16:17'),('3d8340a0-76f3-4bb3-aae6-56487eeb0709','App\\Notifications\\BidStatusChanged','App\\Models\\User',6,'{\"message\":\"Tu oferta de $300.00 ha sido rechazada.\",\"bid_id\":32,\"status\":\"rechazado\",\"url\":\"http:\\/\\/127.0.0.1:8001\\/bids\"}',NULL,'2025-03-06 19:42:24','2025-03-06 19:42:24'),('40123fdf-3632-4703-9373-95fc5fd69028','App\\Notifications\\BidReceived','App\\Models\\User',4,'{\"message\":\"Has recibido una oferta de $2,112.00\",\"bid_id\":31,\"bideable_id\":16,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":7,\"from_user_name\":\"Chrome\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/16\"}','2025-03-06 18:21:20','2025-03-06 18:18:07','2025-03-06 18:21:20'),('419e0df4-5037-4643-8f30-d8af558f7c79','App\\Notifications\\BidStatusChanged','App\\Models\\User',4,'{\"message\":\"Tu oferta de $350.00 ha sido aceptada.\",\"bid_id\":33,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8001\\/work-progress\\/33\"}','2025-03-06 19:43:36','2025-03-06 19:42:27','2025-03-06 19:43:36'),('431b2b02-a47c-4bb2-88b1-3dbac201220d','App\\Notifications\\BidReceived','App\\Models\\User',4,'{\"message\":\"Has recibido una oferta de $12,313.00\",\"bid_id\":18,\"bideable_id\":8,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"from_user_id\":7,\"from_user_name\":\"Chrome\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas_carga\\/8\"}','2025-03-06 08:36:44','2025-03-06 08:36:28','2025-03-06 08:36:44'),('448f422b-46e2-4aa0-aa1f-f7cd3058d5f5','App\\Notifications\\BidReceived','App\\Models\\User',7,'{\"message\":\"Has recibido una oferta de $2,323.00\",\"bid_id\":19,\"bideable_id\":11,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":4,\"from_user_name\":\"edge\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/11\"}','2025-03-06 08:59:12','2025-03-06 08:58:35','2025-03-06 08:59:12'),('4517a280-e480-4203-ab36-f0a8ce780d4a','App\\Notifications\\BidStatusChanged','App\\Models\\User',6,'{\"message\":\"Tu oferta de $1,313.00 ha sido aceptada.\",\"bid_id\":20,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/20\"}',NULL,'2025-03-06 10:24:19','2025-03-06 10:24:19'),('46d7e3b6-3dd0-47ec-805e-99cc7e52d5ae','App\\Notifications\\BidReceived','App\\Models\\User',6,'{\"message\":\"Has recibido una oferta de $2,323.00\",\"bid_id\":35,\"bideable_id\":11,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"from_user_id\":17,\"from_user_name\":\"Mario Roberto Reque Santiva\\u00f1ez\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas_carga\\/11\"}',NULL,'2025-03-27 09:00:11','2025-03-27 09:00:11'),('471c7b20-646e-44b9-be38-eecdd65d3b17','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Safari: asa\",\"chat_id\":18,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/18\"}','2025-03-06 12:07:50','2025-03-06 10:57:51','2025-03-06 12:07:50'),('4d8cb5c9-b38e-43e3-ac86-f2400ebc6175','App\\Notifications\\BidReceived','App\\Models\\User',7,'{\"message\":\"Has recibido una oferta de $12,313.00\",\"bid_id\":25,\"bideable_id\":13,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":4,\"from_user_name\":\"edge\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/13\"}','2025-03-06 10:15:36','2025-03-06 10:15:16','2025-03-06 10:15:36'),('50c755a7-3f58-415a-8570-864608d2cb02','App\\Notifications\\WorkStatusChanged','App\\Models\\User',7,'{\"message\":\"El estado del servicio ha cambiado a terminado\",\"bid_id\":33,\"new_status\":\"terminado\",\"url\":\"http:\\/\\/127.0.0.1:8001\\/work-progress\\/33\"}','2025-03-06 19:47:44','2025-03-06 19:44:17','2025-03-06 19:47:44'),('52cde680-939b-47cd-ae20-84afe75b0041','App\\Notifications\\BidStatusChanged','App\\Models\\User',7,'{\"message\":\"Tu oferta de $2,323.00 ha sido aceptada.\",\"bid_id\":23,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/23\"}','2025-03-06 11:59:21','2025-03-06 10:49:26','2025-03-06 11:59:21'),('543be079-20d1-4229-afe4-38a06919b04c','App\\Notifications\\BidStatusChanged','App\\Models\\User',4,'{\"message\":\"Tu oferta de $213,123.00 ha sido aceptada.\",\"bid_id\":28,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/28\"}','2025-03-06 18:16:28','2025-03-06 10:32:46','2025-03-06 18:16:28'),('57b7d1d8-9b65-4945-82df-e1f89f91ed1e','App\\Notifications\\NewChatMessage','App\\Models\\User',7,'{\"message\":\"Nuevo mensaje de edge: qwq\",\"chat_id\":11,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/11\"}','2025-03-06 18:19:04','2025-03-06 12:16:41','2025-03-06 18:19:04'),('57bb943e-2076-4ec9-a539-0d73336104a8','App\\Notifications\\BidReceived','App\\Models\\User',7,'{\"message\":\"Has recibido una oferta de $350.00\",\"bid_id\":33,\"bideable_id\":18,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":4,\"from_user_name\":\"edge\",\"url\":\"http:\\/\\/127.0.0.1:8001\\/ofertas\\/18\"}','2025-03-06 19:47:46','2025-03-06 19:41:56','2025-03-06 19:47:46'),('5a7a3346-4def-467e-9292-70db3d4d7899','App\\Notifications\\BidReceived','App\\Models\\User',4,'{\"message\":\"Has recibido una oferta de $12,313.00\",\"bid_id\":22,\"bideable_id\":8,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"from_user_id\":6,\"from_user_name\":\"Safari\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas_carga\\/8\"}','2025-03-06 18:16:28','2025-03-06 09:26:42','2025-03-06 18:16:28'),('5dfc17a3-e46d-465b-a01d-236b97077693','App\\Notifications\\BidReceived','App\\Models\\User',6,'{\"id\":11,\"type\":\"bid\",\"message\":\"Nueva oferta recibida de Mario Roberto Reque Santiva\\u00f1ez\",\"details\":\"Oferta de carga: Delhi, India - Florida, EE. UU.\",\"amount\":\"21321.00\",\"url\":\"http:\\/\\/localhost\\/ofertas_carga\\/4\"}','2025-03-05 18:11:27','2025-03-05 18:11:16','2025-03-05 18:11:27'),('61ec7a51-13c5-45ce-b9b5-0b9fc96bfaf3','App\\Notifications\\BidReceived','App\\Models\\User',6,'{\"message\":\"Has recibido una oferta de $213,123.00\",\"bid_id\":28,\"bideable_id\":14,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":4,\"from_user_name\":\"edge\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/14\"}','2025-03-06 18:07:59','2025-03-06 10:31:48','2025-03-06 18:07:59'),('68fe3834-e7cd-406e-b002-7989f8774bcd','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de mario\",\"chat_id\":8,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/8\"}','2025-03-06 09:21:40','2025-03-06 03:06:54','2025-03-06 09:21:40'),('6c500830-e8fb-4f05-803c-eeb3289adb85','App\\Notifications\\WorkStatusChanged','App\\Models\\User',4,'{\"message\":\"El estado del servicio ha cambiado a completado\",\"bid_id\":18,\"new_status\":\"completado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/18\"}','2025-03-06 09:21:33','2025-03-06 08:41:57','2025-03-06 09:21:33'),('6d6b7752-cfd1-48bf-9774-ee0b251ba9c3','App\\Notifications\\NewChatMessage','App\\Models\\User',6,'{\"message\":\"Nuevo mensaje de Mario Roberto Reque Santiva\\u00f1ez\",\"chat_id\":8,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/8\"}','2025-03-06 03:09:44','2025-03-06 02:25:25','2025-03-06 03:09:44'),('7268dd43-b92d-4185-b698-00f7ac2d4201','App\\Notifications\\BidReceived','App\\Models\\User',6,'{\"message\":\"Has recibido una oferta de $123,123.00\",\"bid_id\":15,\"bideable_id\":9,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":4,\"from_user_name\":\"Mario Roberto Reque Santiva\\u00f1ez\"}',NULL,'2025-03-06 04:05:51','2025-03-06 04:05:51'),('738a77bf-dda7-46cd-8d51-776d76786752','App\\Notifications\\BidStatusChanged','App\\Models\\User',6,'{\"message\":\"\\u00a1Tu oferta ha sido aceptada!\",\"bid_id\":17,\"bideable_id\":7,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"status\":\"aceptado\"}','2025-03-06 05:24:44','2025-03-06 05:17:19','2025-03-06 05:24:44'),('767de489-d3fb-4e0f-86a0-ec747115f67f','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Chrome: sdasd\",\"chat_id\":14,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/14\"}','2025-03-06 10:36:58','2025-03-06 10:36:29','2025-03-06 10:36:58'),('7acf128c-0cff-42dd-8a88-25fd438159a5','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Chrome: asd\",\"chat_id\":11,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/11\"}','2025-03-06 18:16:20','2025-03-06 12:18:00','2025-03-06 18:16:20'),('7e1bbb4a-b9a1-4988-828c-a6bdb0edb4dc','App\\Notifications\\NewChatMessage','App\\Models\\User',6,'{\"message\":\"Nuevo mensaje de edge: asdasd\",\"chat_id\":18,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/18\"}',NULL,'2025-03-06 12:07:56','2025-03-06 12:07:56'),('8352f877-db1b-4b0b-9791-0a25a16b1cbf','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Chrome: asd\",\"chat_id\":11,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/11\"}','2025-03-06 18:16:14','2025-03-06 12:17:58','2025-03-06 18:16:14'),('83a56119-b737-4bc0-8bf0-11bbb49a8188','App\\Notifications\\BidStatusChanged','App\\Models\\User',7,'{\"message\":\"Tu oferta de $2,323.00 ha sido aceptada.\",\"bid_id\":29,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/29\"}','2025-03-06 11:59:08','2025-03-06 10:56:58','2025-03-06 11:59:08'),('869a9c4d-f982-4275-8b1a-f82ffc3cdbd6','App\\Notifications\\BidReceived','App\\Models\\User',7,'{\"message\":\"Has recibido una oferta de $1,313.00\",\"bid_id\":21,\"bideable_id\":9,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"from_user_id\":4,\"from_user_name\":\"edge\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas_carga\\/9\"}','2025-03-06 10:07:08','2025-03-06 09:13:35','2025-03-06 10:07:08'),('88b05fe1-4b58-4d80-8271-33ee4c919035','App\\Notifications\\BidStatusChanged','App\\Models\\User',4,'{\"message\":\"Tu oferta de $350.00 ha sido aceptada.\",\"bid_id\":33,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8001\\/work-progress\\/33\"}','2025-03-06 19:49:01','2025-03-06 19:48:27','2025-03-06 19:49:01'),('8be7f8b0-b049-475e-a17e-f85584a7a185','App\\Notifications\\BidReceived','App\\Models\\User',6,'{\"message\":\"Has recibido una oferta de $213,123.00\",\"bid_id\":27,\"bideable_id\":14,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":7,\"from_user_name\":\"Chrome\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/14\"}',NULL,'2025-03-06 10:30:32','2025-03-06 10:30:32'),('8d1d070a-f07d-4758-81da-b34b9cc67cfd','App\\Notifications\\BidStatusChanged','App\\Models\\User',4,'{\"message\":\"Tu oferta de $12,313.00 ha sido aceptada.\",\"bid_id\":25,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/25\"}','2025-03-06 18:16:28','2025-03-06 10:15:48','2025-03-06 18:16:28'),('8d473bd5-365a-40a3-b701-a1cccc6df803','App\\Notifications\\NewChatMessage','App\\Models\\User',6,'{\"message\":\"Nuevo mensaje de mario\",\"chat_id\":1,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/1\"}','2025-03-05 17:28:31','2025-02-26 17:27:24','2025-03-05 17:28:31'),('8d89c52a-8598-4d15-8319-afcf7ff7d5d2','App\\Notifications\\WorkStatusChanged','App\\Models\\User',7,'{\"message\":\"El estado del servicio ha cambiado a terminado\",\"bid_id\":29,\"new_status\":\"terminado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/29\"}','2025-03-06 11:59:04','2025-03-06 11:08:23','2025-03-06 11:59:04'),('8fc25836-1c88-4e17-8d54-c6e86ad7159c','App\\Notifications\\BidStatusChanged','App\\Models\\User',4,'{\"message\":\"\\u00a1Tu oferta ha sido aceptada!\",\"bid_id\":7,\"bideable_id\":5,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"status\":\"aceptado\"}','2025-03-06 09:21:40','2025-03-06 04:51:17','2025-03-06 09:21:40'),('93b66462-da2f-45aa-934e-810a40e39a30','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Chrome: sdadas\",\"chat_id\":11,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/11\"}','2025-03-06 09:21:38','2025-03-06 08:37:28','2025-03-06 09:21:38'),('a47fa852-bb30-423c-8a9b-343e507bac39','App\\Notifications\\BidReceived','App\\Models\\User',7,'{\"message\":\"Has recibido una oferta de $2,323.00\",\"bid_id\":14,\"bideable_id\":3,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"from_user_id\":4,\"from_user_name\":\"Mario Roberto Reque Santiva\\u00f1ez\"}','2025-03-06 04:50:33','2025-03-06 03:32:23','2025-03-06 04:50:33'),('a6820993-5a64-43a9-a65d-c9a93d96245e','App\\Notifications\\BidStatusChanged','App\\Models\\User',7,'{\"message\":\"Tu oferta de $12,313.00 ha sido aceptada.\",\"bid_id\":18,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/18\"}','2025-03-06 08:38:49','2025-03-06 08:38:20','2025-03-06 08:38:49'),('ac7f4120-9217-4c5e-8165-643dbc3f0390','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Chrome: sadad\",\"chat_id\":16,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/16\"}','2025-03-06 12:08:01','2025-03-06 12:07:35','2025-03-06 12:08:01'),('b365f4d3-5d4e-4e58-ada5-f60b9fbd0332','App\\Notifications\\BidReceived','App\\Models\\User',4,'{\"id\":10,\"type\":\"bid\",\"message\":\"Nueva oferta recibida de mario\",\"details\":\"Oferta de ruta: 123 Edward Street, Toronto, Ontario, Canad\\u00e1 - 25 de Mar\\u00e7o - Centro Hist\\u00f3rico de S\\u00e3o Paulo, S\\u00e3o Paulo - Estado de S\\u00e3o Paulo, Brasil\",\"amount\":\"213123.00\",\"url\":\"http:\\/\\/localhost\\/ofertas\\/7\"}','2025-03-05 18:06:16','2025-03-05 18:02:29','2025-03-05 18:06:16'),('b5078c90-9937-4e6c-b0d9-251903d25794','App\\Notifications\\NewChatMessage','App\\Models\\User',6,'{\"message\":\"Nuevo mensaje de mario\",\"chat_id\":3,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/3\"}','2025-03-05 17:26:31','2025-02-28 00:19:45','2025-03-05 17:26:31'),('b606e09a-f8af-4b71-826a-77d0be7f3f2b','App\\Notifications\\BidAccepted','App\\Models\\User',4,'{\"id\":11,\"type\":\"bid\",\"message\":\"\\u00a1Tu oferta ha sido aceptada!\",\"details\":\"Oferta de carga: Delhi, India - Florida, EE. UU.\",\"amount\":\"21321.00\",\"url\":\"http:\\/\\/localhost\\/bid\\/11\"}','2025-03-05 18:43:02','2025-03-05 18:42:49','2025-03-05 18:43:02'),('b6bcc8c6-674e-404a-b6b2-e29ff81cc144','App\\Notifications\\BidAccepted','App\\Models\\User',6,'{\"id\":9,\"type\":\"bid\",\"message\":\"\\u00a1Tu oferta ha sido aceptada!\",\"details\":\"Oferta de ruta: Sderot, Israel - SDSU, Campanile Drive, San Diego, California, EE. UU.\",\"amount\":\"231.83\",\"url\":\"http:\\/\\/localhost\\/bid\\/9\"}','2025-03-05 18:09:50','2025-03-05 18:02:29','2025-03-05 18:09:50'),('b8ae8630-543f-4c46-91c5-18bf2125b394','App\\Notifications\\NewChatMessage','App\\Models\\User',6,'{\"message\":\"Nuevo mensaje de mario\",\"chat_id\":1,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/1\"}','2025-02-27 19:22:15','2025-02-27 02:36:44','2025-02-27 19:22:15'),('ba3581c4-1d80-4d94-bf0b-0c355ec3cf5d','App\\Notifications\\BidStatusChanged','App\\Models\\User',4,'{\"id\":9,\"type\":\"status\",\"message\":\"El servicio ha sido marcado como terminado\",\"details\":\"Oferta de ruta: Sderot, Israel - SDSU, Campanile Drive, San Diego, California, EE. UU.\",\"amount\":\"231.83\",\"status\":\"terminado\",\"url\":\"http:\\/\\/localhost\\/bid\\/9\"}','2025-03-05 18:34:34','2025-03-05 18:02:29','2025-03-05 18:34:34'),('bb14a096-48b2-40db-ab70-060e8504391d','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de Mario Roberto Reque Santiva\\u00f1ez\",\"chat_id\":9,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/9\"}','2025-03-06 05:04:52','2025-03-06 04:51:43','2025-03-06 05:04:52'),('bce0b7ec-3a73-463e-afdf-ae29b22e7a4c','App\\Notifications\\BidStatusChanged','App\\Models\\User',7,'{\"message\":\"Tu oferta de $2,112.00 ha sido aceptada.\",\"bid_id\":31,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/31\"}','2025-03-15 03:12:29','2025-03-06 18:19:29','2025-03-15 03:12:29'),('c85257ca-85a0-4cd3-9cfb-c85185c9342b','App\\Notifications\\NewChatMessage','App\\Models\\User',6,'{\"message\":\"Nuevo mensaje de Mario Roberto Reque Santiva\\u00f1ez\",\"chat_id\":7,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/7\"}','2025-03-06 05:20:31','2025-03-06 04:14:30','2025-03-06 05:20:31'),('c89fd00c-542f-45f5-96a1-39cd2440c60e','App\\Notifications\\BidStatusChanged','App\\Models\\User',4,'{\"message\":\"Tu oferta de $2,323.00 ha sido aceptada.\",\"bid_id\":19,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/19\"}','2025-03-06 18:16:28','2025-03-06 10:36:18','2025-03-06 18:16:28'),('ca3cf0bd-d123-4a60-aefb-d04a91b55329','App\\Notifications\\BidReceived','App\\Models\\User',6,'{\"message\":\"Has recibido una oferta de $2,323.00\",\"bid_id\":29,\"bideable_id\":11,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"from_user_id\":7,\"from_user_name\":\"Chrome\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas_carga\\/11\"}','2025-03-06 10:56:49','2025-03-06 10:56:33','2025-03-06 10:56:49'),('cca851f9-eede-4633-9136-385251d0884a','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de mario\",\"chat_id\":6,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/6\"}','2025-03-05 19:47:17','2025-03-05 19:46:32','2025-03-05 19:47:17'),('cfc60275-e272-4642-902c-4dba940d9d29','App\\Notifications\\BidReceived','App\\Models\\User',7,'{\"message\":\"Has recibido una oferta de $2,323.00\",\"bid_id\":26,\"bideable_id\":11,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":6,\"from_user_name\":\"Safari\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas\\/11\"}','2025-03-06 18:19:09','2025-03-06 10:29:59','2025-03-06 18:19:09'),('d53ed442-df48-4d77-b478-46c8b6a8078b','App\\Notifications\\BidReceived','App\\Models\\User',4,'{\"title\":\"Nueva oferta recibida\",\"message\":\"Has recibido una oferta de $233,442.00\",\"bid_id\":17,\"bideable_id\":7,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"from_user_id\":6,\"from_user_name\":\"Safari\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas_carga\\/7\"}','2025-03-06 08:29:59','2025-03-06 05:15:57','2025-03-06 08:29:59'),('d963e0fd-9d5d-4cf4-9bdc-6d95a8fbb7eb','App\\Notifications\\BidStatusChanged','App\\Models\\User',7,'{\"message\":\"Tu oferta de $12,313.00 ha sido aceptada.\",\"bid_id\":18,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/18\"}','2025-03-06 12:16:57','2025-03-06 12:16:23','2025-03-06 12:16:57'),('e0179633-63aa-4243-9a41-723e9e1249b2','App\\Notifications\\NewChatMessage','App\\Models\\User',4,'{\"message\":\"Nuevo mensaje de mario\",\"chat_id\":4,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/4\"}','2025-03-05 17:56:23','2025-03-05 17:42:37','2025-03-05 17:56:23'),('e31a7ab7-9140-4e3a-83c5-70dcbce6c1e4','App\\Notifications\\NewChatMessage','App\\Models\\User',7,'{\"message\":\"Nuevo mensaje de edge: wecos\",\"chat_id\":16,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/16\"}','2025-03-06 18:18:59','2025-03-06 12:14:54','2025-03-06 18:18:59'),('e35906ff-52f1-462f-b29a-95b6682a21e7','App\\Notifications\\NewChatMessage','App\\Models\\User',7,'{\"message\":\"Nuevo mensaje de edge: qwq\",\"chat_id\":16,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/16\"}','2025-03-06 12:15:48','2025-03-06 12:15:07','2025-03-06 12:15:48'),('e96b9cf0-359d-43cd-bd82-54efa1b23806','App\\Notifications\\NewChatMessage','App\\Models\\User',7,'{\"message\":\"Nuevo mensaje de edge: Hola puedesndndnd\",\"chat_id\":11,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/11\"}','2025-03-06 08:37:24','2025-03-06 08:36:58','2025-03-06 08:37:24'),('f1e66dd8-4ec7-43f5-acf2-486fa529a7c6','App\\Notifications\\BidStatusChanged','App\\Models\\User',4,'{\"message\":\"\\u00a1Tu oferta ha sido aceptada!\",\"bid_id\":7,\"bideable_id\":5,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"status\":\"aceptado\"}','2025-03-06 05:05:07','2025-03-06 04:51:30','2025-03-06 05:05:07'),('f21f46a6-5a3e-4084-9826-5240e71c49dd','App\\Notifications\\BidStatusChanged','App\\Models\\User',7,'{\"message\":\"Tu oferta de $213,123.00 ha sido aceptada.\",\"bid_id\":27,\"status\":\"aceptado\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/27\"}','2025-03-06 11:59:06','2025-03-06 11:03:30','2025-03-06 11:59:06'),('f2e2e394-9d26-4b09-8acc-e13ee198165b','App\\Notifications\\NewChatMessage','App\\Models\\User',7,'{\"message\":\"Nuevo mensaje de edge: sada\",\"chat_id\":11,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/11\"}','2025-03-06 08:51:32','2025-03-06 08:38:26','2025-03-06 08:51:32'),('f2f71a3c-be0d-4178-ba41-0e8a123594de','App\\Notifications\\BidReceived','App\\Models\\User',2,'{\"message\":\"Nueva oferta recibida de mario\",\"bid_id\":1,\"monto\":\"213123.00\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/ofertas_carga\\/1\"}',NULL,'2025-02-24 19:03:42','2025-02-24 19:03:42'),('f3dd6fe1-68d8-4705-82d1-941f9c67392c','App\\Notifications\\BidStatusUpdateRequest','App\\Models\\User',6,'{\"title\":\"Confirmaci\\u00f3n requerida\",\"message\":\"Tu confirmaci\\u00f3n es necesaria para marcar el trabajo como \\\"en proceso\\\"\",\"bid_id\":17,\"status_requested\":\"en_proceso\",\"bideable_id\":7,\"bideable_type\":\"App\\\\Models\\\\OfertaCarga\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/work-progress\\/17\"}','2025-03-06 05:26:17','2025-03-06 05:25:54','2025-03-06 05:26:17'),('f650a608-89d4-4fca-849d-e6dfe7d721a8','App\\Notifications\\NewChatMessage','App\\Models\\User',6,'{\"message\":\"Nuevo mensaje de Mario Roberto Reque Santiva\\u00f1ez\",\"chat_id\":6,\"url\":\"http:\\/\\/127.0.0.1:8000\\/chats\\/6\"}','2025-03-05 19:46:43','2025-03-05 19:46:23','2025-03-05 19:46:43'),('fd0cf2a6-b77e-4058-9ed1-d41fc1c7cc85','App\\Notifications\\BidReceived','App\\Models\\User',7,'{\"message\":\"Has recibido una oferta de $300.00\",\"bid_id\":32,\"bideable_id\":18,\"bideable_type\":\"App\\\\Models\\\\OfertaRuta\",\"from_user_id\":6,\"from_user_name\":\"Safari\",\"url\":\"http:\\/\\/127.0.0.1:8001\\/ofertas\\/18\"}','2025-03-06 19:41:07','2025-03-06 19:40:19','2025-03-06 19:41:07');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ofertas_carga`
--

DROP TABLE IF EXISTS `ofertas_carga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ofertas_carga` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tipo_carga` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destino` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peso` decimal(8,2) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `presupuesto` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ofertas_carga_user_id_foreign` (`user_id`),
  CONSTRAINT `ofertas_carga_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ofertas_carga`
--

LOCK TABLES `ofertas_carga` WRITE;
/*!40000 ALTER TABLE `ofertas_carga` DISABLE KEYS */;
INSERT INTO `ofertas_carga` VALUES (8,4,'1','Delhi, India','23 de Enero, Caracas 1030, Distrito Capital, Venezuela',23212.58,'2023-02-23 00:00:00',12313.00,'2025-03-06 08:36:06','2025-03-06 08:36:06'),(9,7,'2','Campeche 233, Hipódromo, Cuauhtémoc, 06100 Ciudad de México, CDMX, México','23 de Enero, Caracas 1030, Distrito Capital, Venezuela',313.00,'3344-02-23 00:00:00',1313.00,'2025-03-06 08:59:49','2025-03-06 08:59:49'),(11,6,'2','Trump International Hotel & Tower Chicago, 401 N Wabash Ave, Chicago, IL 60611, EE. UU.','Krasnaya ploshchad\', Moskva, Rusia, 109012',233.00,'2025-03-20 00:00:00',2323.00,'2025-03-06 10:55:21','2025-03-06 10:55:21'),(12,17,'1','Máribor, Eslovenia','La Paz, Bolivia',233.00,'2026-06-12 00:00:00',233.00,'2025-03-27 08:11:20','2025-03-27 08:11:20'),(13,17,'1','Wisconsin, EE. UU.','Dallas, Texas, EE. UU.',23.00,'2025-12-29 00:00:00',23.00,'2025-03-27 10:02:39','2025-03-27 10:02:39'),(14,17,'1','31234 Edemissen, Alemania','Eritrea',213.00,'2025-12-12 00:00:00',123.00,'2025-03-27 10:03:57','2025-03-27 10:03:57'),(15,4,'2','La Paz, Bolivia','Sipe Sipe, Cochabamba, Bolivia',123.00,'2025-05-07 00:00:00',1231.00,'2025-03-27 10:07:56','2025-03-27 10:07:56'),(16,16,'3','Adelaida Australia Meridional, Australia','West Palm Beach, Florida, EE. UU.',12.00,'2025-03-28 00:00:00',123.00,'2025-03-27 15:01:46','2025-03-27 15:01:46'),(17,17,'2','Qweqwe, Sudáfrica','Quebec, Canadá',123.00,'2028-05-17 00:00:00',123.00,'2025-03-27 15:04:21','2025-03-27 15:04:21');
/*!40000 ALTER TABLE `ofertas_carga` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ofertas_ruta`
--

DROP TABLE IF EXISTS `ofertas_ruta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ofertas_ruta` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tipo_camion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destino` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `capacidad` int NOT NULL,
  `precio_referencial` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ofertas_ruta_user_id_foreign` (`user_id`),
  CONSTRAINT `ofertas_ruta_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ofertas_ruta`
--

LOCK TABLES `ofertas_ruta` WRITE;
/*!40000 ALTER TABLE `ofertas_ruta` DISABLE KEYS */;
INSERT INTO `ofertas_ruta` VALUES (11,7,'2','Denver, Colorado, EE. UU.','Fgura, Malta','3321-02-23 00:00:00',233,2323.00,'2025-03-06 08:58:04','2025-03-06 08:58:04'),(12,4,'2','230 Fifth Rooftop Bar, Broadway, Nueva York, EE. UU.','23 PASKAL Shopping Center, Jalan Pasir Kaliki, Kebon Jeruk, Bandung City, Java Occidental, Indonesia','2323-03-12 00:00:00',12297,2323.00,'2025-03-06 09:25:17','2025-03-06 09:25:17'),(13,7,'2','230 Fifth Rooftop Bar, Broadway, Nueva York, EE. UU.','El Salvador','2332-03-23 00:00:00',32111,12313.00,'2025-03-06 10:04:23','2025-03-06 10:04:23'),(14,6,'1','Cochabamba, Bolivia','Oruro, Bolivia','2025-03-14 00:00:00',2323,213123.00,'2025-03-06 10:28:35','2025-03-06 10:28:35'),(15,6,'2','New York, Nueva York, EE. UU.','Pekín, China','2025-03-14 00:00:00',331,2332.00,'2025-03-06 10:56:10','2025-03-06 10:56:10'),(16,4,'1','West Palm Beach, Florida, EE. UU.','233 Broadway, Nueva York, EE. UU.','2333-03-12 00:00:00',2321,2112.00,'2025-03-06 18:17:51','2025-03-06 18:17:51'),(17,7,'1','Oruro, Bolivia','Quillacollo, Bolivia','2025-03-05 00:00:00',6767,345.00,'2025-03-06 19:35:41','2025-03-06 19:35:41'),(18,7,'1','Argentina','Bolivia','2025-03-20 00:00:00',1000,0.00,'2025-03-06 19:36:43','2025-03-06 19:36:43'),(19,4,'1','Manzanillo, Colima, México','Mexicali, Baja California, México','2025-03-12 00:00:00',123456,4300.00,'2025-03-06 19:51:05','2025-03-06 19:51:05'),(20,17,'2','Qwetu | Student Residences | Parklands, Nairobi, Kenia','Gdansk, Polonia','3000-05-14 00:00:00',172,123.00,'2025-03-27 15:04:49','2025-03-27 15:04:49');
/*!40000 ALTER TABLE `ofertas_ruta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `required_documents`
--

DROP TABLE IF EXISTS `required_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `required_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `required_documents`
--

LOCK TABLES `required_documents` WRITE;
/*!40000 ALTER TABLE `required_documents` DISABLE KEYS */;
INSERT INTO `required_documents` VALUES (1,'Carnet de identidad','carnet de identidad','carnet de identidad',1,'2025-02-18 23:54:07','2025-02-18 23:54:07');
/*!40000 ALTER TABLE `required_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('2SypeQ3WjBSJcCSko1AwutqPQUBQ6VB5fcGmSeNJ',17,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoidjBJdWpjVGVNTmJQMkZwMHllMnJOdVdQZ1VkTU52ajFPbnNxOUxNWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNztzOjIyOiJ3ZWxjb21lZF92ZXJpZmllZF91c2VyIjtiOjE7fQ==',1743074608),('5YhYOWZJxIEuBw4z3MWEF7NjIrdWh0fQa7k8G0Lh',16,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Safari/605.1.15','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRHF5R2lCeGI0N3dhZzJHM0NXdklsdERHWkNuYVNKb3ZRckpJQ0hHMiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM1OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvb2ZlcnRhc19jYXJnYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE2O3M6MjI6IndlbGNvbWVkX3ZlcmlmaWVkX3VzZXIiO2I6MTt9',1743074650);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_cargas`
--

DROP TABLE IF EXISTS `tipo_cargas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_cargas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_cargas`
--

LOCK TABLES `tipo_cargas` WRITE;
/*!40000 ALTER TABLE `tipo_cargas` DISABLE KEYS */;
/*!40000 ALTER TABLE `tipo_cargas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `truck_types`
--

DROP TABLE IF EXISTS `truck_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `truck_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `truck_types`
--

LOCK TABLES `truck_types` WRITE;
/*!40000 ALTER TABLE `truck_types` DISABLE KEYS */;
INSERT INTO `truck_types` VALUES (1,'Camión Plataforma','Camión con plataforma',1,'2025-02-18 22:46:34','2025-02-18 22:46:34'),(2,'Camión Caja','Camión con caja cerrada',1,'2025-02-18 22:46:34','2025-02-18 22:46:34'),(3,'Camión Refrigerado','Camión con refrigeración',1,'2025-02-18 22:46:34','2025-02-18 22:46:34');
/*!40000 ALTER TABLE `truck_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_documents`
--

DROP TABLE IF EXISTS `user_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `required_document_id` bigint unsigned NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pendiente','aprobado','rechazado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_documents_user_id_foreign` (`user_id`),
  KEY `user_documents_required_document_id_foreign` (`required_document_id`),
  CONSTRAINT `user_documents_required_document_id_foreign` FOREIGN KEY (`required_document_id`) REFERENCES `required_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_documents`
--

LOCK TABLES `user_documents` WRITE;
/*!40000 ALTER TABLE `user_documents` DISABLE KEYS */;
INSERT INTO `user_documents` VALUES (2,4,1,'documents/4/wtxzfunVvdhwSSzC7R6jknAy8eoAfAI5Ur1xkTah.jpg','aprobado',NULL,'2025-02-19 15:49:42','2025-03-04 05:42:37'),(3,7,1,'documents/7/U5JYmy2anLSHIZ3RFlEwdNxkIgAiJhul4z1R965X.png','aprobado',NULL,'2025-02-28 23:48:53','2025-03-01 00:03:22'),(5,9,1,'documents/9/7jiv6c4SvhBlt7XawE6QUwGwUIWs4zBpyOAXjtC2.png','aprobado',NULL,'2025-03-01 01:23:19','2025-03-01 01:23:33'),(6,13,1,'documents/13/RQi0pxDxVKeFVWEHJQm37nYQ7UXTQJZ3vgLGSt4e.jpg','rechazado','no funciona','2025-03-17 15:33:15','2025-03-17 15:58:59'),(7,6,1,'documents/6/DBg9ojB4FEtuWpOsKaZuAjc22KfaPPrD5tdTAKWd.jpg','aprobado',NULL,'2025-03-17 16:11:14','2025-03-17 16:11:36'),(8,10,1,'documents/10/B2YdYAJtnO5I15KDIDR5GmYm59YKyP2E4yhw7TpN.png','aprobado',NULL,'2025-03-17 16:14:52','2025-03-17 16:16:35'),(9,14,1,'documents/14/67d85b000584d_14_1742232320.jpeg','pendiente',NULL,'2025-03-17 21:25:20','2025-03-17 21:25:20'),(10,15,1,'documents/15/67d874ab07e06_15_1742238891.jpg','pendiente',NULL,'2025-03-17 23:14:51','2025-03-17 23:14:51'),(11,16,1,'https://app.pickntruck.com/documents/16/67e48a141c715_16_1743030804.jpg','aprobado',NULL,'2025-03-27 03:13:24','2025-03-27 03:22:09'),(12,17,1,'https://app.pickntruck.com/documents/17/67e48e8dac238_17_1743031949.png','aprobado',NULL,'2025-03-27 03:32:29','2025-03-27 03:32:41');
/*!40000 ALTER TABLE `user_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deletion_scheduled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,'edge','marioreque81@gmail.com','2025-03-04 05:44:14','$2y$12$WLoGeGYTpVucc2h6ABXAf.nmkJEe8V5hQbopAt5jTMr.ad2cLHDOu',0,1,NULL,'2025-02-19 15:49:18','2025-03-06 05:12:52',NULL),(5,'Operador','touchandimport@hotmail.com',NULL,'$2y$12$dEas4gOs99pCc3OASwKA6OEMPCXqSBm8Ne7ANPBG.Gn2d0Juz26.y',0,0,NULL,'2025-02-19 16:13:40','2025-02-19 16:13:40',NULL),(6,'Safari','chaskyceo@gmail.com',NULL,'$2y$12$xZCx9anC6TkUzhE2pMfxdeilI98oSr3CC6IaYtSMqUGkyGaiAjllW',0,1,NULL,'2025-02-26 01:58:43','2025-03-17 16:12:01',NULL),(7,'Chrome','marioreque88@gmail.com','2025-03-17 15:58:34','$2y$12$DdkI0nJnAY6o8cbVo.mRDuGKJE5SlOFZxAd3u0Pr8wmTDR0XIvwem',0,1,NULL,'2025-02-28 02:11:58','2025-03-17 15:58:34',NULL),(9,'mmd','mlrood@lsldlslll.com','2025-03-02 14:49:41','$2y$12$9uCTieYFlkp8dWBW.rniQuZ2PH.nFIqf3sV/BbNzOUdcbnvX4EIP2',0,1,NULL,'2025-03-01 01:22:31','2025-03-02 14:49:41',NULL),(10,'mario','rodeoderive@gmail.com','2025-03-17 16:15:19','$2y$12$4v1SwpnNbbf9DbG9vsQyb.rvxDBLPEd0EJMQdVKi2Q0zoBcqPAR8q',0,1,NULL,'2025-03-17 08:56:33','2025-03-17 16:15:19',NULL),(11,'dueño','sonode@snemnr.com',NULL,'$2y$12$7JBbn7z9JoySJO.iRaagBecOAcD/jHrXnO/JImFbcGD1C9uNqLGWG',0,0,NULL,'2025-03-17 09:09:10','2025-03-17 09:09:10',NULL),(12,'kdkkakksj','sksksk@sksksksk.com',NULL,'$2y$12$ZLI.TXrnM/3rCVFvDfcB0eiwHTNTzgn.It.jSCed1dUoFsVBfWek.',0,0,NULL,'2025-03-17 09:10:27','2025-03-17 09:10:27',NULL),(13,'jojoajj','majdj@jfjfj.com','2025-03-17 15:37:06','$2y$12$ARaJsWeQPcU88wscizN..uhG7YjtwoOc2adaRvu7qIpXQAaDSnlqK',0,1,NULL,'2025-03-17 15:27:30','2025-03-17 15:37:06',NULL),(14,'Mario Roberto Reque Santivañez','chaskymkt1@gmail.com',NULL,'$2y$12$ndaFk0kvlMzoAzA5qHMPF.Bx06Fc4LxK3fBEwu3V0oWdTafoy4Er6',0,0,NULL,'2025-03-17 19:33:04','2025-03-17 19:33:04',NULL),(15,'mario','chaskypayment@gmail.com',NULL,'$2y$12$r12UGMg2R7iFz4VfiRw3wujxMO4C3kF90991TFzvhBXzcbYKQucVy',0,0,NULL,'2025-03-17 23:14:32','2025-03-17 23:14:32',NULL),(16,'mario','chasky.payments@gmail.com','2025-03-27 03:22:15','$2y$12$9xmL7OZ6lBo5P1fksO7PSOc.3JkZgQNQqpuBQOpkIt9oBI/ZBs5kq',0,1,NULL,'2025-03-27 03:08:15','2025-03-27 03:22:15',NULL),(17,'Mario Roberto Reque Santivañez','chaskymkt2@gmail.com','2025-03-27 03:32:44','$2y$12$kRVe2nwtfLzeFtotOTf6juFPjFsSFty5Jm11XWYzQ0uxETfWBfbGm',0,1,NULL,'2025-03-27 03:32:17','2025-03-27 03:32:44',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-27  7:24:15
