CREATE DATABASE  IF NOT EXISTS `todomasterdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `todomasterdb`;
-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: todomasterdb
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activitylogs`
--

DROP TABLE IF EXISTS `activitylogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activitylogs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activitylogs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activitylogs`
--

LOCK TABLES `activitylogs` WRITE;
/*!40000 ALTER TABLE `activitylogs` DISABLE KEYS */;
INSERT INTO `activitylogs` VALUES (1,27,'Registered a new account','2025-03-25 09:29:01'),(2,27,'Created a new category: Work Tasks','2025-03-25 09:29:01'),(3,27,'Added a new priority: High','2025-03-25 09:29:01'),(4,27,'Created a new task: Complete project report','2025-03-25 09:29:01');
/*!40000 ALTER TABLE `activitylogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Work Tasks'),(2,'Work Tasks'),(3,'Work Tasks'),(4,'Work Tasks'),(5,'Work Tasks'),(6,'Personal'),(7,'Personal');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `priority`
--

DROP TABLE IF EXISTS `priority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `priority` (
  `priority_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `color` varchar(50) NOT NULL,
  PRIMARY KEY (`priority_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `priority`
--

LOCK TABLES `priority` WRITE;
/*!40000 ALTER TABLE `priority` DISABLE KEYS */;
INSERT INTO `priority` VALUES (1,'Low','#00FF00'),(2,'Medium','#FFFF00'),(3,'High','#FF0000'),(4,'High','#FF0000'),(5,'High','#FF0000'),(6,'Medium','#FFA500'),(7,'Medium','#FFA500');
/*!40000 ALTER TABLE `priority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `status` enum('completed','incompleted','toDo') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`task_id`),
  KEY `user_id` (`user_id`),
  KEY `fk_category` (`priority_id`),
  KEY `fk_tasks_categories` (`category_id`),
  CONSTRAINT `fk_category` FOREIGN KEY (`priority_id`) REFERENCES `priority` (`priority_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_priority` FOREIGN KEY (`priority_id`) REFERENCES `priority` (`priority_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_tasks_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (5,12,'Complete project report','2025-04-10 15:00:00','toDo','2025-03-23 21:28:56',NULL,2),(6,16,'Complete project report','2025-04-10 15:00:00','toDo','2025-03-25 09:09:19',NULL,3),(7,18,'Complete project report','2025-04-10 15:00:00','toDo','2025-03-25 09:10:48',4,4),(8,27,'Complete project report','2025-04-10 15:00:00','toDo','2025-03-25 09:29:01',5,5);
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'Jane Doe','jane@example.com','$2y$10$a/8Yj3JeGWw6ZuvOmObrduat.QPZXyicWVSB8YIwE3K.2ubyAUDVe','user','2025-03-23 20:43:29'),(7,'Angelina Jolie','angelina@example.com','$2y$10$RTYNp/egTST1Vbs/S.AYge/ChtGXr1audszu4tE3BDUPL/N9YvkA6','user','2025-03-23 20:50:45'),(8,'John Smith','johnsmith@example.com','$2y$10$4/IgA9vK5skqBNQcMQMZ6ubutHH3in8jJYIetfcXB6t7kfAMTABqy','user','2025-03-23 21:10:02'),(10,'Anna Smith','anna@example.com','$2y$10$t4iL9RaHuYD6dvtot.hJD.OMb5R9wfK/4OBKfRNV23jyNhoSdA5c.','user','2025-03-23 21:28:02'),(12,'Anne Smith','anne@example.com','$2y$10$mjWkbvd5WVC2/7dWUyF4rutgOd096NcmgnJHRqEdGXt4/HTZESIMe','user','2025-03-23 21:28:56'),(14,'Anne Smith','blabla@example.com','$2y$10$t9OCod4t/x/Ld/OXyU0TKeGH8CgljJTckb3ytdFDZ1uR3u0Fr1TNi','user','2025-03-25 09:07:55'),(16,'Ena Slipicevic','enaSS@example.com','$2y$10$gUQ6YucoyBjJb.kOr4uF1ejEM79Z4FHCVkqo5UsMiMIpahI46nx/K','user','2025-03-25 09:09:19'),(18,'Ena Slipicevic','enaSS22@example.com','$2y$10$2x6.uaoPpyd5q8EP4jG3fOojfcHZXN0w0nEVHAaBNfUBmVeSewJsi','user','2025-03-25 09:10:48'),(20,'Ena Slipicevic','enaSS225@example.com','$2y$10$kqUnMeBjENRmU8Ev3HcVd.w4vZQYiaPrKE/TTjuorXvLHell/w2Pu','user','2025-03-25 09:20:04'),(22,'Ena Slipicevic','enaSS2255@example.com','$2y$10$fEhZQOdf3c3R4ysksiepeuOro1woDpvbCZiJZJFoJwds9Mm8xKPWm','user','2025-03-25 09:22:51'),(24,'Ena Slipicevic','enaSS22515@example.com','$2y$10$DjbAZ1XJ9niXzwj2025EL.kLKaZ9J6QQp5r/JRDhBv5Hl2OXFVS2e','user','2025-03-25 09:27:06'),(26,'Ena Slipicevic','enaSS225125@example.com','$2y$10$eXffESWJkfmtZXbrCh/XKOEzcYZqz5Mk3qmdhJsag48tD/NxoXw/e','user','2025-03-25 09:28:17'),(27,'Ena Slipicevic','enaSS2251125@example.com','$2y$10$6T07LbhOlE6kGu8KsIAE4eOubwgbJZ8qEf8h5yPOd8xpeGq3.HAlm','user','2025-03-25 09:29:01');
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

-- Dump completed on 2025-03-25 11:26:29
