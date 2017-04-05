-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: ProteinSim
-- ------------------------------------------------------
-- Server version	5.7.17-0ubuntu0.16.04.1

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
  `pdbFile` mediumblob,
  `simulationName` varchar(45) NOT NULL,
  `description` text,
  `startTime` datetime DEFAULT NULL,
  `endTime` datetime DEFAULT NULL,
  `ETA` datetime DEFAULT NULL,
  `results` varchar(200) DEFAULT NULL,
  `queuePosition` int(11) DEFAULT NULL,
  `duration` decimal(20,5) DEFAULT NULL,
  `temperature` decimal(20,5) DEFAULT NULL,
  PRIMARY KEY (`mutations`,`pdbFileName`),
  KEY `username_idx` (`username`),
  CONSTRAINT `username` FOREIGN KEY (`username`) REFERENCES `Users` (`username`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Simulations`
--

LOCK TABLES `Simulations` WRITE;
/*!40000 ALTER TABLE `Simulations` DISABLE KEYS */;
INSERT INTO `Simulations` VALUES ('A123B','UHRF1_TTD-PHD_No_Zinc.pdb','jginnard','','Sim3','123',NULL,NULL,NULL,NULL,3,NULL,NULL),('A123F','UHRF1_TTD-PHD_No_Zinc.pdb','jginnard','UHRF1_TTD-PHD_No_Zinc.pdb','Sim5','new sim',NULL,NULL,NULL,NULL,4,NULL,NULL),('Y100A, Y200A, Y300A','UHRF1_TTD-PHD_Unmodified_H3.pdb','jginnard','UHRF1_TTD-PHD_Unmodified_H3.pdb','4/5/2017','Test',NULL,NULL,NULL,NULL,NULL,2.00000,40.00000),('Y100B, Y200B, Y300B','UHRF1_TTD-PHD_Unmodified_H3.pdb','jginnard','UHRF1_TTD-PHD_Unmodified_H3.pdb','4/5/2017','Test',NULL,NULL,NULL,NULL,NULL,2.00000,40.00000),('Y200A','nvt.mdp','jginnard','','Name','123',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `Simulations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `username` varchar(45) NOT NULL,
  `firstName` varchar(45) NOT NULL,
  `lastName` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `type` varchar(45) NOT NULL,
  `googleId` decimal(21,0) DEFAULT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `googleId_UNIQUE` (`googleId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES ('bginnard','Brendan','Ginnard','bginnard@emich.edu','pending',NULL),('jginnard','Jeremy','Ginnard','jginnard@emich.edu','Admin',NULL),('sginnard','Shane','Ginnard','sginnard@emich.edu','Admin',NULL);
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

-- Dump completed on 2017-04-05 16:57:21
