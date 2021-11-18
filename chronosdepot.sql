-- MySQL dump 10.13  Distrib 5.5.60, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: chronosdepot
-- ------------------------------------------------------
-- Server version	5.5.60-0+deb7u1

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
-- Table structure for table `availables`
--

DROP TABLE IF EXISTS `availables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `availables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `start` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `availables_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `availables`
--

LOCK TABLES `availables` WRITE;
/*!40000 ALTER TABLE `availables` DISABLE KEYS */;
/*!40000 ALTER TABLE `availables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currencies` (
  `id` int(10) unsigned NOT NULL,
  `code` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (8,'ALL'),(12,'DZD'),(32,'ARS'),(36,'AUD'),(44,'BSD'),(48,'BHD'),(50,'BDT'),(51,'AMD'),(52,'BBD'),(60,'BMD'),(64,'BTN'),(68,'BOB'),(72,'BWP'),(84,'BZD'),(90,'SBD'),(96,'BND'),(104,'MMK'),(108,'BIF'),(116,'KHR'),(124,'CAD'),(132,'CVE'),(136,'KYD'),(144,'LKR'),(152,'CLP'),(156,'CNY'),(170,'COP'),(174,'KMF'),(188,'CRC'),(191,'HRK'),(192,'CUP'),(203,'CZK'),(208,'DKK'),(214,'DOP'),(222,'SVC'),(230,'ETB'),(232,'ERN'),(238,'FKP'),(242,'FJD'),(262,'DJF'),(270,'GMD'),(292,'GIP'),(320,'GTQ'),(324,'GNF'),(328,'GYD'),(332,'HTG'),(340,'HNL'),(344,'HKD'),(348,'HUF'),(352,'ISK'),(356,'INR'),(360,'IDR'),(364,'IRR'),(368,'IQD'),(376,'ILS'),(388,'JMD'),(392,'JPY'),(398,'KZT'),(400,'JOD'),(404,'KES'),(408,'KPW'),(410,'KRW'),(414,'KWD'),(417,'KGS'),(418,'LAK'),(422,'LBP'),(426,'LSL'),(430,'LRD'),(434,'LYD'),(446,'MOP'),(454,'MWK'),(458,'MYR'),(462,'MVR'),(478,'MRO'),(480,'MUR'),(484,'MXN'),(496,'MNT'),(498,'MDL'),(504,'MAD'),(512,'OMR'),(516,'NAD'),(524,'NPR'),(532,'ANG'),(533,'AWG'),(548,'VUV'),(554,'NZD'),(558,'NIO'),(566,'NGN'),(578,'NOK'),(586,'PKR'),(590,'PAB'),(598,'PGK'),(600,'PYG'),(604,'PEN'),(608,'PHP'),(634,'QAR'),(643,'RUB'),(646,'RWF'),(654,'SHP'),(678,'STD'),(682,'SAR'),(690,'SCR'),(694,'SLL'),(702,'SGD'),(704,'VND'),(706,'SOS'),(710,'ZAR'),(728,'SSP'),(748,'SZL'),(752,'SEK'),(756,'CHF'),(760,'SYP'),(764,'THB'),(776,'TOP'),(780,'TTD'),(784,'AED'),(788,'TND'),(800,'UGX'),(807,'MKD'),(818,'EGP'),(826,'GBP'),(834,'TZS'),(840,'USD'),(858,'UYU'),(860,'UZS'),(882,'WST'),(886,'YER'),(901,'TWD'),(931,'CUC'),(932,'ZWL'),(934,'TMT'),(936,'GHS'),(937,'VEF'),(938,'SDG'),(940,'UYI'),(941,'RSD'),(943,'MZN'),(944,'AZN'),(946,'RON'),(947,'CHE'),(948,'CHW'),(949,'TRY'),(950,'XAF'),(951,'XCD'),(952,'XOF'),(953,'XPF'),(955,'XBA'),(956,'XBB'),(957,'XBC'),(958,'XBD'),(959,'XAU'),(960,'XDR'),(961,'XAG'),(962,'XPT'),(963,'XTS'),(964,'XPD'),(965,'XUA'),(967,'ZMW'),(968,'SRD'),(969,'MGA'),(970,'COU'),(971,'AFN'),(972,'TJS'),(973,'AOA'),(974,'BYR'),(975,'BGN'),(976,'CDF'),(977,'BAM'),(978,'EUR'),(979,'MXV'),(980,'UAH'),(981,'GEL'),(984,'BOV'),(985,'PLN'),(986,'BRL'),(990,'CLF'),(994,'XSU'),(997,'USN'),(999,'XXX');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `image_versions`
--

DROP TABLE IF EXISTS `image_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image_versions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `public_path` varchar(255) NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `created` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `image_versions_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `public_path` varchar(255) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice_lines`
--

