-- MySQL dump 10.16  Distrib 10.1.13-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: hi
-- ------------------------------------------------------
-- Server version	10.1.13-MariaDB

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
-- Table structure for table `acc`
--

DROP TABLE IF EXISTS `acc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_account` int(10) unsigned NOT NULL,
  `id_depp` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `id_account_other` int(10) unsigned NOT NULL,
  `in_out` enum('in','out','exchange','zero') COLLATE utf8_bin NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dollar` decimal(20,2) NOT NULL DEFAULT '0.00',
  `dinar` decimal(20,0) NOT NULL DEFAULT '0',
  `type` enum('payout','payin','buy','give_debt','get_debt','expense','sell','drawing','quipment','edit_buy','edit_sell','transfer','cancel','active') COLLATE utf8_bin NOT NULL,
  `dollar_rate` float NOT NULL,
  `in_dollar` decimal(20,2) NOT NULL DEFAULT '0.00',
  `depp_balance` decimal(20,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(20,2) NOT NULL DEFAULT '0.00',
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `pair` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_acc_user` (`id_user`),
  KEY `FK_acc_account_2` (`id_account_other`),
  KEY `FK_acc_depp` (`id_depp`),
  KEY `FK_acc_account` (`id_account`),
  CONSTRAINT `FK_acc_account` FOREIGN KEY (`id_account`) REFERENCES `account` (`id`),
  CONSTRAINT `FK_acc_account_2` FOREIGN KEY (`id_account_other`) REFERENCES `account` (`id`),
  CONSTRAINT `FK_acc_depp` FOREIGN KEY (`id_depp`) REFERENCES `depp` (`id`),
  CONSTRAINT `FK_acc_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='id\r\nid_account\r\nid_depp\r\nid_user\r\nid_account_other\r\nin_out\r\ndate\r\ndollar\r\ndinar\r\ntype\r\ndollar_rate\r\nin_dollar\r\ndepp_balance\r\nbalance\r\ndetail';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acc`
--

