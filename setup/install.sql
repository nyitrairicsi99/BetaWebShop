SET FOREIGN_KEY_CHECKS=0;
SET character_set_client = 'utf8mb4';
SET character_set_results = 'utf8mb4';
SET character_set_connection = 'utf8mb4';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,NULL,'All','all',1);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
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
INSERT INTO `currencies` VALUES (1,'HUF','Forint','Ft'),(2,'EUR','Euro','€'),(3,'USD','Dollar','$');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `house_numbers`
--

LOCK TABLES `house_numbers` WRITE;
/*!40000 ALTER TABLE `house_numbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `house_numbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installed_plugins`
--

DROP TABLE IF EXISTS `installed_plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installed_plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'EN','English');
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
  `people_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `state_id` (`state_id`),
  KEY `users_id` (`users_id`),
  KEY `pay_types_id` (`pay_types_id`),
  KEY `addresses_id` (`addresses_id`),
  KEY `people_id` (`people_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`pay_types_id`) REFERENCES `pay_types` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`addresses_id`) REFERENCES `addresses` (`id`),
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`people_id`) REFERENCES `people` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_types`
--

LOCK TABLES `pay_types` WRITE;
/*!40000 ALTER TABLE `pay_types` DISABLE KEYS */;
INSERT INTO `pay_types` VALUES (1,'cash_on_delivery');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `people`
--

LOCK TABLES `people` WRITE;
/*!40000 ALTER TABLE `people` DISABLE KEYS */;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'admin_access'),(2,'ignore_ban'),(3,'view_statistics'),(4,'view_coupons'),(5,'manage_coupons'),(6,'view_products'),(7,'create_product'),(8,'manage_products'),(9,'delete_product'),(10,'view_users'),(11,'manage_users'),(12,'view_permissions'),(13,'manage_permissions'),(14,'manage_settings'),(15,'manage_addons'),(16,'view_orders'),(17,'manage_orders');
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
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phrases`
--

LOCK TABLES `phrases` WRITE;
/*!40000 ALTER TABLE `phrases` DISABLE KEYS */;
INSERT INTO `phrases` VALUES (2,1,'search','Search'),(39,1,'statistics','Statistics'),(152,1,'add','Add'),(153,1,'addons','Addons'),(154,1,'address','Address'),(155,1,'add_new_images','Add new images'),(156,1,'admin','Admin'),(157,1,'always_available','Always available'),(158,1,'appear','Appear'),(159,1,'available','Available'),(160,1,'available_from','Available from'),(161,1,'available_to','Available to'),(162,1,'back','Back'),(163,1,'back_to_shop','Back to shop'),(164,1,'bans','Bans'),(165,1,'basket','Basket'),(166,1,'basket_add','Add to basket'),(167,1,'cash_on_delivery','Cash on delivery'),(168,1,'category','Category'),(169,1,'city','City'),(170,1,'code','Code'),(171,1,'coupon','Coupon'),(172,1,'coupons','Coupons'),(173,1,'create','Create'),(174,1,'create_product','Create product'),(175,1,'date','Date'),(176,1,'delete','Delete'),(177,1,'delete_language','Delete language'),(178,1,'delete_product','Delete product'),(179,1,'description','Description'),(180,1,'disappear','Disappear'),(181,1,'discount','Discount'),(182,1,'done','Done'),(183,1,'edit','Edit'),(184,1,'edit_categories','Edit categories'),(185,1,'edit_language','Edit language'),(186,1,'edit_product','Edit product'),(187,1,'email','Email'),(188,1,'export','Export'),(189,1,'export_language','Export language'),(190,1,'first','First'),(191,1,'first_name','First name'),(192,1,'housenumber','House number'),(193,1,'id','Id'),(194,1,'images','Images'),(195,1,'import','Import'),(196,1,'import_language','Import language'),(197,1,'last','Last'),(198,1,'last_name','Last name'),(199,1,'login','Login'),(200,1,'logout','Logout'),(201,1,'main_category','Main category'),(202,1,'modify','Modify'),(203,1,'name','Name'),(204,1,'new','New'),(205,1,'new_coupon','New coupon'),(206,1,'new_password','New password'),(207,1,'order','Order'),(208,1,'ordered','Ordered'),(209,1,'orders','Orders'),(210,1,'password','Password'),(211,1,'password_again','Password again'),(212,1,'password_now','Password now'),(213,1,'paypal','PayPal'),(214,1,'permissions','Permissions'),(215,1,'personal_informations','Personal informations'),(216,1,'phone_number','Phone number'),(217,1,'phrase','Phrase'),(218,1,'piece','Quantity'),(219,1,'postcode','Postcode'),(220,1,'price','Price'),(221,1,'products','Products'),(222,1,'profile','Profile'),(223,1,'profile_operations','Profile operations'),(224,1,'rank','Rank'),(225,1,'redirect_to_shipping','Redirect to shipping'),(226,1,'register','Register'),(227,1,'rememberme','Remember me'),(228,1,'remove','Remove'),(229,1,'save','Save'),(230,1,'settings','Settings'),(231,1,'show','Show'),(232,1,'single_use','Single use'),(233,1,'stock','Stock'),(235,1,'street','Street'),(236,1,'theme','Theme'),(237,1,'translated','Translated'),(238,1,'unused_categories','Unused categories'),(239,1,'used_categories','Used categories'),(240,1,'username','Username'),(241,1,'users','Users'),(242,1,'user_details','User details'),(243,1,'uses','Uses'),(244,1,'webshop','Webshop'),(245,1,'webshop_details','Webshop details'),(246,1,'webshop_language','Webshop language'),(248,1,'details','Details'),(251,1,'pay_type','Pay type'),(252,1,'order_state','Order state'),(254,1,'edit_permission','Edit permission'),(285,1,'perm_admin_access','View admin interface'),(286,1,'perm_create_product','Create product'),(287,1,'perm_delete_product','Delete product'),(288,1,'perm_ignore_ban','Ignore ban'),(289,1,'perm_manage_addons','Manage addons'),(290,1,'perm_manage_coupons','Manage coupons'),(291,1,'perm_manage_permissions','Manage permissions'),(292,1,'perm_manage_products','Manage products'),(293,1,'perm_manage_settings','Manage settings'),(294,1,'perm_manage_users','Manage users'),(295,1,'perm_view_coupons','View coupons'),(296,1,'perm_view_permissions','View permissions'),(297,1,'perm_view_products','View products'),(298,1,'perm_view_statistics','View statistics'),(299,1,'perm_view_users','View users'),(304,1,'perm_manage_orders','Manage orders'),(305,1,'perm_view_orders','View orders'),(316,1,'banned','Banned'),(317,1,'check_for_addons','Check for addons'),(318,1,'check_for_themes','Check for themes'),(319,1,'off','Off'),(320,1,'on','On'),(321,1,'orders_daily','Daily orders'),(322,1,'orders_monthly','Monthly orders'),(323,1,'visitors','Visitors'),(324,1,'visitors_daily','Daily visitors'),(325,1,'visitors_monthly','Monthly visitors'),(326,1,'notification_missing_parameters','Missing parameters.'),(327,1,'notification_error','Error during operation.'),(328,1,'notification_image_needed','Image needed to create product.'),(329,1,'notification_currency_not_same','You can add product to basket with same currency.'),(330,1,'notification_stock_not_enough','Not enough product on stock.'),(331,1,'notification_could_not_add_basket','Could not add to basket.'),(332,1,'notification_stock_not_enough_to_buy','Not enough product on stock to order.'),(333,1,'notification_product_not_found','Product not found.'),(334,1,'notification_incorrect_password','Incorrect password.'),(335,1,'notification_passwords_not_match','Passwords not matches.'),(336,1,'notification_incorrect_username_or_password','Incorrect username or password.'),(337,1,'notification_reserved_username_or_email','Username or email already reserved.'),(338,1,'notification_success_operation','Success operation.'),(339,1,'notification_success_order','Success order.'),(340,1,'notification_success_login','Success login.'),(341,1,'notification_success_register','Success register.'),(342,1,'notification_short_password','The given password is too short.'),(343,1,'none','None'),(344,1,'notification_invalid_piece','Invalid quantity.'),(345,1,'register_or_login_to_order','Please register or log in to order.');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postcodes`
--

