-- MySQL dump 10.13  Distrib 5.5.38, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: divinelegy
-- ------------------------------------------------------
-- Server version	5.5.38-0ubuntu0.12.04.1

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
) ENGINE=InnoDB AUTO_INCREMENT=360 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `downloads`
--

DROP TABLE IF EXISTS `downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `downloads` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` mediumint(8) unsigned NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `mimetype` varchar(255) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `uploaded` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=291 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mirrors`
--

DROP TABLE IF EXISTS `mirrors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mirrors` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) DEFAULT NULL,
  `file_id` mediumint(8) unsigned NOT NULL,
  `source` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `packs`
--

DROP TABLE IF EXISTS `packs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packs` (
  `title` varchar(255) NOT NULL,
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` mediumint(8) unsigned DEFAULT NULL,
  `user_id` mediumint(8) unsigned NOT NULL,
  `banner_file_id` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `bpm_changes` int(1) DEFAULT NULL,
  `stops` int(1) DEFAULT NULL,
  `fg_changes` int(1) DEFAULT NULL,
  `bg_changes` int(1) DEFAULT NULL,
  `banner_file_id` mediumint(8) unsigned DEFAULT NULL,
  `simfile_file_id` mediumint(8) unsigned DEFAULT NULL,
  `pack_id` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=427 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `difficulty` enum('Beginner','Easy','Medium','Hard','Challenge','Edit') NOT NULL,
  `step_artist_id` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `facebook_id` varchar(255) DEFAULT NULL,
  `quota` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-12-05 14:59:50
