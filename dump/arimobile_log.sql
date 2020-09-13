-- MySQL dump 10.13  Distrib 5.7.30, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: arimobile_log
-- ------------------------------------------------------
-- Server version	8.0.20

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
-- Table structure for table `data`
--

DROP TABLE IF EXISTS `data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_log` int unsigned NOT NULL DEFAULT '0',
  `old` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `new` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87717 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data`
--

LOCK TABLES `data` WRITE;
/*!40000 ALTER TABLE `data` DISABLE KEYS */;
INSERT INTO `data` VALUES (87620,9377,NULL,'1','id_perm'),(87621,9377,NULL,'admin','name'),(87622,9377,NULL,'admin','username'),(87623,9377,NULL,'$2y$10$4SMPNZ0W8B8.wu9/PvfyvePDL0.Whhw63kco0bCFHqVuza1UjSUhW','password'),(87624,9377,NULL,'active','state'),(87625,9377,NULL,'0','change_password'),(87626,9377,NULL,'ku','lang'),(87627,9377,NULL,'07505149171','mobile'),(87628,9377,NULL,'sabina.diako@gmail.com','email'),(87629,9378,NULL,'7','id_user'),(87630,9378,NULL,'6','id_depp'),(87631,9379,'1',NULL,'id'),(87632,9379,'1',NULL,'id_user'),(87633,9379,'1',NULL,'id_depp'),(87634,9380,'3',NULL,'id'),(87635,9380,'1',NULL,'id_user'),(87636,9380,'2',NULL,'id_depp'),(87637,9381,'4',NULL,'id'),(87638,9381,'3',NULL,'id_user'),(87639,9381,'6',NULL,'id_depp'),(87640,9382,'11',NULL,'id'),(87641,9382,'4',NULL,'id_user'),(87642,9382,'5',NULL,'id_depp'),(87643,9383,'12',NULL,'id'),(87644,9383,'4',NULL,'id_user'),(87645,9383,'4',NULL,'id_depp'),(87646,9384,'13',NULL,'id'),(87647,9384,'5',NULL,'id_user'),(87648,9384,'6',NULL,'id_depp'),(87649,9385,'14',NULL,'id'),(87650,9385,'6',NULL,'id_user'),(87651,9385,'6',NULL,'id_depp'),(87652,9386,'17',NULL,'id'),(87653,9386,'بۆکان',NULL,'name'),(87654,9386,'bokan',NULL,'detail'),(87655,9387,'1',NULL,'id'),(87656,9387,'1',NULL,'id_branch'),(87657,9387,'10',NULL,'id_cash'),(87658,9387,'11',NULL,'id_store'),(87659,9387,'piece',NULL,'type'),(87660,9387,'108',NULL,'code'),(87661,9387,'Mobile Pieces',NULL,'name'),(87662,9387,'berderki sara',NULL,'address'),(87663,9387,'13123',NULL,'phone'),(87664,9387,'قەتعەکانی مۆبایل',NULL,'detail'),(87665,9388,'2',NULL,'id'),(87666,9388,'1',NULL,'id_branch'),(87667,9388,'10',NULL,'id_cash'),(87668,9388,'11',NULL,'id_store'),(87669,9388,'repair_mobile',NULL,'type'),(87670,9388,'106',NULL,'code'),(87671,9388,'Mobile Repair',NULL,'name'),(87672,9388,'',NULL,'address'),(87673,9388,'',NULL,'phone'),(87674,9388,'چاککردنەوەی مۆبایل',NULL,'detail'),(87675,9389,'3',NULL,'id'),(87676,9389,'2',NULL,'id_branch'),(87677,9389,'10',NULL,'id_cash'),(87678,9389,'11',NULL,'id_store'),(87679,9389,'piece',NULL,'type'),(87680,9389,'200',NULL,'code'),(87681,9389,'Software',NULL,'name'),(87682,9389,'',NULL,'address'),(87683,9389,'',NULL,'phone'),(87684,9389,'',NULL,'detail'),(87685,9390,'4',NULL,'id'),(87686,9390,'1',NULL,'id_branch'),(87687,9390,'10',NULL,'id_cash'),(87688,9390,'11',NULL,'id_store'),(87689,9390,'camera',NULL,'type'),(87690,9390,'032',NULL,'code'),(87691,9390,'Camera',NULL,'name'),(87692,9390,'',NULL,'address'),(87693,9390,'',NULL,'phone'),(87694,9390,'',NULL,'detail'),(87695,9391,'5',NULL,'id'),(87696,9391,'1',NULL,'id_branch'),(87697,9391,'10',NULL,'id_cash'),(87698,9391,'11',NULL,'id_store'),(87699,9391,'repair_computer',NULL,'type'),(87700,9391,'109',NULL,'code'),(87701,9391,'چاک کردنەوەی لەپتۆپ',NULL,'name'),(87702,9391,'',NULL,'address'),(87703,9391,'',NULL,'phone'),(87704,9391,'',NULL,'detail'),(87705,9392,'1',NULL,'id'),(87706,9392,'1',NULL,'id_depp'),(87707,9392,'5',NULL,'id_cat'),(87708,9393,'3',NULL,'id'),(87709,9393,'2',NULL,'id_depp'),(87710,9393,'5',NULL,'id_cat'),(87711,9394,'5',NULL,'id'),(87712,9394,'1',NULL,'id_depp'),(87713,9394,'4',NULL,'id_cat'),(87714,9395,'6',NULL,'id'),(87715,9395,'5',NULL,'id_depp'),(87716,9395,'3',NULL,'id_cat');
/*!40000 ALTER TABLE `data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `table` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `action` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `detail` text CHARACTER SET utf8 COLLATE utf8_bin,
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `id_table` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9396 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (9377,3,'2020-07-15 22:18:40','user','add','','::1',7),(9378,7,'2020-07-15 22:19:27','user_depp','add','','::1',15),(9379,7,'2020-07-15 22:19:36','user_depp','delete','','::1',1),(9380,7,'2020-07-15 22:19:39','user_depp','delete','','::1',3),(9381,7,'2020-07-15 22:19:41','user_depp','delete','','::1',4),(9382,7,'2020-07-15 22:19:43','user_depp','delete','','::1',11),(9383,7,'2020-07-15 22:19:47','user_depp','delete','','::1',12),(9384,7,'2020-07-15 22:19:49','user_depp','delete','','::1',13),(9385,7,'2020-07-15 22:19:51','user_depp','delete','','::1',14),(9386,7,'2020-07-15 22:21:39','city','delete','','::1',17),(9387,7,'2020-07-15 22:21:56','depp','delete','','::1',1),(9388,7,'2020-07-15 22:22:01','depp','delete','','::1',2),(9389,7,'2020-07-15 22:22:05','depp','delete','','::1',3),(9390,7,'2020-07-15 22:22:08','depp','delete','','::1',4),(9391,7,'2020-07-15 22:22:12','depp','delete','','::1',5),(9392,7,'2020-07-15 22:22:26','depp_cat','delete','','::1',1),(9393,7,'2020-07-15 22:22:28','depp_cat','delete','','::1',3),(9394,7,'2020-07-15 22:22:32','depp_cat','delete','','::1',5),(9395,7,'2020-07-15 22:22:35','depp_cat','delete','','::1',6);
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-07-18 23:47:59
