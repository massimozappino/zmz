-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.1.50


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema zmz
--

CREATE DATABASE IF NOT EXISTS zmz;
USE zmz;

--
-- Definition of table `zmz`.`groups`
--

DROP TABLE IF EXISTS `zmz`.`groups`;
CREATE TABLE  `zmz`.`groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(20) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zmz`.`groups`
--

/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
LOCK TABLES `groups` WRITE;
INSERT INTO `zmz`.`groups` VALUES  (1,'guest'),
 (2,'user'),
 (100,'admin');
UNLOCK TABLES;
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;


--
-- Definition of table `zmz`.`sessions`
--

DROP TABLE IF EXISTS `zmz`.`sessions`;
CREATE TABLE  `zmz`.`sessions` (
  `session_id` char(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_creation` datetime DEFAULT NULL,
  `date_last_access` datetime DEFAULT NULL,
  `remember` tinyint(4) DEFAULT '0',
  `hostname` varchar(128) NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zmz`.`sessions`
--

/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
LOCK TABLES `sessions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;


--
-- Definition of table `zmz`.`users`
--

DROP TABLE IF EXISTS `zmz`.`users`;
CREATE TABLE  `zmz`.`users` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `password` char(64) NOT NULL,
  `email` varchar(200) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `surname` varchar(30) DEFAULT NULL,
  `locale` varchar(6) DEFAULT NULL,
  `timezone` varchar(30) DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_registration` datetime DEFAULT NULL,
  `date_activation` datetime DEFAULT NULL,
  `code` char(40) DEFAULT NULL,
  `date_code` datetime DEFAULT NULL,
  `new_email` varchar(200) DEFAULT NULL,
  `code_email` char(40) DEFAULT NULL,
  `date_code_email` datetime DEFAULT NULL,
  `group_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zmz`.`users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
LOCK TABLES `users` WRITE;
INSERT INTO `zmz`.`users` VALUES  (8,'massimo.zappino','123456','massimo.zappino@gmail.com',NULL,NULL,'en','Europe/Rome',1,'2011-03-19 10:05:56','2011-03-19 10:06:14',NULL,NULL,NULL,NULL,NULL,2),
 (9,'massimo.zappino666','111111','qqq@rrrrr.it',NULL,NULL,'it_IT','Europe/Rome',0,'2011-03-19 16:15:41',NULL,'152f9afd97a3dd4d9033a224bda1dccff590e80b','2011-03-19 16:15:41',NULL,NULL,NULL,2);
UNLOCK TABLES;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