DROP TABLE IF EXISTS `invoice_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_lines` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `type` enum('task_type','task','worklog') DEFAULT NULL,
  `task_id` int(10) unsigned DEFAULT NULL,
  `worklog_id` int(10) unsigned DEFAULT NULL,
  `task_type_id` int(10) unsigned DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `work_time` int(10) unsigned DEFAULT NULL,
  `message` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `task_type_id` (`task_type_id`),
  KEY `worklog_id` (`worklog_id`),
  CONSTRAINT `invoice_lines_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  CONSTRAINT `invoice_lines_ibfk_2` FOREIGN KEY (`task_type_id`) REFERENCES `task_types` (`id`),
  CONSTRAINT `invoice_lines_ibfk_3` FOREIGN KEY (`worklog_id`) REFERENCES `worklogs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_lines`
--

LOCK TABLES `invoice_lines` WRITE;
/*!40000 ALTER TABLE `invoice_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_templates`
--

DROP TABLE IF EXISTS `invoice_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `series` varchar(64) DEFAULT NULL,
  `number` int(10) unsigned DEFAULT NULL,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `supplier_email` varchar(128) DEFAULT NULL,
  `supplier_misc` varchar(255) DEFAULT NULL,
  `supplier_rc_no` varchar(64) DEFAULT NULL,
  `supplier_vat_no` varchar(64) DEFAULT NULL,
  `supplier_city` varchar(255) DEFAULT NULL,
  `supplier_country` varchar(255) DEFAULT NULL,
  `supplier_postcode` varchar(255) DEFAULT NULL,
  `supplier_address_1` varchar(255) DEFAULT NULL,
  `supplier_address_2` varchar(255) DEFAULT NULL,
  `supplier_phone` varchar(64) DEFAULT NULL,
  `supplier_bank` varchar(255) DEFAULT NULL,
  `supplier_iban` varchar(255) DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `customer_name` int(255) DEFAULT NULL,
  `customer_email` varchar(128) DEFAULT NULL,
  `customer_misc` varchar(255) DEFAULT NULL,
  `customer_rc_no` varchar(64) DEFAULT NULL,
  `customer_vat_no` varchar(64) DEFAULT NULL,
  `customer_city` varchar(255) DEFAULT NULL,
  `customer_country` varchar(255) DEFAULT NULL,
  `customer_postcode` varchar(255) DEFAULT NULL,
  `customer_address_1` varchar(255) DEFAULT NULL,
  `customer_address_2` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(64) DEFAULT NULL,
  `customer_bank` varchar(255) DEFAULT NULL,
  `customer_iban` varchar(255) DEFAULT NULL,
  `currency_1` int(10) unsigned DEFAULT NULL,
  `currency_2` int(10) unsigned DEFAULT NULL,
  `due_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `invoice_templates_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`id`),
  CONSTRAINT `invoice_templates_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned DEFAULT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `logo_image_id` int(10) unsigned DEFAULT NULL,
  `series` varchar(64) DEFAULT NULL,
  `number` int(10) unsigned DEFAULT NULL,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `supplier_email` varchar(128) DEFAULT NULL,
  `supplier_misc` varchar(255) DEFAULT NULL,
  `supplier_rc_no` varchar(64) DEFAULT NULL,
  `supplier_vat_no` varchar(64) DEFAULT NULL,
  `supplier_city` varchar(255) DEFAULT NULL,
  `supplier_country` varchar(255) DEFAULT NULL,
  `supplier_postcode` varchar(255) DEFAULT NULL,
  `supplier_address_1` varchar(255) DEFAULT NULL,
  `supplier_address_2` varchar(255) DEFAULT NULL,
  `supplier_phone` varchar(64) DEFAULT NULL,
  `supplier_bank` varchar(255) DEFAULT NULL,
  `supplier_iban` varchar(255) DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(128) DEFAULT NULL,
  `customer_misc` varchar(255) DEFAULT NULL,
  `customer_rc_no` varchar(64) DEFAULT NULL,
  `customer_vat_no` varchar(64) DEFAULT NULL,
  `customer_city` varchar(255) DEFAULT NULL,
  `customer_country` varchar(255) DEFAULT NULL,
  `customer_postcode` varchar(255) DEFAULT NULL,
  `customer_address_1` varchar(255) DEFAULT NULL,
  `customer_address_2` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(64) DEFAULT NULL,
  `customer_bank` varchar(255) DEFAULT NULL,
  `customer_iban` varchar(255) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `sub_total_1` decimal(10,2) DEFAULT NULL,
  `sub_total_2` decimal(10,2) DEFAULT NULL,
  `total_1` decimal(10,2) DEFAULT NULL,
  `currency_1` int(10) unsigned DEFAULT NULL,
  `total_2` decimal(10,2) DEFAULT NULL,
  `currency_2` int(10) unsigned DEFAULT NULL,
  `created` int(10) unsigned DEFAULT NULL,
  `due_time` int(10) unsigned DEFAULT NULL,
  `payment_link` varchar(512) DEFAULT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `customer_id` (`customer_id`),
  KEY `project_id` (`project_id`),
  KEY `template_id` (`template_id`),
  KEY `currency_1` (`currency_1`),
  KEY `currency_2` (`currency_2`),
  KEY `logo_image_id` (`logo_image_id`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`id`),
  CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `invoices_ibfk_4` FOREIGN KEY (`template_id`) REFERENCES `invoice_templates` (`id`),
  CONSTRAINT `invoices_ibfk_5` FOREIGN KEY (`currency_1`) REFERENCES `currencies` (`id`),
  CONSTRAINT `invoices_ibfk_6` FOREIGN KEY (`currency_2`) REFERENCES `currencies` (`id`),
  CONSTRAINT `invoices_ibfk_7` FOREIGN KEY (`logo_image_id`) REFERENCES `images` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `type` enum('team_invite_request','team_join_request','project_add','project_leave','task_add','task_leave','task_complete','accept_team_invite','accept_team_join','team_leave','team_exclude','team_cancel_invite','team_cancel_join') NOT NULL,
  `info` varchar(1024) DEFAULT NULL,
  `status` enum('pending','viewed') NOT NULL DEFAULT 'pending',
  `created` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=127 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(2048) DEFAULT NULL,
  `created` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `positions_projects`
--

DROP TABLE IF EXISTS `positions_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions_projects` (
  `position_id` int(10) unsigned DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  KEY `position_id` (`position_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `positions_projects_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`),
  CONSTRAINT `positions_projects_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions_projects`
