-- MySQL dump 10.13  Distrib 5.6.12, for Win64 (x86_64)
--
-- Host: localhost    Database: divinelegy
-- ------------------------------------------------------
-- Server version	5.6.12

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
-- Table structure for table `artists`
--

DROP TABLE IF EXISTS `artists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artists` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `artists`
--

LOCK TABLES `artists` WRITE;
/*!40000 ALTER TABLE `artists` DISABLE KEYS */;
INSERT INTO `artists` VALUES (1,'atpunk01'),(2,'FaggotForce');
/*!40000 ALTER TABLE `artists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simfiles`
--

DROP TABLE IF EXISTS `simfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simfiles` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `artist_id` mediumint(8) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `bpm_high` int(11) NOT NULL,
  `bpm_low` int(11) NOT NULL,
  `bpm_changes` bit(1) NOT NULL,
  `stops` bit(1) NOT NULL,
  `fg_changes` bit(1) NOT NULL,
  `bg_changes` bit(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simfiles`
--

LOCK TABLES `simfiles` WRITE;
/*!40000 ALTER TABLE `simfiles` DISABLE KEYS */;
INSERT INTO `simfiles` VALUES (1,'BaBoom',1,1,177,177,'','\0','\0','\0'),(2,'Some Faggot Chart',2,2,230,230,'\0','\0','\0','\0'),(3,'More Bullshit',2,2,210,210,'\0','\0','\0','\0');
/*!40000 ALTER TABLE `simfiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `step_artists`
--

DROP TABLE IF EXISTS `step_artists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `step_artists` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `user_id` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `step_artists`
--

LOCK TABLES `step_artists` WRITE;
/*!40000 ALTER TABLE `step_artists` DISABLE KEYS */;
INSERT INTO `step_artists` VALUES (1,'(-[Jayce]-)',1),(2,'Zaia',2),(3,'Serenade',2);
/*!40000 ALTER TABLE `step_artists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `steps`
--

DROP TABLE IF EXISTS `steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `steps` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `simfile_id` mediumint(8) unsigned NOT NULL,
  `mode` enum('dance-single','dance-double') NOT NULL,
  `rating` int(10) unsigned NOT NULL,
  `difficulty` enum('beginner','easy','medium','hard','challenge','edit') NOT NULL,
  `step_artist_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `steps`
--

LOCK TABLES `steps` WRITE;
/*!40000 ALTER TABLE `steps` DISABLE KEYS */;
INSERT INTO `steps` VALUES (1,1,'dance-single',10,'challenge',1),(2,1,'dance-single',9,'hard',1),(3,1,'dance-single',7,'medium',1),(4,1,'dance-single',4,'easy',1),(5,1,'dance-single',2,'beginner',1),(6,2,'dance-single',15,'beginner',2),(7,3,'dance-single',16,'beginner',3);
/*!40000 ALTER TABLE `steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'jayce@divinelegy.com','Jayce'),(2,'chino@chino.net','Chino');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_meta`
--

DROP TABLE IF EXISTS `users_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_meta` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_meta`
--

LOCK TABLES `users_meta` WRITE;
/*!40000 ALTER TABLE `users_meta` DISABLE KEYS */;
INSERT INTO `users_meta` VALUES (1,1,'Jayce','Newton','Australia'),(2,2,'Chino','Wood','Australia');
/*!40000 ALTER TABLE `users_meta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-31 19:53:16
