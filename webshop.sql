-- MariaDB dump 10.19  Distrib 10.4.20-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: webshop
-- ------------------------------------------------------
-- Server version	10.4.20-MariaDB

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

--
-- Table structure for table `2fa_codes`
--

DROP TABLE IF EXISTS `2fa_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `2fa_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `expiry` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `2fa_codes`
--

LOCK TABLES `2fa_codes` WRITE;
/*!40000 ALTER TABLE `2fa_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `2fa_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cities_id` int(11) DEFAULT NULL,
  `streets_id` int(11) DEFAULT NULL,
  `house_numbers_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `house_numbers_id` (`house_numbers_id`),
  KEY `streets_id` (`streets_id`),
  KEY `cities_id` (`cities_id`),
  CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`house_numbers_id`) REFERENCES `house_numbers` (`id`),
  CONSTRAINT `addresses_ibfk_2` FOREIGN KEY (`streets_id`) REFERENCES `streets` (`id`),
  CONSTRAINT `addresses_ibfk_3` FOREIGN KEY (`cities_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `addresses_ibfk_4` FOREIGN KEY (`house_numbers_id`) REFERENCES `house_numbers` (`id`),
  CONSTRAINT `addresses_ibfk_5` FOREIGN KEY (`streets_id`) REFERENCES `streets` (`id`),
  CONSTRAINT `addresses_ibfk_6` FOREIGN KEY (`cities_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `addresses_ibfk_7` FOREIGN KEY (`house_numbers_id`) REFERENCES `house_numbers` (`id`),
  CONSTRAINT `addresses_ibfk_8` FOREIGN KEY (`streets_id`) REFERENCES `streets` (`id`),
  CONSTRAINT `addresses_ibfk_9` FOREIGN KEY (`cities_id`) REFERENCES `cities` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES (1,1,1,1),(2,4,2,2),(3,5,2,2),(4,6,3,3),(5,7,2,2),(6,7,2,4);
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentcategory` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `short` varchar(255) DEFAULT NULL,
  `display_navbar` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parentcategory` (`parentcategory`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parentcategory`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,NULL,'Shoes','shoes',1),(2,1,'Boots','boots',1),(3,1,'Sandals','sandals',1),(4,NULL,'Books','books',1),(5,4,'Fantasy','fantasy',1),(6,4,'Horror','horror',1),(7,NULL,'Games','games',1),(8,4,'Action','action',1),(10,7,'Videogame','videogame',1),(11,7,'Table games','tablegames',1);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `postcodes_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `postcodes_id` (`postcodes_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,'Pécs',1),(4,'Edenderry',4),(5,'Edenderry',5),(6,'Gádoros',6),(7,'Edenderry',7);
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `end_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `discount` int(11) NOT NULL DEFAULT 0,
  `singleuse` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'CODE01','2022-01-31 16:15:04','2022-01-31 16:15:04',80,0),(2,'CODE02','2022-01-31 15:39:14','2022-01-31 15:39:14',0,1),(3,'CODE03','2022-01-31 15:39:14','2022-01-31 15:39:14',0,1),(4,'CODE04','2022-01-31 15:39:14','2022-01-31 15:39:14',0,1),(5,'CODE05','2022-01-31 15:39:14','2022-01-31 15:39:14',0,1),(7,'CODE06','2022-01-31 16:00:26','2022-01-31 16:00:26',0,1),(8,'CODE07','2022-01-31 15:39:14','2022-01-31 15:39:14',0,1),(9,'CODE08','2022-01-31 15:39:14','2022-01-31 15:39:14',0,1),(10,'CODE09','2022-01-31 15:39:14','2022-01-31 15:39:14',0,1),(12,'CODE11','2022-01-31 15:39:14','2022-01-31 15:39:14',0,1),(14,'NEWYEAR','2022-01-30 23:00:00','2034-01-30 23:00:00',30,1);
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shortname` varchar(255) DEFAULT NULL,
  `longname` varchar(255) DEFAULT NULL,
  `sign` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'Ft','Forint','Ft'),(2,'Euro','Euro','€'),(3,'Dollar','Dollar','$');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `house_numbers`
--

DROP TABLE IF EXISTS `house_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `house_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `house_numbers`
--

LOCK TABLES `house_numbers` WRITE;
/*!40000 ALTER TABLE `house_numbers` DISABLE KEYS */;
INSERT INTO `house_numbers` VALUES (1,'10'),(2,'44'),(3,'3/A'),(4,'45');
/*!40000 ALTER TABLE `house_numbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installed_plugins`
--

DROP TABLE IF EXISTS `installed_plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installed_plugins` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installed_plugins`
--

LOCK TABLES `installed_plugins` WRITE;
/*!40000 ALTER TABLE `installed_plugins` DISABLE KEYS */;
/*!40000 ALTER TABLE `installed_plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shortname` varchar(255) DEFAULT NULL,
  `longname` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (2,'EN','English'),(5,'HU','Hungarian');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_states`
--

DROP TABLE IF EXISTS `order_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_states`
--

LOCK TABLES `order_states` WRITE;
/*!40000 ALTER TABLE `order_states` DISABLE KEYS */;
INSERT INTO `order_states` VALUES (1,'ordered'),(2,'done');
/*!40000 ALTER TABLE `order_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `order_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `pay_types_id` int(11) NOT NULL,
  `addresses_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `state_id` (`state_id`),
  KEY `users_id` (`users_id`),
  KEY `pay_types_id` (`pay_types_id`),
  KEY `addresses_id` (`addresses_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`pay_types_id`) REFERENCES `pay_types` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`addresses_id`) REFERENCES `addresses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,16,'2022-02-06 17:38:36',1,6),(2,1,16,'2022-02-06 17:49:24',1,6),(3,2,16,'2022-02-06 22:34:16',1,6);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_types`
--

DROP TABLE IF EXISTS `pay_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_types`
--

LOCK TABLES `pay_types` WRITE;
/*!40000 ALTER TABLE `pay_types` DISABLE KEYS */;
INSERT INTO `pay_types` VALUES (1,'cash_on_delivery'),(2,'paypal');
/*!40000 ALTER TABLE `pay_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `people`
--

DROP TABLE IF EXISTS `people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(16) DEFAULT NULL,
  `addresses_id` int(11) DEFAULT NULL,
  `first_name` varchar(32) DEFAULT NULL,
  `last_name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_id` (`addresses_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `people`
--

LOCK TABLES `people` WRITE;
/*!40000 ALTER TABLE `people` DISABLE KEYS */;
INSERT INTO `people` VALUES (1,'+36308961902',1,'Richard','Nyitrai'),(2,'0894972173',6,'Stewie','Highmountain'),(3,'+36721234567',4,'Zoltán','Vámosi');
/*!40000 ALTER TABLE `people` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'admin_access','Enable to access the admin page.'),(2,'view_users','view_users'),(3,'manage_users','manage_users');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phrases`
--

DROP TABLE IF EXISTS `phrases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phrases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `languages_id` int(11) DEFAULT NULL,
  `phrase` varchar(255) DEFAULT NULL,
  `translated` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `languages_id` (`languages_id`)
) ENGINE=InnoDB AUTO_INCREMENT=255 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phrases`
--

LOCK TABLES `phrases` WRITE;
/*!40000 ALTER TABLE `phrases` DISABLE KEYS */;
INSERT INTO `phrases` VALUES (1,1,'search','Keresés'),(2,2,'search','Search'),(4,4,'search','Traži'),(5,5,'search','Keresés'),(6,6,'search','Keresés'),(7,5,'webshop','Webáruház'),(38,1,'statistics','Statisztikák'),(39,2,'statistics','Statistics'),(40,5,'user_details','Felhasználói adatok'),(41,5,'username','Felhasználónév'),(42,5,'email','E-mail'),(43,5,'password','Jelszó'),(44,5,'new_password','Új jelszó'),(45,5,'password_again','Jelszó újra'),(46,5,'password_now','Jelenlegi jelszó'),(47,5,'personal_informations','Személyes adatok'),(48,5,'name','Név'),(49,5,'first_name','Keresztnév'),(50,5,'last_name','Vezetéknév'),(51,5,'phone_number','Telefonszám'),(52,5,'address','Cím'),(53,5,'postcode','Irányítószám'),(54,5,'city','Város'),(55,5,'street','Utca'),(56,5,'housenumber','Házszám'),(57,5,'save','Mentés'),(58,5,'piece','Darab'),(59,5,'basket_add','Kosárhoz ad'),(60,5,'login','Bejelenetkezés'),(61,5,'rememberme','Jegyezz meg'),(62,5,'register','Regisztráció'),(63,5,'basket','Kosár'),(64,5,'price','Ár'),(65,5,'edit','Szerkesztés'),(66,5,'redirect_to_shipping','Tovább a szállításhoz'),(67,5,'delete','Törlés'),(68,5,'id','Azonosító'),(69,5,'rank','Rang'),(70,5,'modify','Módosítás'),(71,5,'admin','Admin'),(72,5,'theme','Téma'),(73,5,'edit_categories','Kategóriák szerkesztése'),(74,5,'webshop_language','Webáruház nyelve'),(75,5,'edit_language','Nyelv szerkesztés'),(76,5,'import_language','Nyelv importálása'),(77,5,'import','Importálás'),(78,5,'export_language','Nyelv exportálás'),(79,5,'export','Exportálás'),(80,5,'stock','Raktár'),(81,5,'add','Hozzáadás'),(82,5,'back','Vissza'),(83,5,'edit_product','Termék szerkesztése'),(84,5,'delete_product','Termék törlése'),(85,5,'description','Leírás'),(86,5,'add_new_images','Új képek hozzáadása'),(87,5,'available','Elérhető'),(88,5,'appear','Megjelenés'),(89,5,'disappear','Eltűnés'),(90,5,'webshop_details','Webáruház adatok'),(91,5,'theme','Téma'),(92,5,'edit_categories','Kategóriák szerkesztése'),(93,5,'webshop_language','Webáruház nyelve'),(94,5,'edit_language','Nyelv szerkesztés'),(95,5,'import_language','Nyelv importálása'),(96,5,'import','Importálás'),(97,5,'export_language','Nyelv exportálás'),(98,5,'export','Exportálás'),(99,5,'stock','Raktár'),(100,5,'add','Hozzáadás'),(101,5,'back','Vissza'),(102,5,'edit_product','Termék szerkesztése'),(103,5,'delete_product','Termék törlése'),(104,5,'description','Leírás'),(105,5,'add_new_images','Új képek hozzáadása'),(106,5,'available','Elérhető'),(107,5,'appear','Megjelenés'),(108,5,'disappear','Eltűnés'),(109,5,'always_available','Mindig elérhető'),(110,5,'first','Első'),(111,5,'last','Utolsó'),(112,5,'phrase','Kifejezés'),(113,5,'translated','Fordított'),(114,5,'delete_language','Nyelv törlése'),(115,5,'show','Megjelenítés'),(116,5,'new','Új'),(117,5,'category','Kategória'),(118,5,'main_category','Fő kategória'),(119,5,'unused_categories','Nem használt kategóriák'),(120,5,'used_categories','Használt kategóriák'),(121,5,'remove','Eltávolítás'),(122,5,'create_product','Termék létrehozása'),(123,5,'images','Képek'),(124,5,'create','Létrehozás'),(125,5,'statistics','Statisztikák'),(126,5,'coupons','Kuponok'),(127,5,'products','Termékek'),(128,5,'users','Felhasználók'),(129,5,'orders','Rendelések'),(130,5,'permissions','Jogok'),(131,5,'bans','Tiltások'),(132,5,'settings','Beállítások'),(133,5,'addons','Bővítmények'),(134,5,'back_to_shop','Vissza az áruházhoz'),(135,5,'profile','Profil'),(136,5,'logout','Kilépés'),(137,5,'profile_operations','Profil műveletek'),(138,5,'code','Kód'),(139,5,'available_from','-tól elérhető'),(140,5,'available_to','-ig elérhető'),(141,5,'uses','Felhasználások'),(142,5,'discount','Kedvezmény'),(143,5,'single_use','Egyszer használható'),(144,5,'new_coupon','Új kupon'),(145,5,'coupon','Kuponkód'),(146,5,'order','Rendelés'),(147,5,'cash_on_delivery','Utánvét'),(148,5,'paypal','Paypal'),(149,5,'ordered','Megrendelve'),(150,5,'date','Dátum'),(151,5,'done','Kész'),(152,2,'add','Add'),(153,2,'addons','Addons'),(154,2,'address','Address'),(155,2,'add_new_images','Add new images'),(156,2,'admin','Admin'),(157,2,'always_available','Always available'),(158,2,'appear','Appear'),(159,2,'available','Available'),(160,2,'available_from','Available from'),(161,2,'available_to','Available to'),(162,2,'back','Back'),(163,2,'back_to_shop','Back to shop'),(164,2,'bans','Bans'),(165,2,'basket','Basket'),(166,2,'basket_add','Add to basket'),(167,2,'cash_on_delivery','Cash on delivery'),(168,2,'category','Category'),(169,2,'city','City'),(170,2,'code','Code'),(171,2,'coupon','Coupon'),(172,2,'coupons','Coupons'),(173,2,'create','Create'),(174,2,'create_product','Create product'),(175,2,'date','Date'),(176,2,'delete','Delete'),(177,2,'delete_language','Delete language'),(178,2,'delete_product','Delete product'),(179,2,'description','Description'),(180,2,'disappear','Disappear'),(181,2,'discount','Discount'),(182,2,'done','Done'),(183,2,'edit','Edit'),(184,2,'edit_categories','Edit categories'),(185,2,'edit_language','Edit language'),(186,2,'edit_product','Edit product'),(187,2,'email','Email'),(188,2,'export','Export'),(189,2,'export_language','Export language'),(190,2,'first','First'),(191,2,'first_name','First name'),(192,2,'housenumber','House number'),(193,2,'id','Id'),(194,2,'images','Images'),(195,2,'import','Import'),(196,2,'import_language','Import language'),(197,2,'last','Last'),(198,2,'last_name','Last name'),(199,2,'login','Login'),(200,2,'logout','Logout'),(201,2,'main_category','Main category'),(202,2,'modify','Modify'),(203,2,'name','Name'),(204,2,'new','New'),(205,2,'new_coupon','New coupon'),(206,2,'new_password','New password'),(207,2,'order','Order'),(208,2,'ordered','Ordered'),(209,2,'orders','Orders'),(210,2,'password','Password'),(211,2,'password_again','Password again'),(212,2,'password_now','Password now'),(213,2,'paypal','PayPal'),(214,2,'permissions','Permissions'),(215,2,'personal_informations','Personal informations'),(216,2,'phone_number','Phone number'),(217,2,'phrase','Phrase'),(218,2,'piece','Quantity'),(219,2,'postcode','Postcode'),(220,2,'price','Price'),(221,2,'products','Products'),(222,2,'profile','Profile'),(223,2,'profile_operations','Profile operations'),(224,2,'rank','Rank'),(225,2,'redirect_to_shipping','Redirect to shipping'),(226,2,'register','Register'),(227,2,'rememberme','Remember me'),(228,2,'remove','Remove'),(229,2,'save','Save'),(230,2,'settings','Settings'),(231,2,'show','Show'),(232,2,'single_use','Single use'),(233,2,'stock','Stock'),(235,2,'street','Street'),(236,2,'theme','Theme'),(237,2,'translated','Translated'),(238,2,'unused_categories','Unused categories'),(239,2,'used_categories','Used categories'),(240,2,'username','Username'),(241,2,'users','Users'),(242,2,'user_details','User details'),(243,2,'uses','Uses'),(244,2,'webshop','Webshop'),(245,2,'webshop_details','Webshop details'),(246,2,'webshop_language','Webshop language'),(247,5,'details','Adatok'),(248,2,'details','Details'),(249,5,'order_state','Megrendelés állapota'),(250,5,'pay_type','Fizetés típusa'),(251,2,'pay_type','Pay type'),(252,2,'order_state','Order state'),(253,5,'edit_permission','Jogok szerkesztése'),(254,2,'edit_permission','Edit permission');
/*!40000 ALTER TABLE `phrases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postcodes`
--

DROP TABLE IF EXISTS `postcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postcode` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postcodes`
--

LOCK TABLES `postcodes` WRITE;
/*!40000 ALTER TABLE `postcodes` DISABLE KEYS */;
INSERT INTO `postcodes` VALUES (1,'7632'),(4,'0'),(5,'R45W328'),(6,'5932'),(7,'R45W329');
/*!40000 ALTER TABLE `postcodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_id` (`products_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (24,1,'IMG_4240.JPG'),(26,1,'1642262473istockphoto-1249496770-170667a.jpg');
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_order`
--

DROP TABLE IF EXISTS `product_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) DEFAULT NULL,
  `orders_id` int(11) DEFAULT NULL,
  `discounts_id` int(11) DEFAULT NULL,
  `piece` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `discounts_id` (`discounts_id`),
  KEY `products_id` (`products_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_order`
--

LOCK TABLES `product_order` WRITE;
/*!40000 ALTER TABLE `product_order` DISABLE KEYS */;
INSERT INTO `product_order` VALUES (1,36,3,NULL,1),(2,36,3,NULL,5),(3,34,3,NULL,10);
/*!40000 ALTER TABLE `product_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `currencies_id` int(11) DEFAULT NULL,
  `units_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `active_from` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active_to` timestamp NULL DEFAULT current_timestamp(),
  `display_notactive` tinyint(1) DEFAULT NULL,
  `categories_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `units_id` (`units_id`),
  KEY `currencies_id` (`currencies_id`),
  KEY `categories_id` (`categories_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Sztyuji telefonja','Ha megveszed elrakathatod a halál faszára a telefonját és nem fog hívni.',20000,2,NULL,111,'2022-01-14 23:00:00','2034-01-10 23:00:00',0,7,0),(32,'','',0,1,NULL,0,'2022-01-15 16:11:07','2034-01-10 23:00:00',0,2,1),(33,'','',0,1,NULL,0,'2022-01-15 16:11:09','2034-01-10 23:00:00',0,2,1),(34,'light','asddasaddaasd',101010,1,NULL,110,'2022-01-10 23:00:00','2034-01-10 23:00:00',1,5,0),(35,'asSS','SADAD',2313,1,NULL,32,'2022-01-15 16:22:56','2034-01-10 23:00:00',1,2,1),(36,'Sztyuji telefonja','Ha megveszed elrakathatod a halál faszára a telefonját és nem fog hívni.',20000,1,NULL,1000,'2022-01-10 23:00:00','2034-01-10 23:00:00',1,2,0),(37,'','',0,1,NULL,0,'2022-01-15 16:11:22','2034-01-14 23:00:00',1,2,1),(38,'','',0,1,NULL,0,'2022-01-15 16:11:23','2034-01-14 23:00:00',1,2,1);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rank_permission`
--

DROP TABLE IF EXISTS `rank_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rank_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ranks_id` int(11) DEFAULT NULL,
  `permissions_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permissions_id` (`permissions_id`),
  KEY `ranks_id` (`ranks_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rank_permission`
--

LOCK TABLES `rank_permission` WRITE;
/*!40000 ALTER TABLE `rank_permission` DISABLE KEYS */;
INSERT INTO `rank_permission` VALUES (1,2,1),(2,2,2),(3,2,3);
/*!40000 ALTER TABLE `rank_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ranks`
--

DROP TABLE IF EXISTS `ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ranks`
--

LOCK TABLES `ranks` WRITE;
/*!40000 ALTER TABLE `ranks` DISABLE KEYS */;
INSERT INTO `ranks` VALUES (1,'user'),(2,'admin'),(5,'moderator');
/*!40000 ALTER TABLE `ranks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `themes_id` int(11) DEFAULT NULL,
  `languages_id` int(11) DEFAULT NULL,
  `license_hash` varchar(255) DEFAULT NULL,
  `webshop_name` varchar(255) DEFAULT NULL,
  `root_directory` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,2,NULL,'Szakdolgozat webshop',NULL);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `streets`
--

DROP TABLE IF EXISTS `streets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `streets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `street` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `streets`
--

LOCK TABLES `streets` WRITE;
/*!40000 ALTER TABLE `streets` DISABLE KEYS */;
INSERT INTO `streets` VALUES (1,'Gadó u.'),(2,'st Patriks Wood'),(3,'Bajcsy-Zsilinszky utca');
/*!40000 ALTER TABLE `streets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `themes`
--

DROP TABLE IF EXISTS `themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `folder` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `themes`
--

LOCK TABLES `themes` WRITE;
/*!40000 ALTER TABLE `themes` DISABLE KEYS */;
INSERT INTO `themes` VALUES (1,'Default','default','1.0'),(2,'Default2.0','default','2.0');
/*!40000 ALTER TABLE `themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `short` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `used_coupons`
--

DROP TABLE IF EXISTS `used_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `used_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupons_id` int(11) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupons_id` (`coupons_id`),
  KEY `users_id` (`users_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `used_coupons`
--

LOCK TABLES `used_coupons` WRITE;
/*!40000 ALTER TABLE `used_coupons` DISABLE KEYS */;
INSERT INTO `used_coupons` VALUES (1,1,15),(2,1,16);
/*!40000 ALTER TABLE `used_coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `people_id` int(11) DEFAULT NULL,
  `ranks_id` int(11) DEFAULT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `people_id` (`people_id`),
  KEY `ranks_id` (`ranks_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (15,'William','$2y$10$7828jPg2RgqPfP0.4r7D0OLF3FQ0ANHiy26lk9wVZcJ//PwAseMZe','admin@webshop.hu',1,2,0),(16,'admin','$2y$10$KZ/Z.VOStTVGIBt6o3DKhOr0wLVaTWkFB8m6ntJbJLcd0kFSK2hdm','admin@webshop.hu1',2,2,0),(17,'user1234','$2y$10$m0r5URVj.osEQ4cEenjLcOn68S/Sg1Yxtw.QrUseGODuxLcB152Wi','valami@valami.hu',3,2,0);
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

-- Dump completed on 2022-02-14 23:28:38
