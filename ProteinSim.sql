-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: ProteinSim
-- ------------------------------------------------------
-- Server version	5.7.17-0ubuntu0.16.04.2

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
-- Table structure for table `Simulations`
--

DROP TABLE IF EXISTS `Simulations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Simulations` (
  `mutations` varchar(500) NOT NULL,
  `pdbFileName` varchar(45) NOT NULL,
  `username` varchar(45) NOT NULL,
  `simulationName` varchar(45) NOT NULL,
  `description` text,
  `startTime` datetime DEFAULT NULL,
  `endTime` datetime DEFAULT NULL,
  `ETA` datetime DEFAULT NULL,
  `results` varchar(200) DEFAULT NULL,
  `queuePosition` int(11) DEFAULT NULL,
  `duration` decimal(20,5) DEFAULT NULL,
  `temperature` decimal(20,5) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`mutations`,`pdbFileName`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `username_idx` (`username`),
  CONSTRAINT `username` FOREIGN KEY (`username`) REFERENCES `Users` (`username`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Simulations`
--

LOCK TABLES `Simulations` WRITE;
/*!40000 ALTER TABLE `Simulations` DISABLE KEYS */;
INSERT INTO `Simulations` VALUES ('1234','1aki.pdb','jginnard','File Test 2','Test',NULL,NULL,NULL,NULL,0,2.00000,30.00000,10);
/*!40000 ALTER TABLE `Simulations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `username` varchar(45) DEFAULT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `email` varchar(45) NOT NULL,
  `type` varchar(45) DEFAULT 'pending',
  `oauth_uid` varchar(255) DEFAULT NULL,
  `oauth_provider` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`email`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `googleId_UNIQUE` (`oauth_uid`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES ('admin','GLR-MI-655','Admin','admin@a2cap.org','admin','108122486812011989499','google'),('dclarke1','Daniel','Clarke','dclarke1@emich.edu','standard','106175315101862413998','google'),('ikane','Isaac','Kane','ikane@emich.edu','admin','118317439164359735169','google'),('jeremyginnard','Jeremy','Ginnard','jeremyginnard@gmail.com','admin','117755420049357968206','google'),('jginnard','Jeremy','Ginnard','jginnard@emich.edu','admin','114609478627172520366','google'),('ssotok','Steven','Sotok','ssotok@emich.edu','admin','113857894688739323277','google');
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-04-18 22:24:20
