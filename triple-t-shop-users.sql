Warning: A partial dump from a server that has GTIDs will by default include the GTIDs of all transactions, even those that changed suppressed parts of the database. If you don't want to restore GTIDs, pass --set-gtid-purged=OFF. To make a complete dump, pass --all-databases --triggers --routines --events. 
Warning: A dump from a server that has GTIDs enabled will by default include the GTIDs of all transactions, even those that were executed during its extraction and might not be represented in the dumped data. This might result in an inconsistent data dump. 
In order to ensure a consistent backup of the database, pass --single-transaction or --lock-all-tables or --source-data. 
-- MySQL dump 10.13  Distrib 8.4.0, for macos13.2 (arm64)
--
-- Host: localhost    Database: wordpress
-- ------------------------------------------------------
-- Server version	9.6.0

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'a2ded0d2-57d6-11f1-8b47-c331accbf464:1-11240';

--
-- Table structure for table `wp_users`
--

DROP TABLE IF EXISTS `wp_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_users` (
  `ID` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_status` int NOT NULL DEFAULT '0',
  `display_name` varchar(250) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_users`
--

LOCK TABLES `wp_users` WRITE;
/*!40000 ALTER TABLE `wp_users` DISABLE KEYS */;
INSERT INTO `wp_users` VALUES (1,'admin','$wp$2y$12$fugf2C1GmYKjedpxrKaPlO9RX/yN0zJJ5aBfneB2IXtFyurIV5IqK','admin','you@example.com','http://localhost:8080/wordpress','2026-05-25 03:12:07','',0,'admin'),(8,'arimartono1','$wp$2y$12$53zDE4X2.8IKBumA9iVT2ObO5ygzY72/ihJOg.ME/.Bt.EolWw4ce','arimartono1','arimartono1@gmail.com','','2026-05-26 08:05:59','',0,'arimartonosmegma'),(14,'endtest','$wp$2y$12$WFcqmU1uOspt1MBUJiXPMOgd7uvsmZ8a6MnTvsvy2H5.V.XuEC6/e','endtest','endtest@final.com','','2026-05-28 02:26:28','',0,'endtest');
/*!40000 ALTER TABLE `wp_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_usermeta`
--

DROP TABLE IF EXISTS `wp_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=306 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_usermeta`
--

LOCK TABLES `wp_usermeta` WRITE;
/*!40000 ALTER TABLE `wp_usermeta` DISABLE KEYS */;
INSERT INTO `wp_usermeta` VALUES (1,1,'nickname','admin'),(2,1,'first_name',''),(3,1,'last_name',''),(4,1,'description',''),(5,1,'rich_editing','true'),(6,1,'syntax_highlighting','true'),(7,1,'comment_shortcuts','false'),(8,1,'admin_color','modern'),(9,1,'use_ssl','0'),(10,1,'show_admin_bar_front','true'),(11,1,'locale',''),(12,1,'wp_capabilities','a:1:{s:13:\"administrator\";b:1;}'),(13,1,'wp_user_level','10'),(14,1,'dismissed_wp_pointers',''),(15,1,'show_welcome_panel','1'),(161,8,'nickname','arimartono1'),(162,8,'first_name','Ari'),(163,8,'last_name','Martono'),(164,8,'description',''),(165,8,'rich_editing','true'),(166,8,'syntax_highlighting','true'),(167,8,'comment_shortcuts','false'),(168,8,'admin_color','modern'),(169,8,'use_ssl','0'),(170,8,'show_admin_bar_front','true'),(171,8,'locale',''),(172,8,'wp_capabilities','a:1:{s:8:\"customer\";b:1;}'),(173,8,'wp_user_level','0'),(174,8,'_wc_order_attribution_source_type','typein'),(175,8,'_wc_order_attribution_utm_source','(direct)'),(176,8,'_wc_order_attribution_session_entry','http://localhost:8080/'),(177,8,'_wc_order_attribution_session_start_time','2026-05-25 03:25:12'),(178,8,'_wc_order_attribution_session_pages','23'),(179,8,'_wc_order_attribution_session_count','4'),(180,8,'_wc_order_attribution_user_agent','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36'),(181,8,'_wc_order_attribution_device_type','Desktop'),(182,8,'last_update','1779950570'),(185,8,'wc_last_active','1780361251'),(186,8,'billing_first_name','Ari'),(187,8,'billing_last_name','Martono'),(222,1,'_woocommerce_persistent_cart_1','a:1:{s:4:\"cart\";a:0:{}}'),(238,8,'billing_address_1','Komplesk Gudang Peluru'),(239,8,'billing_address_2','676767'),(240,8,'billing_city','Jakarta'),(241,8,'billing_state','JK'),(242,8,'billing_postcode','50333'),(243,8,'billing_country','ID'),(244,8,'billing_email','arimartono1@gmail.com'),(278,14,'nickname','endtest'),(279,14,'first_name',''),(280,14,'last_name',''),(281,14,'description',''),(282,14,'rich_editing','true'),(283,14,'syntax_highlighting','true'),(284,14,'comment_shortcuts','false'),(285,14,'admin_color','modern'),(286,14,'use_ssl','0'),(287,14,'show_admin_bar_front','true'),(288,14,'locale',''),(289,14,'wp_capabilities','a:1:{s:10:\"subscriber\";b:1;}'),(290,14,'wp_user_level','0'),(291,14,'dismissed_wp_pointers',''),(293,8,'billing_phone','0811157480'),(294,8,'shipping_first_name','Ari '),(295,8,'shipping_last_name','Martono'),(296,8,'shipping_address_1','Kompleks'),(297,8,'shipping_city','Jakarta'),(298,8,'shipping_state','JK'),(299,8,'shipping_postcode','12309'),(300,8,'shipping_country','ID'),(302,8,'_woocommerce_persistent_cart_1','a:1:{s:4:\"cart\";a:0:{}}');
/*!40000 ALTER TABLE `wp_usermeta` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-02  8:57:01
