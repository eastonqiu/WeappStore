CREATE DATABASE  IF NOT EXISTS `homestead` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `homestead`;
-- MySQL dump 10.13  Distrib 5.5.49, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: homestead
-- ------------------------------------------------------
-- Server version	5.7.17

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,NULL,1,'Category 1','category-1','2017-01-28 08:17:28','2017-02-01 03:02:18'),(2,NULL,1,'Category 2','category-2','2017-01-28 08:17:28','2017-01-28 08:17:28');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `data_rows`
--

DROP TABLE IF EXISTS `data_rows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_rows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data_type_id` int(10) unsigned NOT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `browse` tinyint(1) NOT NULL DEFAULT '1',
  `read` tinyint(1) NOT NULL DEFAULT '1',
  `edit` tinyint(1) NOT NULL DEFAULT '1',
  `add` tinyint(1) NOT NULL DEFAULT '1',
  `delete` tinyint(1) NOT NULL DEFAULT '1',
  `details` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `data_rows_data_type_id_foreign` (`data_type_id`),
  CONSTRAINT `data_rows_data_type_id_foreign` FOREIGN KEY (`data_type_id`) REFERENCES `data_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_rows`
--

LOCK TABLES `data_rows` WRITE;
/*!40000 ALTER TABLE `data_rows` DISABLE KEYS */;
INSERT INTO `data_rows` VALUES (1,1,'id','PRI','ID',1,0,1,1,0,1,''),(2,1,'author_id','text','Author',1,0,1,1,0,1,''),(3,1,'title','text','Title',1,1,1,1,1,1,''),(4,1,'excerpt','text_area','excerpt',1,0,1,1,1,1,''),(5,1,'body','rich_text_box','Body',1,0,1,1,1,1,''),(6,1,'image','image','Post Image',0,1,1,1,1,1,'{\n\"resize\": {\n\"width\": \"1000\",\n\"height\": \"null\"\n},\n\"quality\": \"70%\",\n\"upsize\": true,\n\"thumbnails\": [\n{\n\"name\": \"medium\",\n\"scale\": \"50%\"\n},\n{\n\"name\": \"small\",\n\"scale\": \"25%\"\n},\n{\n\"name\": \"cropped\",\n\"crop\": {\n\"width\": \"300\",\n\"height\": \"250\"\n}\n}\n]\n}'),(7,1,'slug','text','slug',1,0,1,1,1,1,'{\"slugify\": {\"origin\": \"title\", \"forceUpdate\": true}}'),(8,1,'meta_description','text_area','meta_description',1,0,1,1,1,1,''),(9,1,'meta_keywords','text_area','meta_keywords',1,0,1,1,1,1,''),(10,1,'status','select_dropdown','status',1,1,1,1,1,1,'{\n\"default\": \"DRAFT\",\n\"options\": {\n\"PUBLISHED\": \"published\",\n\"DRAFT\": \"draft\",\n\"PENDING\": \"pending\"\n}\n}'),(11,1,'created_at','timestamp','created_at',0,1,1,0,0,0,''),(12,1,'updated_at','timestamp','updated_at',0,0,0,0,0,0,''),(13,2,'id','PRI','id',1,0,0,0,0,0,''),(14,2,'author_id','text','author_id',1,0,0,0,0,0,''),(15,2,'title','text','title',1,1,1,1,1,1,''),(16,2,'excerpt','text_area','excerpt',1,0,1,1,1,1,''),(17,2,'body','rich_text_box','body',1,0,1,1,1,1,''),(18,2,'slug','text','slug',1,0,1,1,1,1,'{\"slugify\": {\"origin\": \"title\"}}'),(19,2,'meta_description','text','meta_description',1,0,1,1,1,1,''),(20,2,'meta_keywords','text','meta_keywords',1,0,1,1,1,1,''),(21,2,'status','select_dropdown','status',1,1,1,1,1,1,'{\n\"default\": \"INACTIVE\",\n\"options\": {\n\"INACTIVE\": \"INACTIVE\",\n\"ACTIVE\": \"ACTIVE\"\n}\n}'),(22,2,'created_at','timestamp','created_at',1,1,1,0,0,0,''),(23,2,'updated_at','timestamp','updated_at',1,0,0,0,0,0,''),(24,2,'image','image','image',0,1,1,1,1,1,''),(25,3,'id','PRI','id',1,0,0,0,0,0,''),(26,3,'name','text','name',1,1,1,1,1,1,''),(27,3,'email','text','email',1,1,1,1,1,1,''),(28,3,'password','password','password',1,0,0,1,1,0,''),(29,3,'remember_token','text','remember_token',0,0,0,0,0,0,''),(30,3,'created_at','timestamp','created_at',0,1,1,0,0,0,''),(31,3,'updated_at','timestamp','updated_at',0,0,0,0,0,0,''),(32,3,'avatar','image','avatar',0,1,1,1,1,1,''),(33,5,'id','PRI','id',1,0,0,0,0,0,''),(34,5,'name','text','name',1,1,1,1,1,1,''),(35,5,'created_at','timestamp','created_at',0,0,0,0,0,0,''),(36,5,'updated_at','timestamp','updated_at',0,0,0,0,0,0,''),(37,4,'id','PRI','id',1,0,0,0,0,0,''),(38,4,'parent_id','select_dropdown','parent_id',0,0,1,1,1,1,'{\n\"default\": \"__null__\",\n\"null\": \"__null__\",\n\"options\": {\n\"__null__\": \"NULL\"\n},\n\"relationship\": {\n\"key\": \"id\",\n\"label\": \"name\"\n}\n}'),(39,4,'order','text','order',1,1,1,1,1,1,'{\n\"default\": 1\n}\n'),(40,4,'name','text','name',1,1,1,1,1,1,''),(41,4,'slug','text','slug',1,1,1,1,1,1,''),(42,4,'created_at','timestamp','created_at',0,0,1,0,0,0,''),(43,4,'updated_at','timestamp','updated_at',0,0,0,0,0,0,''),(44,6,'id','PRI','id',1,0,0,0,0,0,''),(45,6,'name','text','Name',1,1,1,1,1,1,''),(46,6,'created_at','timestamp','created_at',0,0,0,0,0,0,''),(47,6,'updated_at','timestamp','updated_at',0,0,0,0,0,0,''),(48,6,'display_name','text','Display Name',1,1,1,1,1,1,''),(49,1,'seo_title','text','seo_title',0,1,1,1,1,1,''),(50,1,'featured','checkbox','featured',1,1,1,1,1,1,''),(51,3,'role_id','text','role_id',1,0,0,1,1,0,''),(52,7,'id','PRI','Id',1,0,0,0,0,0,''),(53,7,'name','text','名称',1,1,1,1,1,1,''),(54,7,'qrcode','text','二维码',1,0,1,1,1,0,''),(55,7,'description','rich_text_box','描述',1,0,1,1,1,1,''),(56,7,'created_at','text','Created At',0,1,1,0,0,0,''),(57,7,'updated_at','text','Updated At',0,1,1,0,0,0,''),(58,7,'deleted_at','text','Deleted At',0,0,1,0,0,0,''),(59,7,'author_id','select_dropdown','Author Id',0,1,1,1,1,1,'{\r\n    \"default\": \"__null__\",\r\n    \"null\": \"__null__\",\r\n    \"options\": {\r\n        \"__null__\": \"NULL\"\r\n    },\r\n    \"relationship\": {\r\n        \"key\": \"id\",\r\n        \"label\": \"name\"\r\n    }\r\n}'),(60,7,'categories','select_multiple','Categories',0,0,1,1,1,1,'{\r\n    \"default\": \"__null__\",\r\n    \"null\": \"__null__\",\r\n    \"options\": {\r\n        \"__null__\": \"NULL\"\r\n    },\r\n    \"relationship\": {\r\n        \"key\": \"id\",\r\n        \"label\": \"name\"\r\n    }\r\n}');
/*!40000 ALTER TABLE `data_rows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `data_types`
--

DROP TABLE IF EXISTS `data_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name_singular` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name_plural` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `generate_permissions` tinyint(1) NOT NULL DEFAULT '0',
  `server_side` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_types_name_unique` (`name`),
  UNIQUE KEY `data_types_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_types`
--

LOCK TABLES `data_types` WRITE;
/*!40000 ALTER TABLE `data_types` DISABLE KEYS */;
INSERT INTO `data_types` VALUES (1,'posts','posts','Post','Posts','voyager-news','TCG\\Voyager\\Models\\Post','',1,0,'2017-01-28 08:17:23','2017-01-28 08:17:23'),(2,'pages','pages','Page','Pages','voyager-file-text','TCG\\Voyager\\Models\\Page','',1,0,'2017-01-28 08:17:24','2017-01-28 08:17:24'),(3,'users','users','User','Users','voyager-person','TCG\\Voyager\\Models\\User','',1,0,'2017-01-28 08:17:24','2017-01-28 08:17:24'),(4,'categories','categories','Category','Categories','voyager-categories','TCG\\Voyager\\Models\\Category','',1,0,'2017-01-28 08:17:24','2017-01-28 08:17:24'),(5,'menus','menus','Menu','Menus','voyager-list','TCG\\Voyager\\Models\\Menu','',1,0,'2017-01-28 08:17:24','2017-01-28 08:17:24'),(6,'roles','roles','Role','Roles','voyager-lock','TCG\\Voyager\\Models\\Role','',1,0,'2017-01-28 08:17:24','2017-01-28 08:17:24'),(7,'weapps','weapps','Weapp','Weapps','','App\\Weapp','',1,0,'2017-01-30 14:49:49','2017-01-30 14:49:49');
/*!40000 ALTER TABLE `data_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `target` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '_self',
  `icon_class` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_items_menu_id_foreign` (`menu_id`),
  CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (1,1,'Dashboard','/admin','_self','voyager-boat',NULL,NULL,1,'2017-01-28 08:17:25','2017-01-28 08:17:25'),(2,1,'Media','/admin/media','_self','voyager-images',NULL,NULL,4,'2017-01-28 08:17:25','2017-01-30 15:00:49'),(3,1,'Posts','/admin/posts','_self','voyager-news',NULL,NULL,5,'2017-01-28 08:17:25','2017-01-30 15:00:49'),(4,1,'Users','/admin/users','_self','voyager-person',NULL,NULL,3,'2017-01-28 08:17:25','2017-01-28 08:17:25'),(5,1,'Categories','/admin/categories','_self','voyager-categories',NULL,NULL,8,'2017-01-28 08:17:25','2017-01-28 08:17:25'),(6,1,'Pages','/admin/pages','_self','voyager-file-text',NULL,NULL,7,'2017-01-28 08:17:25','2017-01-28 08:17:25'),(7,1,'Roles','/admin/roles','_self','voyager-lock',NULL,NULL,2,'2017-01-28 08:17:25','2017-01-28 08:17:25'),(8,1,'Tools','','_self','voyager-tools',NULL,NULL,9,'2017-01-28 08:17:25','2017-01-28 08:17:25'),(9,1,'Menu Builder','/admin/menus','_self','voyager-list',NULL,8,1,'2017-01-28 08:17:26','2017-01-30 15:00:49'),(10,1,'Database','/admin/database','_self','voyager-data',NULL,8,2,'2017-01-28 08:17:26','2017-01-30 15:00:49'),(11,1,'Settings','/admin/settings','_self','voyager-settings',NULL,NULL,10,'2017-01-28 08:17:26','2017-01-30 15:00:49'),(12,1,'Weapps','/admin/weapps','_self','voyager-code','#000000',NULL,6,'2017-01-30 15:00:33','2017-01-31 11:33:50');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menus_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,'admin','2017-01-28 08:17:25','2017-01-28 08:17:25');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2016_01_01_000000_add_user_avatar',1),(4,'2016_01_01_000000_create_data_types_table',1),(5,'2016_01_01_000000_create_pages_table',1),(6,'2016_01_01_000000_create_posts_table',1),(7,'2016_02_15_204651_create_categories_table',1),(8,'2016_05_19_173453_create_menu_table',1),(9,'2016_10_21_190000_create_roles_table',1),(10,'2016_10_21_190000_create_settings_table',1),(11,'2016_10_30_000000_set_user_avatar_nullable',1),(12,'2016_11_30_131710_add_user_role',1),(13,'2016_11_30_135954_create_permission_table',1),(14,'2016_11_30_141208_create_permission_role_table',1),(15,'2016_12_26_201236_data_types__add__server_side',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8_unicode_ci,
  `body` text COLLATE utf8_unicode_ci,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_description` text COLLATE utf8_unicode_ci,
  `meta_keywords` text COLLATE utf8_unicode_ci,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'INACTIVE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,0,'Scurvy Page','Hang the jib grog grog blossom grapple dance the hempen jig gangway pressgang bilge rat to go on account lugger. Nelsons folly gabion line draught scallywag fire ship gaff fluke fathom case shot. Sea Legs bilge rat sloop matey gabion long clothes run a shot across the bow Gold Road cog league.','<p>Scallywag grog swab Cat o\'nine tails scuttle rigging hardtack cable nipper Yellow Jack. Handsomely spirits knave lad killick landlubber or just lubber deadlights chantey pinnace crack Jennys tea cup. Provost long clothes black spot Yellow Jack bilged on her anchor league lateen sail case shot lee tackle.</p>\n<p>Ballast spirits fluke topmast me quarterdeck schooner landlubber or just lubber gabion belaying pin. Pinnace stern galleon starboard warp carouser to go on account dance the hempen jig jolly boat measured fer yer chains. Man-of-war fire in the hole nipperkin handsomely doubloon barkadeer Brethren of the Coast gibbet driver squiffy.</p>','pages/AAgCCnqHfLlRub9syUdw.jpg','scurvy-page','Yar Meta Description','Keyword1, Keyword2','ACTIVE','2017-01-28 08:17:28','2017-01-28 08:17:28');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_permission_id_index` (`permission_id`),
  KEY `permission_role_role_id_index` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES (1,1),(1,2),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1);
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `table_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permissions_key_index` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'browse_admin','admin','2017-01-28 08:17:26','2017-01-28 08:17:26'),(2,'browse_database','admin','2017-01-28 08:17:26','2017-01-28 08:17:26'),(3,'browse_media','admin','2017-01-28 08:17:26','2017-01-28 08:17:26'),(4,'browse_settings','admin','2017-01-28 08:17:26','2017-01-28 08:17:26'),(5,'browse_menus','menus','2017-01-28 08:17:26','2017-01-28 08:17:26'),(6,'read_menus','menus','2017-01-28 08:17:26','2017-01-28 08:17:26'),(7,'edit_menus','menus','2017-01-28 08:17:26','2017-01-28 08:17:26'),(8,'add_menus','menus','2017-01-28 08:17:26','2017-01-28 08:17:26'),(9,'delete_menus','menus','2017-01-28 08:17:26','2017-01-28 08:17:26'),(10,'browse_pages','pages','2017-01-28 08:17:26','2017-01-28 08:17:26'),(11,'read_pages','pages','2017-01-28 08:17:26','2017-01-28 08:17:26'),(12,'edit_pages','pages','2017-01-28 08:17:26','2017-01-28 08:17:26'),(13,'add_pages','pages','2017-01-28 08:17:26','2017-01-28 08:17:26'),(14,'delete_pages','pages','2017-01-28 08:17:26','2017-01-28 08:17:26'),(15,'browse_roles','roles','2017-01-28 08:17:26','2017-01-28 08:17:26'),(16,'read_roles','roles','2017-01-28 08:17:26','2017-01-28 08:17:26'),(17,'edit_roles','roles','2017-01-28 08:17:26','2017-01-28 08:17:26'),(18,'add_roles','roles','2017-01-28 08:17:26','2017-01-28 08:17:26'),(19,'delete_roles','roles','2017-01-28 08:17:26','2017-01-28 08:17:26'),(20,'browse_users','users','2017-01-28 08:17:26','2017-01-28 08:17:26'),(21,'read_users','users','2017-01-28 08:17:26','2017-01-28 08:17:26'),(22,'edit_users','users','2017-01-28 08:17:26','2017-01-28 08:17:26'),(23,'add_users','users','2017-01-28 08:17:26','2017-01-28 08:17:26'),(24,'delete_users','users','2017-01-28 08:17:26','2017-01-28 08:17:26'),(25,'browse_posts','posts','2017-01-28 08:17:26','2017-01-28 08:17:26'),(26,'read_posts','posts','2017-01-28 08:17:26','2017-01-28 08:17:26'),(27,'edit_posts','posts','2017-01-28 08:17:26','2017-01-28 08:17:26'),(28,'add_posts','posts','2017-01-28 08:17:26','2017-01-28 08:17:26'),(29,'delete_posts','posts','2017-01-28 08:17:26','2017-01-28 08:17:26'),(30,'browse_categories','categories','2017-01-28 08:17:26','2017-01-28 08:17:26'),(31,'read_categories','categories','2017-01-28 08:17:26','2017-01-28 08:17:26'),(32,'edit_categories','categories','2017-01-28 08:17:26','2017-01-28 08:17:26'),(33,'add_categories','categories','2017-01-28 08:17:27','2017-01-28 08:17:27'),(34,'delete_categories','categories','2017-01-28 08:17:27','2017-01-28 08:17:27'),(35,'browse_weapps','weapps','2017-01-30 14:49:49','2017-01-30 14:49:49'),(36,'read_weapps','weapps','2017-01-30 14:49:49','2017-01-30 14:49:49'),(37,'edit_weapps','weapps','2017-01-30 14:49:49','2017-01-30 14:49:49'),(38,'add_weapps','weapps','2017-01-30 14:49:49','2017-01-30 14:49:49'),(39,'delete_weapps','weapps','2017-01-30 14:49:49','2017-01-30 14:49:49');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seo_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `excerpt` text COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_description` text COLLATE utf8_unicode_ci NOT NULL,
  `meta_keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('PUBLISHED','DRAFT','PENDING') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,NULL,'Lorem Ipsum Post','','                                                                                                                        This is the excerpt for the Lorem Ipsum Post                                                                                                        ','<p>This is the body of the lorem ipsum post</p>','posts/nlje9NZQ7bTMYOUG4lF1.jpg','lorem-ipsum-post','                                                                                                                                                This is the meta description                                                                                                                                ','                                                                                                                                                keyword1, keyword2, keyword3                                                                                                                                ','DRAFT',0,'2017-01-28 08:17:28','2017-02-01 02:44:12'),(2,0,NULL,'My Sample Post',NULL,'This is the excerpt for the sample Post','<p>This is the body for the sample post, which includes the body.</p>\n                <h2>We can use all kinds of format!</h2>\n                <p>And include a bunch of other stuff.</p>','posts/7uelXHi85YOfZKsoS6Tq.jpg','my-sample-post','Meta Description for sample post','keyword1, keyword2, keyword3','PUBLISHED',0,'2017-01-28 08:17:28','2017-01-28 08:17:28'),(3,0,NULL,'Latest Post',NULL,'This is the excerpt for the latest post','<p>This is the body for the latest post</p>','posts/9txUSY6wb7LTBSbDPrD9.jpg','latest-post','This is the meta description','keyword1, keyword2, keyword3','PUBLISHED',0,'2017-01-28 08:17:28','2017-01-28 08:17:28'),(4,0,NULL,'Yarr Post',NULL,'Reef sails nipperkin bring a spring upon her cable coffer jury mast spike marooned Pieces of Eight poop deck pillage. Clipper driver coxswain galleon hempen halter come about pressgang gangplank boatswain swing the lead. Nipperkin yard skysail swab lanyard Blimey bilge water ho quarter Buccaneer.','<p>Swab deadlights Buccaneer fire ship square-rigged dance the hempen jig weigh anchor cackle fruit grog furl. Crack Jennys tea cup chase guns pressgang hearties spirits hogshead Gold Road six pounders fathom measured fer yer chains. Main sheet provost come about trysail barkadeer crimp scuttle mizzenmast brig plunder.</p>\n<p>Mizzen league keelhaul galleon tender cog chase Barbary Coast doubloon crack Jennys tea cup. Blow the man down lugsail fire ship pinnace cackle fruit line warp Admiral of the Black strike colors doubloon. Tackle Jack Ketch come about crimp rum draft scuppers run a shot across the bow haul wind maroon.</p>\n<p>Interloper heave down list driver pressgang holystone scuppers tackle scallywag bilged on her anchor. Jack Tar interloper draught grapple mizzenmast hulk knave cable transom hogshead. Gaff pillage to go on account grog aft chase guns piracy yardarm knave clap of thunder.</p>','posts/yuk1fBwmKKZdY2qR1ZKM.jpg','yarr-post','this be a meta descript','keyword1, keyword2, keyword3','PUBLISHED',0,'2017-01-28 08:17:28','2017-01-28 08:17:28');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','Administrator','2017-01-28 08:17:26','2017-01-28 08:17:26'),(2,'user','Normal User','2017-01-28 08:17:26','2017-01-28 08:17:26');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `details` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'title','Site Title','Site Title','','text',1),(2,'description','Site Description','Site Description','','text',2),(3,'logo','Site Logo','','','image',3),(4,'admin_bg_image','Admin Background Image','','','image',9),(5,'admin_title','Admin Title','Voyager','','text',4),(6,'admin_description','Admin Description','Welcome to Voyager. The Missing Admin for Laravel','','text',5),(7,'admin_loader','Admin Loader','','','image',6),(8,'admin_icon_image','Admin Icon Image','','','image',7),(9,'google_analytics_client_id','Google Analytics Client ID','','','text',9);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'users/default.png',
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@admin.com','$2y$10$.iuA6x1VT31yJsEF2Jd6j.54ipvnuceU8l3LCHALAeFVPTgPNI8mC','n9LyDEWjUPoGB8izeiS15e8hhXH9Oqly0SGG8wC86Qb7XVtvraaw0qY7O3Gc','2017-01-28 08:17:28','2017-01-30 07:37:54','users/default.png',1),(2,'Test','test@test.com','$2y$10$GpMdQUqmBCbke.ezZ3WCYe/BjibHZd6h9UkHscTcoRabTSU/jxF/O','2uShLG0pp5XEJkxfgKWgMapyn3Ga3xVHfc4IZi5aSoPvLiJ8BIBWyPgQkpBg','2017-01-28 14:10:58','2017-01-30 07:38:19','users/default.png',2),(3,'123','123@123.com','$2y$10$qXs5m29VwfV6vVinFFlYju3rVcl3HZDzjvJaPPPhwf.9JNnt0RLgS','MnW31ufzBsGySRnk0uIA3IglMpFIgeC5VKlHigqJWyqXFEo7pZ6W06D1cmsN','2017-01-30 07:27:29','2017-01-30 07:27:36','users/default.png',2);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weapp_category`
--

DROP TABLE IF EXISTS `weapp_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weapp_category` (
  `weapp_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`weapp_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weapp_category`
--

LOCK TABLES `weapp_category` WRITE;
/*!40000 ALTER TABLE `weapp_category` DISABLE KEYS */;
INSERT INTO `weapp_category` VALUES (1,1,'2017-02-01 14:33:49','2017-02-01 14:33:49'),(1,2,'2017-02-01 14:33:49','2017-02-01 14:33:49');
/*!40000 ALTER TABLE `weapp_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weapps`
--

DROP TABLE IF EXISTS `weapps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weapps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `qrcode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `categories` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weapps`
--

LOCK TABLES `weapps` WRITE;
/*!40000 ALTER TABLE `weapps` DISABLE KEYS */;
INSERT INTO `weapps` VALUES (1,1,'test','test','<p>stests</p>','2017-01-31 11:27:12','2017-02-01 14:49:32',NULL,NULL);
/*!40000 ALTER TABLE `weapps` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-04-23  0:02:46