LOCK TABLES `postcodes` WRITE;
/*!40000 ALTER TABLE `postcodes` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
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
  `discount_percent` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `discounts_id` (`discounts_id`),
  KEY `products_id` (`products_id`),
  KEY `orders_id` (`orders_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_order`
--

LOCK TABLES `product_order` WRITE;
/*!40000 ALTER TABLE `product_order` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rank_permission`
--

LOCK TABLES `rank_permission` WRITE;
/*!40000 ALTER TABLE `rank_permission` DISABLE KEYS */;
INSERT INTO `rank_permission` VALUES (1,2,1),(2,2,2),(3,2,3),(4,2,4),(5,2,5),(6,2,6),(7,2,7),(8,2,8),(9,2,9),(10,2,10),(11,2,11),(12,2,12),(13,2,13),(14,2,14),(15,2,15),(16,2,16),(17,2,17);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ranks`
--

LOCK TABLES `ranks` WRITE;
/*!40000 ALTER TABLE `ranks` DISABLE KEYS */;
INSERT INTO `ranks` VALUES (1,'user'),(2,'admin');
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
  `webshop_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,1,NULL,'Szakdolgozat webshop');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `streets`
--

LOCK TABLES `streets` WRITE;
/*!40000 ALTER TABLE `streets` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `themes`
--

LOCK TABLES `themes` WRITE;
/*!40000 ALTER TABLE `themes` DISABLE KEYS */;
INSERT INTO `themes` VALUES (1,'Default','default','1.0');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `used_coupons`
--

LOCK TABLES `used_coupons` WRITE;
/*!40000 ALTER TABLE `used_coupons` DISABLE KEYS */;
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
  `logincookie` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `people_id` (`people_id`),
  KEY `ranks_id` (`ranks_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitors`
--

DROP TABLE IF EXISTS `visitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitors`
--

LOCK TABLES `visitors` WRITE;
/*!40000 ALTER TABLE `visitors` DISABLE KEYS */;
/*!40000 ALTER TABLE `visitors` ENABLE KEYS */;
UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS=1;