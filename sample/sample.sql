-- MySQL dump 10.13  Distrib 5.6.45-86.1, for Linux (x86_64)
--
-- Host: localhost    Database: anonymize_sample
-- ------------------------------------------------------
-- Server version	5.6.45-86.1

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
-- Table structure for table `example_1`
--

DROP TABLE IF EXISTS `example_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `example_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(50) DEFAULT NULL,
  `json` text,
  `dated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dated` (`dated`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `example_1`
--

LOCK TABLES `example_1` WRITE;
/*!40000 ALTER TABLE `example_1` DISABLE KEYS */;
INSERT INTO `example_1` (`id`, `fname`, `json`, `dated`, `comment`) VALUES (1,'Alex','{}','2020-02-20 02:20:02','Lorem ipsum dolor sit amet'),(2,'Gigi','{\"key1\":\"test data\",\"key2\":{\"0\":1,\"1\":2,\"2\":3,\"key2-1\":\"yellow\"},\"0\":1,\"1\":2,\"2\":3}','1998-02-02 02:02:02','My email is asd@asd.com'),(3,'Bjorg','[]','1980-10-10 10:10:10','Hello world'),(4,'Artemis','[1,2,3]','2020-01-01 01:01:01','Lorem Ipsum este pur şi simplu o machetă pentru text a industriei tipografice. Lorem Ipsum a fost macheta standard a industriei încă din secolul al XVI-lea, când un tipograf anonim a luat o planşetă de litere şi le-a amestecat pentru a crea o carte demonstrativă pentru literele respective. Nu doar că a supravieţuit timp de cinci secole, dar şi a facut saltul în tipografia electronică practic neschimbată. A fost popularizată în anii \'60 odată cu ieşirea colilor Letraset care conţineau pasaje Lorem Ipsum, iar mai recent, prin programele de publicare pentru calculator, ca Aldus PageMaker care includeau versiuni de Lorem Ipsum.');
/*!40000 ALTER TABLE `example_1` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `example_2`
--

DROP TABLE IF EXISTS `example_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `example_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(15) NOT NULL DEFAULT '0',
  `value` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `example_2`
--

LOCK TABLES `example_2` WRITE;
/*!40000 ALTER TABLE `example_2` DISABLE KEYS */;
INSERT INTO `example_2` (`id`, `key`, `value`) VALUES (1,'EMAIL','test@email.com'),(2,'PHONE','0700111222'),(3,'COMMENT','\'sup world'),(4,'FISCAL_CODE','RO123123');
INSERT INTO `example_2` (`id`, `key`, `value`) VALUES (5,'EMAIL','test@email.com'),(6,'PHONE','0700111222'),(7,'COMMENT','\'sup world'),(8,'FISCAL_CODE','RO123123'),(6,'PHONE','0700111222');
/*!40000 ALTER TABLE `example_2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `example_3`
--

DROP TABLE IF EXISTS `example_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `example_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `not_needed` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `example_3`
--

LOCK TABLES `example_3` WRITE;
/*!40000 ALTER TABLE `example_3` DISABLE KEYS */;
INSERT INTO `example_3` (`id`, `not_needed`) VALUES (1,'wer'),(2,'asd'),(3,'junk');
/*!40000 ALTER TABLE `example_3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `example_4`
--

DROP TABLE IF EXISTS `example_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `example_4` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `example_4`
--

LOCK TABLES `example_4` WRITE;
/*!40000 ALTER TABLE `example_4` DISABLE KEYS */;
INSERT INTO `example_4` (`id`, `name`) VALUES (1,'asd'),(2,'qwe');
INSERT INTO `example_4` (`id`, `name`) VALUES (3,'asd'),(4,'qwe');
INSERT INTO `example_4` (`id`, `name`) VALUES (5,'asd'),(6,'qwe');
INSERT INTO `example_4` (`id`, `name`) VALUES (7,'asd'),(8,'qwe');
/*!40000 ALTER TABLE `example_4` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-02-21 11:29:52