LOCK TABLES `acc` WRITE;
/*!40000 ALTER TABLE `acc` DISABLE KEYS */;
/*!40000 ALTER TABLE `acc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acc_indepp`
--

DROP TABLE IF EXISTS `acc_indepp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acc_indepp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_indepp` int(10) unsigned NOT NULL,
  `id_acc` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_acc_indepp_indepp` (`id_indepp`),
  KEY `FK_acc_indepp_acc` (`id_acc`),
  CONSTRAINT `FK_acc_indepp_acc` FOREIGN KEY (`id_acc`) REFERENCES `acc` (`id`),
  CONSTRAINT `FK_acc_indepp_indepp` FOREIGN KEY (`id_indepp`) REFERENCES `indepp` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acc_indepp`
--

LOCK TABLES `acc_indepp` WRITE;
/*!40000 ALTER TABLE `acc_indepp` DISABLE KEYS */;
/*!40000 ALTER TABLE `acc_indepp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acc_order`
--

DROP TABLE IF EXISTS `acc_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acc_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(10) unsigned NOT NULL,
  `id_acc` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_acc_indepp_indepp` (`id_order`),
  KEY `FK_acc_indepp_acc` (`id_acc`),
  CONSTRAINT `acc_order_ibfk_1` FOREIGN KEY (`id_acc`) REFERENCES `acc` (`id`),
  CONSTRAINT `acc_order_ibfk_2` FOREIGN KEY (`id_order`) REFERENCES `ordered` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acc_order`
--

LOCK TABLES `acc_order` WRITE;
/*!40000 ALTER TABLE `acc_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `acc_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acc_outdepp`
--

DROP TABLE IF EXISTS `acc_outdepp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acc_outdepp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_outdepp` int(10) unsigned NOT NULL,
  `id_acc` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_acc_indepp_indepp` (`id_outdepp`),
  KEY `FK_acc_indepp_acc` (`id_acc`),
  CONSTRAINT `FK_acc_outdepp_acc` FOREIGN KEY (`id_acc`) REFERENCES `acc` (`id`),
  CONSTRAINT `FK_acc_outdepp_outdepp` FOREIGN KEY (`id_outdepp`) REFERENCES `outdepp` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acc_outdepp`
--

LOCK TABLES `acc_outdepp` WRITE;
/*!40000 ALTER TABLE `acc_outdepp` DISABLE KEYS */;
/*!40000 ALTER TABLE `acc_outdepp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `type` enum('company','customer','partner','expense','store','cash','drawing','equipment','noAccount') COLLATE utf8_bin NOT NULL,
  `serial` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `address` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126367 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES (0,'noAccount','noAccount',NULL,'',NULL,NULL,NULL,NULL,'2016-06-25 21:16:48'),(1,'Diwan','company','963214522','07503262536','','','http://127.0.0.1/ _test/php/ pay.php','','2016-06-25 21:16:48'),(2,'TV Reklam','expense','0123456','','07505149171','sabina.diako','22','3','2016-06-25 21:16:48'),(3,'Resid','drawing','','','','','','','2016-06-25 21:16:48'),(6,'Diako Amir','customer','00000001','07501217299',NULL,NULL,NULL,NULL,'2016-05-25 21:16:48'),(10,'سندوقی های','cash','10','','','','','','2016-06-25 21:16:48'),(11,'گەنجینە','store','11','','','','','','2016-06-25 21:16:48'),(100000,'bawar jamal','customer','09',NULL,NULL,NULL,NULL,NULL,'2016-06-25 21:16:48'),(126353,'باور مام وەستا جەمال','customer','0036','07702260787',NULL,NULL,'',NULL,'2016-06-25 21:16:48'),(126365,'سندوقی سیتی سنتر','drawing','0936528',NULL,NULL,NULL,NULL,NULL,'2016-06-25 21:16:48'),(126366,'مخزن','company','','','','','','','2016-06-25 21:16:48');
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch`
--

DROP TABLE IF EXISTS `branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_city` int(10) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `detail` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_branch_city` (`id_city`),
  CONSTRAINT `FK_branch_city` FOREIGN KEY (`id_city`) REFERENCES `city` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch`
--

LOCK TABLES `branch` WRITE;
/*!40000 ALTER TABLE `branch` DISABLE KEYS */;
INSERT INTO `branch` VALUES (1,1,'Hi',''),(2,3,'Point',''),(3,1,'Asse',NULL),(4,1,'City Center',NULL);
/*!40000 ALTER TABLE `branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brand`
--

DROP TABLE IF EXISTS `brand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brand` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_cat` int(10) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_cat_name` (`id_cat`,`name`),
  CONSTRAINT `FK_brand_cat` FOREIGN KEY (`id_cat`) REFERENCES `cat` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brand`
--

LOCK TABLES `brand` WRITE;
/*!40000 ALTER TABLE `brand` DISABLE KEYS */;
INSERT INTO `brand` VALUES (1,1,'Apple','ئەپل'),(2,5,'Shasha',NULL),(3,5,'HTC',NULL),(4,5,'LG',NULL),(5,2,'Canon',NULL),(6,1,'Samsung',NULL),(7,5,'AppleM',NULL),(8,5,'Asse',NULL),(9,5,'Tatana','test');
/*!40000 ALTER TABLE `brand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cat`
--

DROP TABLE IF EXISTS `cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cat`
--

LOCK TABLES `cat` WRITE;
/*!40000 ALTER TABLE `cat` DISABLE KEYS */;
INSERT INTO `cat` VALUES (1,'Mobile','مۆبایل'),(2,'Camera','کامێرا'),(3,'Laptop','لاپتۆپ'),(4,'Accessories','ئیکسیسوارات'),(5,'Piece','قەتعەی مۆبایل');
/*!40000 ALTER TABLE `cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `city` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `detail` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `city`
--

LOCK TABLES `city` WRITE;
/*!40000 ALTER TABLE `city` DISABLE KEYS */;
INSERT INTO `city` VALUES (1,'سلێمانی','یەکەم'),(3,'هەولێر',NULL),(17,'بۆکان','bokan');
/*!40000 ALTER TABLE `city` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `city_before_insert` BEFORE INSERT ON `city` FOR EACH ROW BEGIN
	SET @sum = @sum + 1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `depp`
--

DROP TABLE IF EXISTS `depp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_branch` int(10) unsigned NOT NULL,
  `id_cash` int(10) unsigned NOT NULL,
  `id_store` int(10) unsigned NOT NULL,
  `type` enum('piece','repair_mobile','repair_computer','mobile','camera','software','laptop','accessories') COLLATE utf8_bin NOT NULL,
  `code` varchar(5) COLLATE utf8_bin NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `address` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `detail` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `FK_depp_branch` (`id_branch`),
  KEY `FK_depp_account` (`id_cash`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depp`
--

LOCK TABLES `depp` WRITE;
/*!40000 ALTER TABLE `depp` DISABLE KEYS */;
INSERT INTO `depp` VALUES (1,1,10,11,'piece','108','Mobile Pieces','berderki sara','13123','قەتعەکانی مۆبایل'),(2,1,10,11,'repair_mobile','106','Mobile Repair','','','چاککردنەوەی مۆبایل'),(3,2,10,11,'piece','200','Software',NULL,NULL,NULL),(4,1,10,11,'camera','032','Camera',NULL,NULL,NULL),(5,1,10,11,'repair_computer','109','چاک کردنەوەی لەپتۆپ','','',''),(6,1,10,11,'mobile','107','فرۆشی مۆبایل','','','');
/*!40000 ALTER TABLE `depp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depp_cat`
--

DROP TABLE IF EXISTS `depp_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depp_cat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_depp` int(10) unsigned NOT NULL,
  `id_cat` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__depp` (`id_depp`),
  KEY `FK_depp_cat_cat` (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depp_cat`
--

LOCK TABLES `depp_cat` WRITE;
/*!40000 ALTER TABLE `depp_cat` DISABLE KEYS */;
INSERT INTO `depp_cat` VALUES (1,1,5),(3,2,5),(4,4,2),(5,1,4),(6,5,3),(7,6,1);
/*!40000 ALTER TABLE `depp_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dollar_rate`
--

DROP TABLE IF EXISTS `dollar_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dollar_rate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_city` int(10) unsigned NOT NULL,
  `rate` float NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK__city` (`id_city`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dollar_rate`
--

LOCK TABLES `dollar_rate` WRITE;
/*!40000 ALTER TABLE `dollar_rate` DISABLE KEYS */;
INSERT INTO `dollar_rate` VALUES (1,1,1230,'2015-12-17 19:54:11'),(3,1,1227.5,'2015-12-18 20:12:57'),(4,1,1250,'2016-06-23 09:46:05'),(5,1,1266,'2016-06-22 00:10:58'),(6,1,1275,'2016-06-27 00:12:13'),(7,1,1270,'2016-06-28 18:27:25'),(8,1,1267,'2016-06-29 07:22:47');
/*!40000 ALTER TABLE `dollar_rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indepp`
--

DROP TABLE IF EXISTS `indepp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `indepp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_depp` int(10) unsigned NOT NULL,
  `id_account` int(10) unsigned NOT NULL,
  `invoice` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(20,2) NOT NULL,
  `discount` float NOT NULL,
  `dollar_rate` float NOT NULL,
  `type` enum('active','just_show','cancel','freeze') COLLATE utf8_bin NOT NULL,
  `status` enum('progress','done') COLLATE utf8_bin NOT NULL DEFAULT 'progress',
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_indepp_user` (`id_user`),
  KEY `FK_indepp_depp` (`id_depp`),
  KEY `FK_indepp_account` (`id_account`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='for insert product to database, buy invoice';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indepp`
--

LOCK TABLES `indepp` WRITE;
/*!40000 ALTER TABLE `indepp` DISABLE KEYS */;
INSERT INTO `indepp` VALUES (126,3,1,1,'','2016-06-18 14:57:22',60.00,0,1250,'cancel','progress',''),(127,3,1,1,'','2016-06-18 15:02:43',100.00,0,1250,'active','progress',''),(128,3,1,126370,'','2016-06-18 15:04:09',1500.00,0,1250,'active','progress',''),(129,3,1,1,'','2016-06-18 15:16:29',3500.00,0,1250,'active','progress',''),(130,3,1,126381,'','2016-06-19 17:23:06',500.00,10,1250,'active','progress',''),(131,3,1,126381,'','2016-06-19 17:32:01',300.00,0,1250,'active','progress',''),(132,3,1,126381,'','2016-06-19 17:40:23',1500.00,0,1250,'active','progress',''),(133,3,1,126381,'','2016-06-19 17:45:35',1000.00,0,1250,'cancel','progress',''),(134,3,1,126381,'','2016-06-19 17:54:33',500.00,0,1250,'active','progress',''),(135,3,1,1,'','2016-06-20 07:36:59',1500.00,0,1250,'active','progress',''),(136,3,1,126381,'','2016-06-20 07:45:37',1200.00,0,1250,'cancel','progress',''),(137,3,1,1,'','2016-06-20 07:53:27',600.00,0,1250,'cancel','progress',''),(138,3,1,1,'','2016-06-20 08:01:44',1200.00,0,1250,'cancel','progress',''),(139,3,1,126381,'','2016-06-20 08:03:32',1000.00,0,1250,'active','progress',''),(140,3,1,126381,'','2016-06-20 08:16:09',300.00,0,1250,'cancel','progress',''),(141,3,1,126381,'','2016-06-20 08:19:56',60.00,0,1250,'active','progress',''),(142,3,1,1,'','2016-06-20 08:21:22',60.00,0,1250,'active','progress',''),(143,3,1,1,'','2016-06-20 10:46:51',150.00,0,1250,'just_show','progress',''),(144,3,1,1,'','2016-06-26 15:18:13',800.00,0,1250,'active','progress',''),(145,3,6,1,'q22','2016-06-20 22:39:43',299.00,0,1250,'active','progress','');
/*!40000 ALTER TABLE `indepp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indepp_extra`
--

DROP TABLE IF EXISTS `indepp_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `indepp_extra` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_indepp` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `qty` int(10) unsigned NOT NULL,
  `cost` decimal(20,2) NOT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_indepp_extra_indepp` (`id_indepp`)
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='id_indepp,id_product,qty,cost,detail';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indepp_extra`
--

LOCK TABLES `indepp_extra` WRITE;
/*!40000 ALTER TABLE `indepp_extra` DISABLE KEYS */;
INSERT INTO `indepp_extra` VALUES (211,126,13,2,30.00,'no'),(216,127,3,1,100.00,''),(220,128,12,3,500.00,''),(226,129,13,10,350.00,''),(228,130,13,1,500.00,''),(232,131,13,1,300.00,''),(237,132,13,5,300.00,''),(240,133,13,2,500.00,''),(245,134,13,1,500.00,''),(251,135,13,3,500.00,''),(255,136,13,3,400.00,''),(258,137,13,2,300.00,''),(265,138,13,4,300.00,''),(267,139,13,2,500.00,''),(269,140,13,1,300.00,''),(273,141,13,2,30.00,''),(275,142,13,2,30.00,''),(281,143,13,5,30.00,''),(284,145,1,1,299.00,''),(286,144,13,4,200.00,'');
/*!40000 ALTER TABLE `indepp_extra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model`
--

DROP TABLE IF EXISTS `model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_brand` int(10) unsigned NOT NULL,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_brand_name` (`id_brand`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model`
--

LOCK TABLES `model` WRITE;
/*!40000 ALTER TABLE `model` DISABLE KEYS */;
INSERT INTO `model` VALUES (1,1,'One M7',NULL),(2,3,'One M7',NULL),(3,4,'edge',NULL),(4,4,'n652',NULL),(5,6,'Tab 110',NULL),(6,7,'ipad mini',NULL),(7,7,'ipad 4',NULL),(8,8,'Q4',NULL),(9,9,'Matana',NULL);
/*!40000 ALTER TABLE `model` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_extra`
--

DROP TABLE IF EXISTS `order_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_extra` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_order` int(10) unsigned NOT NULL,
  `id_model` int(10) unsigned NOT NULL,
  `id_repairman` int(10) unsigned DEFAULT NULL,
  `iemi` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `cost` decimal(20,2) NOT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_indepp_extra_indepp` (`id_order`),
  KEY `FK_outdepp_extra_product` (`id_model`),
  KEY `FK_order_extra_repairman` (`id_repairman`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT COMMENT='id,id_order,id_model,id_repairman,iemi,cost,detail\r\n';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_extra`
--

LOCK TABLES `order_extra` WRITE;
/*!40000 ALTER TABLE `order_extra` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_extra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordered`
--

DROP TABLE IF EXISTS `ordered`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ordered` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_depp` int(10) unsigned NOT NULL,
  `id_account` int(10) unsigned NOT NULL,
  `invoice` varchar(50) COLLATE utf8_bin NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_back` timestamp NULL DEFAULT NULL,
  `total` decimal(20,2) DEFAULT NULL,
  `discount` float NOT NULL DEFAULT '0',
  `dollar_rate` float NOT NULL,
  `type` enum('active','just_show','cancel') COLLATE utf8_bin NOT NULL,
  `status` enum('progress','done') COLLATE utf8_bin NOT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_order_depp` (`id_depp`),
  KEY `FK_order_account` (`id_account`),
  KEY `FK_order_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='id,id_user,id_depp,id_account,invoice,date,date_back,total,discount,dollar_rate,type,status,detail,';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordered`
--

LOCK TABLES `ordered` WRITE;
/*!40000 ALTER TABLE `ordered` DISABLE KEYS */;
/*!40000 ALTER TABLE `ordered` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outdepp`
--

DROP TABLE IF EXISTS `outdepp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outdepp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_depp` int(10) unsigned NOT NULL,
  `id_account` int(10) unsigned NOT NULL,
  `invoice` varchar(50) COLLATE utf8_bin NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(20,2) NOT NULL,
  `discount` float NOT NULL,
  `dollar_rate` float NOT NULL,
  `type` enum('active','just_show','cancel','freeze') COLLATE utf8_bin NOT NULL,
  `status` enum('progress','done') COLLATE utf8_bin NOT NULL DEFAULT 'progress',
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice` (`invoice`),
  KEY `FK_indepp_user` (`id_user`),
  KEY `FK_indepp_depp` (`id_depp`),
  KEY `FK_indepp_account` (`id_account`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outdepp`
--

LOCK TABLES `outdepp` WRITE;
/*!40000 ALTER TABLE `outdepp` DISABLE KEYS */;
INSERT INTO `outdepp` VALUES (1,3,1,6,'108-0000001','2016-06-05 19:37:45',500.00,5,1250,'active','progress',''),(2,3,6,100000,'107-0000001','2016-05-25 21:53:51',960.00,10,1250,'active','done',''),(4,3,1,6,'108-0000003','2016-06-26 22:58:05',600.00,0,1250,'active','progress',''),(8,3,6,126353,'107-0000002','2016-06-27 12:24:03',7000.00,10,1275,'active','progress','ئەمە تێبینیە بۆیە لێرە زۆر دەنووسم تا بزانم چی لێ دێ لە کاتی پرینت'),(9,3,6,126353,'107-0000003','2016-06-27 15:24:38',30.00,0,1275,'active','progress','');
/*!40000 ALTER TABLE `outdepp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outdepp_extra`
--

DROP TABLE IF EXISTS `outdepp_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outdepp_extra` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_outdepp` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `qty` int(10) unsigned NOT NULL,
  `cost` decimal(20,2) NOT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_indepp_extra_indepp` (`id_outdepp`),
  KEY `FK_outdepp_extra_product` (`id_product`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='id_indepp,id_product,qty,cost,detail';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outdepp_extra`
--

LOCK TABLES `outdepp_extra` WRITE;
/*!40000 ALTER TABLE `outdepp_extra` DISABLE KEYS */;
INSERT INTO `outdepp_extra` VALUES (3,2,1,3,320.00,''),(4,1,13,1,500.00,''),(5,3,13,20,30.00,''),(6,4,13,20,30.00,''),(7,7,6,6,50.00,''),(13,8,8,1,250.00,'شەرحی بەرهەم'),(14,8,8,5,250.00,''),(15,8,11,10,50.00,''),(16,8,8,20,250.00,''),(17,9,1,1,30.00,'');
/*!40000 ALTER TABLE `outdepp_extra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perm`
--

DROP TABLE IF EXISTS `perm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perm` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `perm` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `user` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `user_depp` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `city` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `branch` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `depp` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `fund` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `dollar_rate` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `brand` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `product` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `piece` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `customer` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `store_model` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `store_piece` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `in_repair` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `in_model` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `in_piece` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `depp_cat` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `payin` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `payout` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `model` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `indepp` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `outdepp` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `store` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `ordered` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  `log` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT '0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perm`
--

LOCK TABLES `perm` WRITE;
/*!40000 ALTER TABLE `perm` DISABLE KEYS */;
INSERT INTO `perm` VALUES (1,'Admin','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111','1111'),(2,'test','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','0000','','0000','0000','0000','0000','0000');
/*!40000 ALTER TABLE `perm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_brand` int(11) unsigned NOT NULL,
  `id_model` int(11) unsigned DEFAULT NULL,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `code` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_buy` decimal(10,2) NOT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `FK_product_brand` (`id_brand`),
  KEY `FK_product_model` (`id_model`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,1,1,'Iphone 6','I253',0.00,1301.35,'ئایفۆن 6'),(2,1,1,'کاڤێر','362',36.22,36.22,NULL),(3,4,3,'keyboard','035',15.00,13.65,NULL),(4,4,3,'Light','6320',36.00,36.00,NULL),(5,4,NULL,'LG phone','X750',15.00,15.00,NULL),(6,3,2,'Volum key','985',1.00,1.00,NULL),(7,4,4,'Light','002',15.00,17.50,NULL),(8,1,NULL,'Iphone4','04',250.00,250.00,NULL),(9,1,NULL,'Iphone5','96',300.00,300.00,NULL),(10,1,NULL,'Iphone6','9632',400.00,400.00,NULL),(11,6,5,'LCD','158',50.00,50.00,''),(12,7,6,'Touch','231',0.00,34.68,''),(13,7,7,'Touch','254',30.00,51.72,NULL),(14,1,NULL,'test','3256',25.00,25.00,NULL),(15,9,9,'Latana','t0',200.00,100.00,''),(16,9,9,'Patana','p0',100.00,97.50,NULL);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repairman`
--

DROP TABLE IF EXISTS `repairman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repairman` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_depp` int(10) unsigned NOT NULL,
  `name` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '''''',
  `state` enum('active','inactive') COLLATE utf8_bin NOT NULL DEFAULT 'active',
  `mobile` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mobile_repairman_depp` (`id_depp`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repairman`
--

LOCK TABLES `repairman` WRITE;
/*!40000 ALTER TABLE `repairman` DISABLE KEYS */;
INSERT INTO `repairman` VALUES (2,1,'کمال','active','07505149171',''),(3,2,'Muhsin','active',NULL,NULL);
/*!40000 ALTER TABLE `repairman` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store`
--

DROP TABLE IF EXISTS `store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_depp` int(10) unsigned NOT NULL,
  `id_product` int(10) unsigned NOT NULL,
  `qty` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_depp_id_product` (`id_depp`,`id_product`),
  KEY `FK_store_product` (`id_product`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store`
--

LOCK TABLES `store` WRITE;
/*!40000 ALTER TABLE `store` DISABLE KEYS */;
INSERT INTO `store` VALUES (14,1,6,1617),(15,1,3,9),(16,1,4,0),(17,1,7,20),(18,1,11,2),(19,1,12,96),(20,1,13,294),(21,6,1,163),(22,1,15,70),(23,1,16,60),(24,6,11,6),(25,6,9,2),(26,6,8,-25);
/*!40000 ALTER TABLE `store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_perm` int(10) unsigned NOT NULL,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `username` varchar(200) COLLATE utf8_bin NOT NULL,
  `password` varchar(200) COLLATE utf8_bin NOT NULL,
  `lang` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'ku',
  `mobile` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `change_password` tinyint(4) DEFAULT '0',
  `state` enum('active','deactive') COLLATE utf8_bin DEFAULT 'active',
  `email` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `detail` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `FK_user_perm` (`id_perm`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'Diako','a','$2y$10$TLeD7lUaWDhWHrYSTKpn1eF/kSG3jQaLdOtrkfD17lQN5QpActnU2','ku',NULL,0,'active',NULL,NULL),(3,1,'DSH','syronz','$2y$10$srlC7DSVlJJ7MOvlHa09qO5i6iOEGZc3Tj0a.A53Mu.0hYpCr4VjG','ku','07505149171',0,'active','',''),(4,1,'ئامانج','amanj','$2y$10$i6HTaQP5BKx0tO6vmlgCbexzrS/yhR/9ihBEso91YuO1IV.utNlYW','ku','',0,'active','','');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_depp`
--

DROP TABLE IF EXISTS `user_depp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_depp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_depp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user_id_depp` (`id_user`,`id_depp`),
  KEY `FK_user_depp_depp` (`id_depp`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_depp`
--

LOCK TABLES `user_depp` WRITE;
/*!40000 ALTER TABLE `user_depp` DISABLE KEYS */;
INSERT INTO `user_depp` VALUES (1,1,1),(3,1,2),(5,3,1),(9,3,2),(10,3,4),(4,3,6),(12,4,4),(11,4,5);
/*!40000 ALTER TABLE `user_depp` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-29 21:17:31