--

LOCK TABLES `positions_projects` WRITE;
/*!40000 ALTER TABLE `positions_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `positions_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions_task_types`
--

DROP TABLE IF EXISTS `positions_task_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions_task_types` (
  `position_id` int(10) unsigned DEFAULT NULL,
  `task_type_id` int(10) unsigned DEFAULT NULL,
  KEY `position_id` (`position_id`),
  KEY `task_type_id` (`task_type_id`),
  CONSTRAINT `positions_task_types_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`),
  CONSTRAINT `positions_task_types_ibfk_2` FOREIGN KEY (`task_type_id`) REFERENCES `task_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `positions_users`
--


DROP TABLE IF EXISTS `positions_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions_users` (
  `position_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  KEY `position_id` (`position_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `positions_users_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`),
  CONSTRAINT `positions_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_contacts`
--

DROP TABLE IF EXISTS `project_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_contacts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `type` enum('customer','spectator') DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `active` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `project_contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `project_contacts_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(512) DEFAULT NULL,
  `budget` int(11) DEFAULT NULL,
  `currency_id` int(11) unsigned DEFAULT NULL,
  `description` text,
  `created` int(11) NOT NULL,
  `status` enum('ongoing','hold','finished') NOT NULL,
  `picture` varchar(512) NOT NULL,
  `wage` decimal(11,2) DEFAULT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `currency_id` (`currency_id`),
  KEY `customer_id` (`customer_id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`template_id`) REFERENCES `invoice_templates` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_teams`
--

DROP TABLE IF EXISTS `projects_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_teams` (
  `project_id` int(10) unsigned DEFAULT NULL,
  `team_id` int(10) unsigned DEFAULT NULL,
  KEY `project_id` (`project_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `projects_teams_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `projects_teams_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_users`
--

DROP TABLE IF EXISTS `projects_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_users` (
  `project_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `projects_users_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `projects_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects_wages`
--

DROP TABLE IF EXISTS `projects_wages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects_wages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `project_id` int(10) unsigned DEFAULT NULL,
  `wage` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `projects_wages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `projects_wages_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reset_passwords`
--

DROP TABLE IF EXISTS `reset_passwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reset_passwords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `used` int(10) unsigned DEFAULT NULL,
  `expires` int(10) unsigned NOT NULL,
  `old_password` varchar(255) DEFAULT NULL,
  `status` enum('pending','used') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reset_passwords_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'login','Login privileges, granted after account confirmation'),(2,'admin','Administrative user, has access to everything.');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_users`
--

DROP TABLE IF EXISTS `roles_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `status_icons`
--

DROP TABLE IF EXISTS `status_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_icons` (
  `status` varchar(128) NOT NULL,
  `icon` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_icons`
--

LOCK TABLES `status_icons` WRITE;
/*!40000 ALTER TABLE `status_icons` DISABLE KEYS */;
INSERT INTO `status_icons` VALUES ('disabled','eye-close'),('finished','thumbs-up'),('ongoing','eye-open');
/*!40000 ALTER TABLE `status_icons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_types`
--

DROP TABLE IF EXISTS `task_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(2048) DEFAULT NULL,
  `created` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `task_types_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `level` int(1) NOT NULL DEFAULT '0',
  `project_id` int(10) unsigned NOT NULL,
  `task_type_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created` int(11) NOT NULL,
  `goal` varchar(512) DEFAULT NULL,
  `estimate` int(11) DEFAULT NULL,
  `work` int(11) NOT NULL DEFAULT '0',
  `budget` int(11) NOT NULL,
  `spent` decimal(11,2) DEFAULT NULL,
  `status` enum('ongoing','finished','disabled') NOT NULL DEFAULT 'ongoing',
  `description` text,
  `progress` int(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `tasks` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=603 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team_requests`
--

DROP TABLE IF EXISTS `team_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `type` enum('join','invite') NOT NULL DEFAULT 'invite',
  `created` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_idx` (`team_id`,`user_id`) COMMENT 'user_idx',
  KEY `user_id` (`user_id`),
  CONSTRAINT `team_requests_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `team_requests_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `image_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `public` int(1) NOT NULL DEFAULT '0',
  `created` int(10) unsigned DEFAULT NULL,
  `simple` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  CONSTRAINT `teams_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teams_users`
--

DROP TABLE IF EXISTS `teams_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams_users` (
  `team_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`team_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_notifications`
--

DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `content` text,
  `status` enum('viewed','new') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notifications`
--

LOCK TABLES `user_notifications` WRITE;
/*!40000 ALTER TABLE `user_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(254) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `headline` varchar(255) DEFAULT NULL,
  `password` varchar(64) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  `fb_id` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `description` varchar(2048) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created` int(11) NOT NULL,
  `image_id` int(10) unsigned DEFAULT NULL,
  `background` varchar(255) DEFAULT '#ffffff',
  `theme` varchar(255) DEFAULT NULL,
  `wage` decimal(11,2) DEFAULT NULL,
  `currency_id` int(10) unsigned DEFAULT NULL,
  `simple` int(1) NOT NULL DEFAULT '0',
  `date_format` varchar(64) NOT NULL DEFAULT 'YYYY-MM-DD',
  `firsttime` int(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`),
  KEY `image_id` (`image_id`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_task_types`
--

DROP TABLE IF EXISTS `users_task_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_task_types` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `task_type_id` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `user_task_type` (`user_id`,`task_type_id`),
  KEY `user_id` (`user_id`),
  KEY `task_type_id` (`task_type_id`),
  CONSTRAINT `users_task_types_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `users_task_types_ibfk_2` FOREIGN KEY (`task_type_id`) REFERENCES `task_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_tasks`
--

DROP TABLE IF EXISTS `users_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_tasks` (
  `task_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `users_tasks_idx` (`task_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `worklogs`
--

DROP TABLE IF EXISTS `worklogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `worklogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `start_time` int(11) DEFAULT NULL,
  `stop_time` int(11) DEFAULT NULL,
  `note` varchar(1024) DEFAULT NULL,
  `budget_spent` decimal(11,2) DEFAULT NULL,
  `modified` int(10) unsigned DEFAULT NULL,
  `original_duration` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=766 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-10 22:39:35
