-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cnsr_asunto
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asistencia`
--

DROP TABLE IF EXISTS `asistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idSesion` int(11) DEFAULT NULL,
  `idStudent` int(11) DEFAULT NULL,
  `idTipoAsistencia` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_asistencia_student` (`idStudent`),
  KEY `fk_asistencia_tipo` (`idTipoAsistencia`),
  KEY `idx_asistencia_idSesion` (`idSesion`),
  CONSTRAINT `fk_asistencia_sesion` FOREIGN KEY (`idSesion`) REFERENCES `asistencia_sesion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_asistencia_student` FOREIGN KEY (`idStudent`) REFERENCES `student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_asistencia_tipo` FOREIGN KEY (`idTipoAsistencia`) REFERENCES `tipoasistencia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=671 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia`
--

LOCK TABLES `asistencia` WRITE;
/*!40000 ALTER TABLE `asistencia` DISABLE KEYS */;
INSERT INTO `asistencia` VALUES (293,9,603,1),(294,9,604,1),(295,9,605,1),(296,9,606,1),(297,9,607,1),(298,9,608,1),(299,9,609,1),(300,9,610,1),(301,9,611,1),(302,9,612,1),(303,9,613,1),(304,9,614,1),(305,9,615,1),(306,9,616,1),(307,9,617,1),(308,9,618,1),(309,9,619,1),(310,9,620,1),(311,9,621,1),(312,9,622,1),(402,13,523,1),(403,13,524,1),(404,13,525,1),(405,13,526,1),(406,13,527,1),(407,13,528,1),(408,13,529,1),(409,13,530,1),(410,13,531,1),(411,13,532,1),(412,13,533,1),(413,13,534,1),(414,13,535,1),(415,13,536,1),(416,13,537,1),(417,13,538,1),(418,13,539,1),(419,13,540,1),(420,13,541,1),(421,13,542,1),(422,13,543,1),(423,14,482,1),(424,14,483,1),(425,14,484,1),(426,14,485,1),(427,14,486,1),(428,14,487,1),(429,14,488,1),(430,14,489,1),(431,14,490,1),(432,14,491,1),(433,14,492,1),(434,14,493,1),(435,14,494,1),(436,14,495,1),(437,14,496,1),(438,14,497,1),(439,14,498,1),(440,14,499,1),(441,14,500,1),(442,14,501,1),(443,15,482,1),(444,15,483,1),(445,15,484,1),(446,15,485,1),(447,15,486,1),(448,15,487,1),(449,15,488,1),(450,15,489,1),(451,15,490,1),(452,15,491,1),(453,15,492,1),(454,15,493,1),(455,15,494,1),(456,15,495,1),(457,15,496,1),(458,15,497,1),(459,15,498,1),(460,15,499,1),(461,15,500,1),(462,15,501,1),(463,16,1,1),(464,16,2,1),(465,16,3,1),(466,16,4,1),(467,16,5,1),(468,16,6,1),(469,16,7,1),(470,16,8,1),(471,16,9,1),(472,16,10,1),(473,16,11,1),(474,16,12,1),(475,16,13,1),(476,16,14,1),(477,16,15,1),(478,16,16,1),(479,16,17,1),(480,16,18,1),(481,16,19,1),(482,17,335,1),(483,17,336,1),(484,17,337,1),(485,17,338,1),(486,17,339,1),(487,17,340,1),(488,17,341,1),(489,17,342,1),(490,17,343,1),(491,17,344,1),(492,17,345,1),(493,17,346,1),(494,17,347,1),(495,17,348,1),(496,17,349,1),(497,17,350,1),(498,17,351,1),(499,18,440,1),(500,18,441,1),(501,18,442,1),(502,18,443,1),(503,18,444,1),(504,18,445,1),(505,18,446,1),(506,18,447,1),(507,18,448,1),(508,18,449,1),(509,18,450,1),(510,18,451,1),(511,18,452,1),(512,18,453,1),(513,18,454,1),(514,18,455,1),(515,18,456,1),(516,18,457,1),(517,18,458,1),(518,18,459,1),(519,18,460,1),(520,19,563,1),(521,19,564,1),(522,19,565,1),(523,19,566,1),(524,19,567,1),(525,19,568,1),(526,19,569,1),(527,19,570,1),(528,19,571,1),(529,19,572,1),(530,19,573,1),(531,19,574,1),(532,19,575,1),(533,19,576,1),(534,19,577,1),(535,19,578,1),(536,19,579,1),(537,19,580,1),(538,19,581,1),(539,19,582,1),(540,20,370,1),(541,20,371,1),(542,20,372,1),(543,20,373,1),(544,20,374,1),(545,20,375,1),(546,20,376,1),(547,20,377,1),(548,20,378,1),(549,20,379,1),(550,20,380,1),(551,20,381,1),(552,20,382,1),(553,20,383,1),(554,20,384,1),(555,20,385,1),(556,20,386,1),(557,20,387,1),(558,21,76,1),(559,21,77,1),(560,21,78,1),(561,21,79,1),(562,21,80,1),(563,21,81,1),(564,21,82,1),(565,21,83,1),(566,21,84,1),(567,21,85,1),(568,21,86,1),(569,21,87,1),(570,21,88,1),(571,21,89,1),(572,21,90,1),(573,21,91,1),(574,21,92,1),(575,21,93,1),(576,21,94,1),(577,22,563,1),(578,22,564,1),(579,22,565,1),(580,22,566,1),(581,22,567,1),(582,22,568,1),(583,22,569,1),(584,22,570,1),(585,22,571,1),(586,22,572,1),(587,22,573,1),(588,22,574,1),(589,22,575,1),(590,22,576,1),(591,22,577,1),(592,22,578,1),(593,22,579,1),(594,22,580,1),(595,22,581,1),(596,22,582,1),(597,23,39,1),(598,23,40,1),(599,23,41,1),(600,23,42,1),(601,23,43,1),(602,23,44,1),(603,23,45,1),(604,23,46,1),(605,23,47,1),(606,23,48,1),(607,23,49,1),(608,23,50,1),(609,23,51,1),(610,23,52,1),(611,23,53,1),(612,23,54,1),(613,23,55,1),(614,23,56,1),(615,24,114,1),(616,24,115,1),(617,24,116,1),(618,24,117,1),(619,24,118,1),(620,24,119,1),(621,24,120,1),(622,24,121,1),(623,24,122,1),(624,24,123,1),(625,24,124,1),(626,24,125,1),(627,24,126,1),(628,24,127,1),(629,24,128,1),(630,24,129,1),(631,24,130,1),(632,25,643,1),(633,25,644,1),(634,25,645,1),(635,25,646,1),(636,25,647,1),(637,25,648,1),(638,25,649,1),(639,25,650,1),(640,25,651,1),(641,25,652,1),(642,25,653,1),(643,25,654,1),(644,25,655,1),(645,25,656,1),(646,25,657,1),(647,25,658,1),(648,25,659,1),(649,25,660,1),(650,25,661,1),(651,25,662,1);
/*!40000 ALTER TABLE `asistencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistencia_sesion`
--

DROP TABLE IF EXISTS `asistencia_sesion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencia_sesion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idCorte` int(11) DEFAULT NULL,
  `idMateria` int(11) DEFAULT NULL,
  `nombreDelTema` varchar(255) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_as_sesion_corte` (`idCorte`),
  KEY `idx_as_sesion_materia` (`idMateria`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia_sesion`
--

LOCK TABLES `asistencia_sesion` WRITE;
/*!40000 ALTER TABLE `asistencia_sesion` DISABLE KEYS */;
INSERT INTO `asistencia_sesion` VALUES (9,1,1,'Dispositivos de la computadora','2026-02-03','2026-02-09 10:10:37'),(13,1,1,'Dispositivos de la computadora','2026-02-10','2026-02-10 08:48:40'),(14,1,1,'Dispositivos de la computadora','2026-02-10','2026-02-10 09:01:26'),(15,1,1,'Creación de carpeta e introducción a Word','2026-02-27','2026-02-10 09:04:35'),(16,1,1,'Partes de la computadora','2026-02-03','2026-02-10 09:05:41'),(17,1,1,'Dispositivos de la computadora','2026-02-10','2026-02-10 09:07:01'),(18,1,1,'Dispositivos de la computadora','2026-02-10','2026-02-10 09:07:15'),(19,1,1,'Dispositivos de la computadora','2026-02-10','2026-02-10 09:07:58'),(20,1,1,'Dispositivos de la computadora','2026-02-10','2026-02-10 09:09:46'),(21,1,1,'Partes de la computadora','2026-02-10','2026-02-10 09:10:24'),(22,1,1,'Creación de carpeta e introducción a Word','2026-02-10','2026-02-10 09:11:27'),(23,1,1,'Partes de la computadora','2026-02-10','2026-02-10 09:12:05'),(24,1,1,'Partes de la computadora','2026-02-10','2026-02-10 09:12:56'),(25,1,1,'Dispositivos de la computadora','2026-02-10','2026-02-10 09:13:45');
/*!40000 ALTER TABLE `asistencia_sesion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asunto`
--

DROP TABLE IF EXISTS `asunto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asunto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT NULL,
  `nota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tema` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idStudent` int(11) DEFAULT NULL,
  `statuss` int(11) DEFAULT NULL,
  `idMateria` int(11) DEFAULT NULL,
  `idCorte` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idStudentFK` (`idStudent`),
  KEY `idMateriaFK` (`idMateria`),
  CONSTRAINT `asunto_materia_fk` FOREIGN KEY (`idMateria`) REFERENCES `materia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asunto_student_fk` FOREIGN KEY (`idStudent`) REFERENCES `student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asunto`
--

LOCK TABLES `asunto` WRITE;
/*!40000 ALTER TABLE `asunto` DISABLE KEYS */;
INSERT INTO `asunto` VALUES (1,'2026-02-05 17:19:00','asdfasdfasdf','asdfasdf',1,0,1,1),(2,'2026-02-03 09:46:00','No trabajo','La computadora y sus partes',336,1,1,1),(3,'2026-02-03 09:48:00','Le falto terminarlo, por estar platicando','La computadora y sus partes',341,1,1,1),(4,'2026-02-06 09:50:00','No trabajo','La computadora y sus partes',540,1,1,1),(5,'2026-01-31 13:40:00','Eliminar','Dibujo de navidad',1,0,1,1);
/*!40000 ALTER TABLE `asunto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup`
--

DROP TABLE IF EXISTS `backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) NOT NULL,
  `fecha` datetime NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup`
--

LOCK TABLES `backup` WRITE;
/*!40000 ALTER TABLE `backup` DISABLE KEYS */;
INSERT INTO `backup` VALUES (1,'backup_2026-02-10_17-32-12.sql','2026-02-10 10:32:12','2026-02-10 17:32:12');
/*!40000 ALTER TABLE `backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corte`
--

DROP TABLE IF EXISTS `corte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corte`
--

LOCK TABLES `corte` WRITE;
/*!40000 ALTER TABLE `corte` DISABLE KEYS */;
INSERT INTO `corte` VALUES (1,'I Corte'),(2,'II Corte'),(3,'III Corte'),(4,'IV Corte');
/*!40000 ALTER TABLE `corte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `criterio`
--

DROP TABLE IF EXISTS `criterio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `criterio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) DEFAULT NULL,
  `puntos` int(11) DEFAULT NULL,
  `idIndicadorL` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idIndicadorL_FK` (`idIndicadorL`),
  CONSTRAINT `idIndicadorL_FK` FOREIGN KEY (`idIndicadorL`) REFERENCES `indicadorl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criterio`
--

LOCK TABLES `criterio` WRITE;
/*!40000 ALTER TABLE `criterio` DISABLE KEYS */;
/*!40000 ALTER TABLE `criterio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enlace`
--

DROP TABLE IF EXISTS `enlace`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enlace` (
  `idIndicador` int(11) NOT NULL,
  `idSeccion` int(11) NOT NULL,
  PRIMARY KEY (`idIndicador`,`idSeccion`),
  KEY `enlace_seccion_fk` (`idSeccion`),
  CONSTRAINT `enlace_indicador_fk` FOREIGN KEY (`idIndicador`) REFERENCES `indicadorl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `enlace_seccion_fk` FOREIGN KEY (`idSeccion`) REFERENCES `seccion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enlace`
--

LOCK TABLES `enlace` WRITE;
/*!40000 ALTER TABLE `enlace` DISABLE KEYS */;
/*!40000 ALTER TABLE `enlace` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indicadorl`
--

DROP TABLE IF EXISTS `indicadorl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `indicadorl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) DEFAULT NULL,
  `año` year(4) DEFAULT NULL,
  `idMateria` int(11) DEFAULT NULL,
  `idCorte` int(11) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idMateria_fk` (`idMateria`),
  KEY `fk_idCorte` (`idCorte`),
  CONSTRAINT `fk_idCorte` FOREIGN KEY (`idCorte`) REFERENCES `corte` (`id`),
  CONSTRAINT `idMateria_fk` FOREIGN KEY (`idMateria`) REFERENCES `materia` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indicadorl`
--

LOCK TABLES `indicadorl` WRITE;
/*!40000 ALTER TABLE `indicadorl` DISABLE KEYS */;
INSERT INTO `indicadorl` VALUES (10,'Informática',2026,1,1,4);
/*!40000 ALTER TABLE `indicadorl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materia`
--

DROP TABLE IF EXISTS `materia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materia`
--

LOCK TABLES `materia` WRITE;
/*!40000 ALTER TABLE `materia` DISABLE KEYS */;
INSERT INTO `materia` VALUES (1,'Informática');
/*!40000 ALTER TABLE `materia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota`
--

DROP TABLE IF EXISTS `nota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nota` int(11) DEFAULT NULL,
  `idStudent` int(11) DEFAULT NULL,
  `idCriterio` int(11) DEFAULT NULL,
  `cualitativa` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nota_student_fk` (`idStudent`),
  KEY `nota_criterio_fk` (`idCriterio`),
  CONSTRAINT `nota_criterio_fk` FOREIGN KEY (`idCriterio`) REFERENCES `criterio` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nota_student_fk` FOREIGN KEY (`idStudent`) REFERENCES `student` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota`
--

LOCK TABLES `nota` WRITE;
/*!40000 ALTER TABLE `nota` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seccion`
--

DROP TABLE IF EXISTS `seccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seccion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `idIndicador` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seccion_indicador_fk` (`idIndicador`),
  CONSTRAINT `seccion_indicador_fk` FOREIGN KEY (`idIndicador`) REFERENCES `indicadorl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seccion`
--

LOCK TABLES `seccion` WRITE;
/*!40000 ALTER TABLE `seccion` DISABLE KEYS */;
INSERT INTO `seccion` VALUES (1,'1ro A',NULL),(2,'1ro B',NULL),(3,'1ro C',NULL),(4,'2do A',NULL),(5,'2do B',NULL),(6,'2do C',NULL),(7,'3ro A',NULL),(8,'3ro B',NULL),(9,'3ro C',NULL),(10,'4to A',NULL),(11,'4to B',NULL),(12,'4to C',NULL),(13,'5to A',NULL),(14,'5to B',NULL),(15,'5to C',NULL),(16,'6to A',NULL),(17,'6to B',NULL),(18,'6to C',NULL);
/*!40000 ALTER TABLE `seccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `idSeccion` int(11) DEFAULT NULL,
  `idCorte` int(11) DEFAULT NULL,
  `fin` int(11) DEFAULT NULL,
  `NumerodeLista` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idSeccionFk` (`idSeccion`),
  KEY `student_corte_fk` (`idCorte`),
  CONSTRAINT `student_corte_fk` FOREIGN KEY (`idCorte`) REFERENCES `corte` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `student_seccion_fk` FOREIGN KEY (`idSeccion`) REFERENCES `seccion` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=684 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES (1,'Altamirano Rodríguez Grace Sofía',1,1,0,1,1),(2,'Altamirano Tórrez Isabella Alejandra',1,1,0,2,1),(3,'Arauz Ortez Camilo Alberto',1,1,0,3,1),(4,'Arróliga Lazo Esther Abigail',1,1,0,4,1),(5,'Barahona Herrera Ellie Camila',1,1,0,5,1),(6,'Benavides Flores Juan José',1,1,0,6,1),(7,'Blandón Balmaceda Lenin Alejandro',1,1,0,7,1),(8,'Borge Barreto Amari Charlotte',1,1,0,8,1),(9,'Castellón Ruiz Alice Valentina',1,1,0,9,1),(10,'Castillo Arauz Mario Francisco',1,1,0,10,1),(11,'Castillo Blandón Jaslin Alondra',1,1,0,11,1),(12,'Castillo Castillo Anthony Gael',1,1,0,12,1),(13,'Centeno Leiva Zoe Yalimar',1,1,0,13,1),(14,'Chávez Andino Yassiry Sofía',1,1,0,14,1),(15,'Córdoba Rivera José David',1,1,0,15,1),(16,'Espinoza Benavidez Rommel Ibrahim',1,1,0,16,1),(17,'Flores Lagos Liam Caleb',1,1,0,17,1),(18,'Fonseca Olivera Lía Antonella',1,1,0,18,1),(19,'García Toruño Nahomy Gissell',1,1,1,19,1),(20,'Gutiérrez Cáceres Marcela',1,1,0,20,1),(21,'Gutiérrez Cáceres Paola',1,1,0,21,1),(22,'Lanuza Mendoza Hansel Marcelo',1,1,0,22,1),(23,'López Sánchez Adriana de Fátima',1,1,0,23,1),(24,'Molina Vargas Noah Sebastian',1,1,0,24,1),(25,'Montoya Hernández Jared Tadeo',1,1,0,25,1),(26,'Morales Gómez Marcelo Matias',1,1,0,26,1),(27,'Moreno Sevilla Lucía Mercedes',1,1,0,27,1),(28,'Ríos Mairena Magdiel Zaid',1,1,0,28,1),(29,'Rivera Martínez Zoe Camila',1,1,0,29,1),(30,'Rivera Rodríguez Alvin Alejandro',1,1,0,30,1),(31,'Rodríguez Arcia Andrea Karina',1,1,0,31,1),(32,'Salgado Espinoza Eythan Aarón',1,1,0,32,1),(33,'Salgado Reyes Edwin Elias',1,1,0,33,1),(34,'Urbina Calderón Aislinn Marie',1,1,0,34,1),(35,'Valdivia Lewin Cataleya Teresa',1,1,0,35,1),(36,'Valle Rivera Valerys Fiorella',1,1,0,36,1),(37,'Valle Valdivia Maviet Samara',1,1,0,37,1),(38,'Vega López Arturo Alonso',1,1,0,38,1),(39,'Casco Máxima Nathacha',2,1,0,1,1),(40,'Díaz Ruiz Nadiehsda Nataly',2,1,0,2,1),(41,'Flores Novoa Noelle Karolina',2,1,0,3,1),(42,'Flores Talavera Mía Montserrat',2,1,0,4,1),(43,'García Ortega Liam Jareth',2,1,0,5,1),(44,'González Huete Nicole Valentina',2,1,0,6,1),(45,'Gutiérrez Galeano José Leonel',2,1,0,7,1),(46,'Gutiérrez Guillén Eydan Abdiel',2,1,0,8,1),(47,'Gutiérrez Lanuza Marianny Francelly',2,1,0,9,1),(48,'Hernández  Olivas Samuel Alejandro',2,1,0,10,1),(49,'Hernández Vindell Mathias Sebastian',2,1,0,11,1),(50,'Lanuza Gutiérrez Danny Santiago',2,1,0,12,1),(51,'Lanuza Valdivia Lucas Andrés',2,1,0,13,1),(52,'Lazo Lanuza Alexia Yamileth',2,1,0,14,1),(53,'Leiva González Amari Elizabeth',2,1,0,15,1),(54,'López Molina Amelia Larissa',2,1,0,16,1),(55,'López Polanco Ronald Ariel',2,1,0,17,1),(56,'Luna Lanuza Denisse Sofía',2,1,1,18,1),(57,'Maradiaga Ortuño Noha Jossary',2,1,0,19,1),(58,'Mendoza Molina Yeimy Lucía',2,1,0,20,1),(59,'Meza Morales Nacely Guadalupe',2,1,0,21,1),(60,'Montoya Salinas José Elias',2,1,0,22,1),(61,'Morán Escobar Miguel Ángel',2,1,0,23,1),(62,'Moreno Montenegro Gerald de Jesús',2,1,0,24,1),(63,'Parrilla Ramírez Anielka Paola',2,1,0,25,1),(64,'Peralta Castilblanco María Guadalupe',2,1,0,26,1),(65,'Peralta Romero Adriana Michelle',2,1,0,27,1),(66,'Rizo Bellorín Ramón Isaac',2,1,0,28,1),(67,'Romero Valle Osmany José',2,1,0,29,1),(68,'Rugama López Antony Caleb',2,1,0,30,1),(69,'Ruiz Ordóñez Liam David',2,1,0,31,1),(70,'Salgado Fletes Leandro José',2,1,0,32,1),(71,'Talavera Castro Jerald Joeth',2,1,0,33,1),(72,'Talavera López Ariana Isabella',2,1,0,34,1),(73,'Téllez Hernández Andrea Solange',2,1,0,35,1),(74,'Valenzuela Salinas Jashuary Valentina',2,1,0,36,1),(75,'Zeledón Zeledón Ariadne Jessireth',2,1,0,37,1),(76,'Alfaro Blandón Ian Mateo',3,1,0,1,1),(77,'Benavides Mendoza Ailany Valeria',3,1,0,2,1),(78,'Calderón Oporta Karelis  Rosely',3,1,0,3,1),(79,'Camas Meneses Valery Lucía',3,1,0,4,1),(80,'Casco López Mary Luz',3,1,0,5,1),(81,'Castellanos Rayo Dylan Enoc',3,1,0,6,1),(82,'Gutiérrez Ibarra Isabella Sofía',3,1,0,7,1),(83,'Hidalgo Toruño Sarah Daniela',3,1,0,8,1),(84,'Laguna Rodas Angelie Camila',3,1,0,9,1),(85,'Lanuza Rivera Máximo',3,1,0,10,1),(86,'López Castellón Anthony Mateo',3,1,0,11,1),(87,'López Pérez Ariana Ruby',3,1,0,12,1),(88,'López Rayo Jocsan Ernesto',3,1,0,13,1),(89,'López Rivera Edward Samuel',3,1,0,14,1),(90,'Lorente Nicaragua Lian Matheo',3,1,0,15,1),(91,'Maradiaga Jarquín Marcelo Noe',3,1,0,16,1),(92,'Martínez Cárcamo Miguel Matias',3,1,0,17,1),(93,'Méndez Meza Mía Valentina',3,1,0,18,1),(94,'Morales Illescas Isabella de María',3,1,1,19,1),(95,'Moreno Acuña Hanna Sophía',3,1,0,20,1),(96,'Navarro Ortez Rodrigo Emiliano',3,1,0,21,1),(97,'Osegueda Gómez Caleb Josafat',3,1,0,22,1),(98,'Oviedo Rivera Brigitte Monserrat',3,1,0,23,1),(99,'Pérez Meza Isamar Isabella',3,1,0,24,1),(100,'Ponce Pérez Yerania Isabella',3,1,0,25,1),(101,'Preza Alfaro Génesis Lucía',3,1,0,26,1),(102,'Rayo Cruz Aishbell Alessia',3,1,0,27,1),(103,'Rivas Tórrez Elías Alonso',3,1,0,28,1),(104,'Rizo Herrera Irany Joseani',3,1,0,29,1),(105,'Rodríguez Lanuza Jeremy Orlando',3,1,0,30,1),(106,'Ruiz Gámez Ainhoa Esperanza',3,1,0,31,1),(107,'Saldaña González Abdel Sebastian',3,1,0,32,1),(108,'Salguera Olivas Joaquín Alexander',3,1,0,33,1),(109,'Talavera López Nahiara Sofía',3,1,0,34,1),(110,'Talavera Obando Valentina Lisareth',3,1,0,35,1),(111,'Toruño Blandón Briana Francella',3,1,0,36,1),(112,'Vado Ordóñez Lucas Matheo',3,1,0,37,1),(113,'Zeas Lanuza Liam Orlando',3,1,0,38,1),(114,'Arauz Hernández Hanna Michelle',4,1,0,1,1),(115,'Blandón Obando Matheo Darell',4,1,0,2,1),(116,'Caldera Romero Luan Alejandro',4,1,0,3,1),(117,'Canales Guevara Mateo Miguel',4,1,0,4,1),(118,'Castillo Bravo Lucas Ariel',4,1,0,5,1),(119,'Castillo Méndez Alisson Guadalupe',4,1,0,6,1),(120,'Castillo Moreno Matthews Johan',4,1,0,7,1),(121,'Centeno Toruño María Lucía',4,1,0,8,1),(122,'Chavarría González Paula Camila',4,1,0,9,1),(123,'Cornavaca Espinoza Elsayra  Nadiela',4,1,0,10,1),(124,'Cruz Cárdenas Ángel Adrian',4,1,0,11,1),(125,'Cruz Rodríguez Dorian',4,1,0,12,1),(126,'Dávila Talavera Valery Sugey',4,1,0,13,1),(127,'Domingues Castro Soriana Guadalupe',4,1,0,14,1),(128,'Espinoza Benavidez Derrick Josué',4,1,0,15,1),(129,'Florian Jiménez Eithan Enmanuel',4,1,0,16,1),(130,'Gámez Salgado Diego Alejandro',4,1,1,17,1),(131,'González Blandón Amy Giuliana',4,1,0,18,1),(132,'Gutiérrez Canales Danny Enmanuel',4,1,0,19,1),(133,'Harvey Aguilera Ana Michelle',4,1,0,20,1),(134,'Lanuza Palacios Dasha Valentina',4,1,0,21,1),(135,'Lazo Machado Thiago Mateo',4,1,0,22,1),(136,'Leduc Mayorga Jeremy Gabriel',4,1,0,23,1),(137,'López Gutiérrez Adriana Lucía',4,1,0,24,1),(138,'López Guzmán Liam Steeven',4,1,0,25,1),(139,'López Romero Weslly Josué',4,1,0,26,1),(140,'Martínez González Robert Matías',4,1,0,27,1),(141,'Ordóñez González Angie Alexandra',4,1,0,28,1),(142,'Palacios Castro Diego Alejandro',4,1,0,29,1),(143,'Pérez Olivas Lexa Aileen',4,1,0,30,1),(144,'Rios Martínez Yuneidy Nicol',4,1,0,31,1),(145,'Rodríguez Zelaya Marvin Elias',4,1,0,32,1),(146,'Sobalvarro Centeno Fredman Josias',4,1,0,33,1),(147,'Soto Moreno Eimy Sophía',4,1,0,34,1),(148,'Tinoco Mora Nelson Emmanuel',4,1,0,35,1),(149,'Valdez Manzanares Génesis Edith',4,1,0,36,1),(150,'Acevedo Albir Sophía Janeth',5,1,0,1,1),(151,'Álvarez Moreno José Miguel',5,1,0,2,1),(152,'Arauz Hernández Danna Paola',5,1,0,3,1),(153,'Cárdenas Hernández Nathan Moisés',5,1,0,4,1),(154,'Castellón Andino Hilary Valeria',5,1,0,5,1),(155,'Espinoza Blandón Austin Mariano',5,1,0,6,1),(156,'Flores Moreno Ian Josué',5,1,0,7,1),(157,'Gadea Arauz Noreyda Elena',5,1,0,8,1),(158,'Galeano Martínez Camilo Gabriel',5,1,0,9,1),(159,'García López Andrew Enmanuel',5,1,0,10,1),(160,'García Turniell Santiago Tadeo',5,1,0,11,1),(161,'González Chavarría Anderson Guillermo',5,1,0,12,1),(162,'González Hoyes Jefren Caleb',5,1,0,13,1),(163,'Jiménez Córdoba Daniela Jimena',5,1,0,14,1),(164,'Kontorovsky Castillo Moisés',5,1,0,15,1),(165,'Laguna Ramos Luis Santiago',5,1,0,16,1),(166,'Laínez Lanzas Eithan Gadiel',5,1,1,17,1),(167,'Manzanares Padilla Gabriella Elizabeth',5,1,0,18,1),(168,'Martínez González Valery',5,1,0,19,1),(169,'Molina Escalante Andrea Belén',5,1,0,20,1),(170,'Obando Romero Angelee María',5,1,0,21,1),(171,'Palacios Chavarría Wilder Bladimir',5,1,0,22,1),(172,'Pérez Chavarría Harold Emmanuel',5,1,0,23,1),(173,'Pineda Escoto Oscar Aveth',5,1,0,24,1),(174,'Quintanilla Talavera María Victoria',5,1,0,25,1),(175,'Ramos Ráudez Julián David',5,1,0,26,1),(176,'Rayo Salas David Alejandro',5,1,0,27,1),(177,'Rizo Molina Cristhell Catalina',5,1,0,28,1),(178,'Rodríguez Lanuza Emily Anahí',5,1,0,29,1),(179,'Rodríguez Nájera Diego Javier',5,1,0,30,1),(180,'Saldivar Castillo Itzell Guadalupe',5,1,0,31,1),(181,'Toledo Zeledón William David',5,1,0,32,1),(182,'Toruño Blandón Kristie Sofía',5,1,0,33,1),(183,'Valdivia Abud Guadalupe Alejandra',5,1,0,34,1),(184,'Aguilar Lanuza Samuel Enrique',6,1,0,1,1),(185,'Altamirano Valle Joalis Ixchel',6,1,0,2,1),(186,'Arevalo Aguirre Liam Marcell',6,1,0,3,1),(187,'Arias Cuadra Cesia Abigail',6,1,0,4,1),(188,'Castillo Suárez Ian Ulises',6,1,0,5,1),(189,'Castillo Toruño Jahzara Nauzeth',6,1,0,6,1),(190,'Castillo Úbeda Lya Massiel',6,1,0,7,1),(191,'Castro Monzón Caroline Lucía',6,1,0,8,1),(192,'Centeno Montoya Alex Enmanuel',6,1,0,9,1),(193,'Centeno Rodríguez Mia Valentina',6,1,0,10,1),(194,'Cruz Cruz Jocsan Caleb',6,1,0,11,1),(195,'Duarte Morán María Fernanda',6,1,0,12,1),(196,'Flores Hudiel Hanny Yoelis',6,1,0,13,1),(197,'Fox González Derick Denvorn',6,1,0,14,1),(198,'Gallardo Valenzuela Gael Valentín',6,1,0,15,1),(199,'García Aguilar Oswin Alonso',6,1,0,16,1),(200,'Garmendia Ruiz Angelys Janaan',6,1,1,17,1),(201,'Jarquín Caballero Ian Matias',6,1,0,18,1),(202,'López Cárcamo Nahomy Sofía',6,1,0,19,1),(203,'López Melgara Anderson Eladio',6,1,0,20,1),(204,'Martínez Peralta Ángel Matheo',6,1,0,21,1),(205,'Molina López Sofía Isabel',6,1,0,22,1),(206,'Moreno Valle Victor Josué',6,1,0,23,1),(207,'Peralta Lanuza Ángel Gabriel',6,1,0,24,1),(208,'Pérez Moreno Cristhian Said',6,1,0,25,1),(209,'Pérez Thomas Leslie Evelia',6,1,0,26,1),(210,'Pineda Suárez Jeffrey Gabriel',6,1,0,27,1),(211,'Rocha Ruiz Ángel Danilo',6,1,0,28,1),(212,'Romero Espino Mariam Sofía',6,1,0,29,1),(213,'Romero Matute Emely Yasuri',6,1,0,30,1),(214,'Sevilla Vásquez Mateo Alexander',6,1,0,31,1),(215,'Soto López Matias Gabriel',6,1,0,32,1),(216,'Tórrez Pérez Gabriel',6,1,0,33,1),(217,'Valdivia Castillo María Gabriela',6,1,0,34,1),(218,'Alaniz Toruño German Sebastian',7,1,0,1,1),(219,'Aquino Zelaya Gabriela del Carmen',7,1,0,2,1),(220,'Arauz Toruño Norlan José',7,1,0,3,1),(221,'Arauz Vallejos Magda Sofía',7,1,0,4,1),(222,'Blandón Cárcamo Carlos Sebastan',7,1,0,5,1),(223,'Castillo Castillo Freylie Mariel',7,1,0,6,1),(224,'Castillo Rodríguez Elieth Alejandra',7,1,0,7,1),(225,'Dávila Espinoza Zoe Nazareth',7,1,0,8,1),(226,'Flores García Zareth Matias',7,1,0,9,1),(227,'Hernández Gutiérrez Mery Sophía',7,1,0,10,1),(228,'Herrera Peralta Nolvin Aarón',7,1,0,11,1),(229,'Illescas Úbeda María Isabel',7,1,0,12,1),(230,'Juárez Olivas Briana Milagro',7,1,0,13,1),(231,'Leiva Bermúdez Eskarled Jasmin',7,1,0,14,1),(232,'Leiva Gutiérrez Sophía Valentina',7,1,0,15,1),(233,'López Cerrato Elias Marcel',7,1,0,16,1),(234,'López Rocha Jairo Alberto',7,1,0,17,1),(235,'Mendoza Barreda Leandro Manuel',7,1,0,18,1),(236,'Mora Toruño David Eduardo',7,1,0,19,1),(237,'Morán Zeledón Keysi Xiomara',7,1,1,20,1),(238,'Moreno González Mía Valentina',7,1,0,21,1),(239,'Moreno Webster Hellfrank Alejandro',7,1,0,22,1),(240,'Moya Benavides Arian Bayardo',7,1,0,23,1),(241,'Palacios Cerrato Marielis Galilea',7,1,0,24,1),(242,'Pérez Lezama Tita Grace',7,1,0,25,1),(243,'Pérez Rodríguez Marcela Rachell',7,1,0,26,1),(244,'Reyes Córdoba Dunniel Jesús',7,1,0,27,1),(245,'Rodríguez Castillo Andrea Nohemi',7,1,0,28,1),(246,'Rugama Matute Marlon Nahum',7,1,0,29,1),(247,'Ruiz Jarquín Marisabel Lucía',7,1,0,30,1),(248,'Tórrez Avilez Eliam Mateo',7,1,0,31,1),(249,'Toruño Benavides Perla Karina',7,1,0,32,1),(250,'Toruño Cerrato Samara Raiza',7,1,0,33,1),(251,'Urey Castillo Christiane Valeria',7,1,0,34,1),(252,'Valenzuela Salgado Marian Nazaret',7,1,0,35,1),(253,'Valladares Fierro Maxwell Julian',7,1,0,36,1),(254,'Zapata Cerrato Laura Daniela',7,1,0,37,1),(255,'Zelaya Gutiérrez Ema Isabella',7,1,0,38,1),(256,'Zeledón Lanuza Jordan Jasareth',7,1,0,39,1),(257,'Acuña Gámez Dariana Elizabeth',8,1,0,1,1),(258,'Aguilera Salgado Yoseling Gabriela',8,1,0,2,1),(259,'Altamirano Amador Ayham David',8,1,0,3,1),(260,'Barreda Briones Ariel Francisco',8,1,0,4,1),(261,'Blandón Florian Celso José',8,1,0,5,1),(262,'Britton Canales Nicole Isabel',8,1,0,6,1),(263,'Canales Chavarría Adriana Valentina',8,1,0,7,1),(264,'Castellanos Jarquín Fernando',8,1,0,8,1),(265,'Escorcia Rayo Zoe Fiorella',8,1,0,9,1),(266,'Espinoza Ruiz Liam Mateo',8,1,0,10,1),(267,'Gámez Zeledón Victoria Eleonora',8,1,0,11,1),(268,'Gómez Lanuza Daniela Sofía',8,1,0,12,1),(269,'Hernández Blandón Mariangely Gabriela',8,1,0,13,1),(270,'Hernánez Pérez Joubam Joseth',8,1,0,14,1),(271,'Hidalgo Monzón Elmer Yair',8,1,0,15,1),(272,'Laguna Rodas Bianka Sofía',8,1,0,16,1),(273,'López Salgado Gustavo Rafael',8,1,0,17,1),(274,'López Sánchez Lauren Francella',8,1,0,18,1),(275,'Mata Cruz Mario Francisco',8,1,1,19,1),(276,'Moreno Pineda Enmanuel Alberto',8,1,0,20,1),(277,'Olivares Juárez Valentina Guisselle',8,1,0,21,1),(278,'Parrilla Machado Liam Isaac',8,1,0,22,1),(279,'Picado Ruiz Fernanda Nahomy',8,1,0,23,1),(280,'Pineda Ventura Alice Sophya',8,1,0,24,1),(281,'Ponce Sanabria Ian Jared',8,1,0,25,1),(282,'Rivas Rojas Luciano Roberto',8,1,0,26,1),(283,'Rivera Amaya Jaycob David',8,1,0,27,1),(284,'Rivera Meléndez Lia Alexandra',8,1,0,28,1),(285,'Rivera Zelaya María Alejandra',8,1,0,29,1),(286,'Rizo Cruz Diego Alonso',8,1,0,30,1),(287,'Rodríguez Romero Rodrigo José',8,1,0,31,1),(288,'Rodríguez Rugama Oscar Josué',8,1,0,32,1),(289,'Rugama Rizo Aarón Santiago',8,1,0,33,1),(290,'Sevilla Arauz Camila Aurora',8,1,0,34,1),(291,'Sobalvarro Martínez Roger Nicolás',8,1,0,35,1),(292,'Tijerino Arauz Erick Santiago',8,1,0,36,1),(293,'Urbina Rodríguez Roxanna Guadalupe',8,1,0,37,1),(294,'Vallejos Aguirre Victoria Valentina',8,1,0,38,1),(295,'Zúniga Tórrez Erick Isaias',8,1,0,39,1),(296,'Altamirano Amador Lucas David',9,1,0,1,1),(297,'Arauz Cruz Leandro Josué',9,1,0,2,1),(298,'Barahona Velásquez Eliam Josué',9,1,0,3,1),(299,'Barquero Pérez Claudia Isabel',9,1,0,4,1),(300,'Benavides Pérez Andrea Lucía',9,1,0,5,1),(301,'Blandón Téllez Génesis Abigail',9,1,0,6,1),(302,'Briones Carrero Briana Paola',9,1,0,7,1),(303,'Castellón Lanuza Brianys Alexa',9,1,0,8,1),(304,'Castillo Arróliga Jhassling Ariam',9,1,0,9,1),(305,'Castillo Irias Azuhey Lucía',9,1,0,10,1),(306,'Castillo Salinas Flora Lucía',9,1,0,11,1),(307,'Cruz Ruiz Maryham Isabella',9,1,0,12,1),(308,'Díaz Castillo Emily Francella',9,1,0,13,1),(309,'Espinoza Toruño José Emanuel',9,1,0,14,1),(310,'Flores Ramírez Marcela Sofía',9,1,0,15,1),(311,'Fuentes Rivera Walmor Joao',9,1,0,16,1),(312,'García Ramírez Izzy Amelia',9,1,0,17,1),(313,'Gómez Moreno Wilbren Matthews',9,1,0,18,1),(314,'Gutiérrez Lira April Samara',9,1,0,19,1),(315,'Legall Borge Jetmary Valeria',9,1,1,20,1),(316,'López Peralta Mariana Isabella',9,1,0,21,1),(317,'López Polanco Tadeo Ariel',9,1,0,22,1),(318,'Martínez Flores Adriel Said',9,1,0,23,1),(319,'Molina Romero Mía Fernanda',9,1,0,24,1),(320,'Molina Vílchez Nohelia Marie',9,1,0,25,1),(321,'Novoa Hidalgo Diana Sofía',9,1,0,26,1),(322,'Paladino Velásquez Leandro Marcelo',9,1,0,27,1),(323,'Pineda Canales Angie Lucía',9,1,0,28,1),(324,'Rayo Espinoza Cristopher Humberto',9,1,0,29,1),(325,'Rayo Soza Stephanie Yaiza',9,1,0,30,1),(326,'Rizo Andino Jean Carlos',9,1,0,31,1),(327,'Roa Palacios Mateo Julian',9,1,0,32,1),(328,'Ruiz Ramírez Virginia Lizeth',9,1,0,33,1),(329,'Talavera García Mariel Alexandra',9,1,0,34,1),(330,'Uriarte Martínez Jeffrey Jacanel',9,1,0,35,1),(331,'Valle Benavidez Briana Valeria',9,1,0,36,1),(332,'Zelaya Gaitán Hilary Thais',9,1,0,37,1),(333,'Zelaya González Lauren Aínes',9,1,0,38,1),(334,'Zeledón Garay Lía Samaria',9,1,0,39,1),(335,'Altamirano Mejía Milagro Guadalupe',10,1,0,1,1),(336,'Andino Castillo Wilfredo Daniel',10,1,0,2,1),(337,'Armas Luna Sebastian Andrés',10,1,0,3,1),(338,'Benavides Gutiérrez Alexandra Gissell',10,1,0,4,1),(339,'Blandón Escorcia María José',10,1,0,5,1),(340,'Castro Blandón Jeremy Ibrahim',10,1,0,6,1),(341,'Cerrato Blandón Jasson Francisco',10,1,0,7,1),(342,'Cruz Ponce Elias Oniel',10,1,0,8,1),(343,'Gadea Gutiérrez Alejandra Michelle',10,1,0,9,1),(344,'Garay Rivera Ángel Gabriel',10,1,0,10,1),(345,'García Escoto Liam Johan',10,1,0,11,1),(346,'García Rivera Lian Alonso',10,1,0,12,1),(347,'González Talavera Alessandra Sophía',10,1,0,13,1),(348,'Gutiérrez Salcedo Carlos Said',10,1,0,14,1),(349,'Hernández Alvarado Matthew Joseph',10,1,0,15,1),(350,'Illescas Úbeda Ana Sofía',10,1,0,16,1),(351,'Laguna Pichardo Alvaro Dominick',10,1,1,17,1),(352,'Lanuza Vílchez Danny Matheo',10,1,0,18,1),(353,'López López Zoe Lucía',10,1,0,19,1),(354,'Machado Flores Jean Carlos',10,1,0,20,1),(355,'Molina Aguilera Fabian Emanuel',10,1,0,21,1),(356,'Montalván Gómez Keely Yaiza',10,1,0,22,1),(357,'Montenegro Zeledón Natasha Isabella',10,1,0,23,1),(358,'Montoya Pineda Estefanía Rocío',10,1,0,24,1),(359,'Moreno Sevilla Edward Francisco',10,1,0,25,1),(360,'Pérez Pérez Hanna Joly',10,1,0,26,1),(361,'Pineda Bermúdez Dominick Matteo',10,1,0,27,1),(362,'Quintero Rodríguez José Mariano',10,1,0,28,1),(363,'Rodríguez Castillo Ian Mateo',10,1,0,29,1),(364,'Saavedra Rodríguez Alvaro Gael',10,1,0,30,1),(365,'Smith Gámez Brianda Arisli',10,1,0,31,1),(366,'Talavera Vásquez Nathaly Joanna',10,1,0,32,1),(367,'Valle Rojas Jimmy Noé',10,1,0,33,1),(368,'Vega Torres Mauricio José',10,1,0,34,1),(369,'Zeledón Pineda Zaira Fernanda',10,1,0,35,1),(370,'Acevedo Castillo Matias Alexander',11,1,0,1,1),(371,'Aguilera Meza Noah Samara',11,1,0,2,1),(372,'Alsawaleha Payán Bosyh Nuray',11,1,0,3,1),(373,'Arevalo Aguirre Eliam Alberto',11,1,0,4,1),(374,'Benavidez González Yared Emilio',11,1,0,5,1),(375,'Betanco Castellón Liss Amanda',11,1,0,6,1),(376,'Blandón Moreno Joao Matheo',11,1,0,7,1),(377,'Blandón Rocha Mía Nicole',11,1,0,8,1),(378,'Canales Díaz Alice del Carmen',11,1,0,9,1),(379,'Castillo Dávila Kathia Nicolle',11,1,0,10,1),(380,'Castillo González Madeline Lissandra',11,1,0,11,1),(381,'Cerrato López Tatiana Sarahí',11,1,0,12,1),(382,'Chévez Pérez Ayling Isabella',11,1,0,13,1),(383,'Cruz López Lian Matteo',11,1,0,14,1),(384,'Dávila Ortiz Diego José',11,1,0,15,1),(385,'Espinoza Castro Briana Belén',11,1,0,16,1),(386,'Guillén Vásquez Matías André',11,1,0,17,1),(387,'Gutiérrez Sirias María Cecilia',11,1,1,18,1),(388,'Lanuza Mendoza Celeste Thairis',11,1,0,19,1),(389,'López Gómez Edward Joshua',11,1,0,20,1),(390,'López González Joseph Leonardo',11,1,0,21,1),(391,'Meza Arróliga Wendy Nallely',11,1,0,22,1),(392,'Miranda  Gutiérrez Ailish Ariana',11,1,0,23,1),(393,'Molina Castellón Leandro Moisés',11,1,0,24,1),(394,'Molina Velazques Zoe Celine',11,1,0,25,1),(395,'Narvaez Arana Mateo Leonardo',11,1,0,26,1),(396,'Ordónez Rizo Jadden Christtoph',11,1,0,27,1),(397,'Pérez Benavides Hamlet Rafael',11,1,0,28,1),(398,'Ramírez Matute Carlos Jafeht',11,1,0,29,1),(399,'Rivera Ramírez Giancarlo',11,1,0,30,1),(400,'Rodríguez Benavides Nathaly Giselle',11,1,0,31,1),(401,'Soto Torres Liam Gael',11,1,0,32,1),(402,'Tinoco Mora Ximena Fernanda',11,1,0,33,1),(403,'Urroz Parrilla Bryan Alessandro',11,1,0,34,1),(404,'Wingchang Raudez Laura Francella',11,1,0,35,1),(405,'Acuña Peralta Osmara Kaory',12,1,0,1,1),(406,'Arauz Castillo Kathi Jadelin',12,1,0,2,1),(407,'Castillo Barreda Juan Octavio',12,1,0,3,1),(408,'Castillo Gutiérrez Daniel Alexander',12,1,0,4,1),(409,'Centeno Montoya Ainhoa Yarisbel',12,1,0,5,1),(410,'Cruz Cruz Dylan Jaziel',12,1,0,6,1),(411,'Escorcia Lagos Cristhian Darell',12,1,0,7,1),(412,'Fajardo Robles Yaiza Cristela',12,1,0,8,1),(413,'Flores Valdivia Marissa Antonella',12,1,0,9,1),(414,'García Hernández Deyra Nahomy',12,1,0,10,1),(415,'García López Zoe Ayelen',12,1,0,11,1),(416,'Hernández Andino Odalys Marlene',12,1,0,12,1),(417,'Laguna Mairena Emelyng Tatianna',12,1,0,13,1),(418,'Lanuza Rayo Astrid Camila',12,1,0,14,1),(419,'Leiva González Edward Fernando',12,1,0,15,1),(420,'López Mejía Valery Dorieth',12,1,0,16,1),(421,'López Miranda Angie Isabel',12,1,0,17,1),(422,'Molina Alvarado Kenia Thais',12,1,1,18,1),(423,'Montenegro Montoya Bismary Guadalupe',12,1,0,19,1),(424,'Ordoñez Moreno Carlos Steven',12,1,0,20,1),(425,'Palacios Castro Steyci Alejandra',12,1,0,21,1),(426,'Pao Hernández Ashley Nicole',12,1,0,22,1),(427,'Pérez Rivera Luis Eduardo',12,1,0,23,1),(428,'Pérez Sobalvarro María Fernanda',12,1,0,24,1),(429,'Rodríguez Morales Ana Mercedes',12,1,0,25,1),(430,'Rodríguez Zamora Elias',12,1,0,26,1),(431,'Rodríguez Zeledón Allison Noeimy',12,1,0,27,1),(432,'Romero Espino Maryangel Alondra',12,1,0,28,1),(433,'Rugama Rayo Mirian Yarelis',12,1,0,29,1),(434,'Siles Siles Alejandra Lisseth',12,1,0,30,1),(435,'Solares Vásquez Iam Marcelo',12,1,0,31,1),(436,'Soto Moreno Xadriel Mateo',12,1,0,32,1),(437,'Vado Ordóñez Amy Sofía',12,1,0,33,1),(438,'Valle Escoto Virgelid Cristal',12,1,0,34,1),(439,'Velásquez Ráudez Anthony Sebastian',12,1,0,35,1),(440,'Acevedo Castillo Abigail Alessandra',13,1,0,1,1),(441,'Amador Úbeda Andrea Belén',13,1,0,2,1),(442,'Arauz Zeledón Matheo Enmanuel',13,1,0,3,1),(443,'Bendaña Sobalvarro Grace Alessandra',13,1,0,4,1),(444,'Blandón Martínez José Miguel',13,1,0,5,1),(445,'Blandón Rayo Valery Isabella',13,1,0,6,1),(446,'Bonilla Alvarado Andrea Sofía',13,1,0,7,1),(447,'Canales Monzón Angeleth Guadalupe',13,1,0,8,1),(448,'Castillo Jiménez Sebastian Andrés',13,1,0,9,1),(449,'Castillo Martínez Allyson Nicole',13,1,0,10,1),(450,'Castillo Talavera Donald Albeiro',13,1,0,11,1),(451,'Chinchilla Benavidez Carlos Enrique',13,1,0,12,1),(452,'Espinoza Cornavaca Roseli Nazareth',13,1,0,13,1),(453,'Galeano Tinoco Brandon Huzield',13,1,0,14,1),(454,'Gámez Meza Ángel Fabricio',13,1,0,15,1),(455,'Gómez Cárdenas Dylan Josué',13,1,0,16,1),(456,'González Alvarado Jeremias José',13,1,0,17,1),(457,'Guerra López Antonella',13,1,0,18,1),(458,'Guerrero Betanco Marianne Sophía',13,1,0,19,1),(459,'Gutiérrez Mairena Ariel Alejandro',13,1,0,20,1),(460,'Hernández Rugama Kirel Alejandra',13,1,1,21,1),(461,'Hidalgo Toruño Camila Abisag',13,1,0,22,1),(462,'Lagos González Alondra Sofía',13,1,0,23,1),(463,'Lovo Chavarría Josué Gabriel',13,1,0,24,1),(464,'Martínez Osorio Max Emilio',13,1,0,25,1),(465,'Martínez Rayo Josmary Guadalupe',13,1,0,26,1),(466,'Meléndez Blandón Mileidy Lismari',13,1,0,27,1),(467,'Molina Vargas Vida Isabella',13,1,0,28,1),(468,'Montano Pérez Julhian David',13,1,0,29,1),(469,'Montoya López Luis Mateo',13,1,0,30,1),(470,'Montoya Pineda Elias Emmanuel',13,1,0,31,1),(471,'Morales Illescas Mariana Lucía',13,1,0,32,1),(472,'Padilla Brenes Génesis Geovanela',13,1,0,33,1),(473,'Portobanco Aguilera Edelmary Yissell',13,1,0,34,1),(474,'Rivera González Andrew Camilo',13,1,0,35,1),(475,'Rugama López Lexy Lorena',13,1,0,36,1),(476,'Rugama Romero Amanda Sophía',13,1,0,37,1),(477,'Ruiz Jarquín Illiam Mariell',13,1,0,38,1),(478,'Tórrez Zeledón Alberto Javier',13,1,0,39,1),(479,'Valdivia Cruz Isabella Sofía',13,1,0,40,1),(480,'Vargas Blandón Melany Caricsa',13,1,0,41,1),(481,'Velásquez Raudez Johan Diroy',13,1,0,42,1),(482,'Acevedo Zelaya Javier Andrés',14,1,0,1,1),(483,'Aquino Zelaya Víctor José',14,1,0,2,1),(484,'Blandón Valdez Gustavo Enrique',14,1,0,3,1),(485,'Calero Flores Ruby Isabella',14,1,0,4,1),(486,'Cardoza Mairena Yaried Antonio',14,1,0,5,1),(487,'Castillo Montoya Angelly Rashell',14,1,0,6,1),(488,'Castillo Rivera Sofía Nicolle',14,1,0,7,1),(489,'Espinoza Montoya José Gabriel',14,1,0,8,1),(490,'Gallardo Valenzuela Anthony Gabriel',14,1,0,9,1),(491,'Garmendia Godoy Silvia Marcela',14,1,0,10,1),(492,'Gómez Aguilar Andrea Marcela',14,1,0,11,1),(493,'Gómez Flores Mateo Sebastian',14,1,0,12,1),(494,'Gutiérrez Gutiérrez Jeyson Gabriel',14,1,0,13,1),(495,'Gutiérrez Vásquez Joelis Nazareth',14,1,0,14,1),(496,'Jiménez Téllez Elias José',14,1,0,15,1),(497,'Lau Casco Luz Argentina',14,1,0,16,1),(498,'López Zelaya Mateo Ernesto',14,1,0,17,1),(499,'Maldonado Huete Isaac Darío',14,1,0,18,1),(500,'Matute Zapata Christopher Iván',14,1,0,19,1),(501,'Mendoza Zelaya Leandro Matias',14,1,1,20,1),(502,'Peralta Talavera Sergio Leandro',14,1,0,21,1),(503,'Peralta Vargas Dariela José',14,1,0,22,1),(504,'Pérez Barreda Isabella Marieth',14,1,0,23,1),(505,'Quintero Pérez Jefferson Amaru',14,1,0,24,1),(506,'Rios Centeno Francisco José',14,1,0,25,1),(507,'Rivera Martínez Lucero Alejandra',14,1,0,26,1),(508,'Rodríguez Marín Suri Dariana',14,1,0,27,1),(509,'Rodríguez Urbina Arianna Celeste',14,1,0,28,1),(510,'Romero Peña Mathias Jesús',14,1,0,29,1),(511,'Rugama Jirón Jorge Antonio',14,1,0,30,1),(512,'Rugama Martínez Diego Nicolas',14,1,0,31,1),(513,'Ruiz Arauz Luciana Sofía',14,1,0,32,1),(514,'Ruiz Cruz Liham Marcela',14,1,0,33,1),(515,'Ruiz Hidalgo Luis Alberto',14,1,0,34,1),(516,'Ruiz Rivera Marians Rachell',14,1,0,35,1),(517,'Salguera Olivas Rolando Josué',14,1,0,36,1),(518,'Toruño Valles Asia Sophía',14,1,0,37,1),(519,'Valdez Arauz Renata Loana',14,1,0,38,1),(520,'Valenzuela Valdivia Valeria',14,1,0,39,1),(521,'Vega López Sofía Vanessa',14,1,0,40,1),(522,'Zapata Cerrato Fernando Antonio',14,1,0,41,1),(523,'Aguirre Ruiz Ana Valentina',15,1,0,1,1),(524,'Alsawaleha Payán Nael Naim',15,1,0,2,1),(525,'Arauz Blandón Hashly Nicole',15,1,0,3,1),(526,'Argüello Caballero Lidice Ximena',15,1,0,4,1),(527,'Barrientos Torrez Noel Ivan',15,1,0,5,1),(528,'Benavides Martínez Frida Isabella',15,1,0,6,1),(529,'Bermúdez Lazo Edward Nadir',15,1,0,7,1),(530,'Chavarría Arosteguí Valentina Montserrat',15,1,0,8,1),(531,'Chavarría Ponce Santiago Andrés',15,1,0,9,1),(532,'Chávez Mairena Engels David',15,1,0,10,1),(533,'Córdoba Valle Alondra Sharlotte',15,1,0,11,1),(534,'Gómez Espino Liam Mateo',15,1,0,12,1),(535,'Gutiérrez Hernández Oscar Emmanuel',15,1,0,13,1),(536,'Gutiérrez Meza Ian Mateo',15,1,0,14,1),(537,'Laguna Escalante Matías Sebastian',15,1,0,15,1),(538,'Leiva García James Alejandro',15,1,0,16,1),(539,'López Tórrez Joshua Alexander',15,1,0,17,1),(540,'Manzanares Rodríguez Mia Elizabeth',15,1,0,18,1),(541,'Matute Cruz Anthony Jafeb',15,1,0,19,1),(542,'Molina Mairena Luciana Belén',15,1,0,20,1),(543,'Molina Valdivia Jhoana Vanessa',15,1,1,21,1),(544,'Montenegro González Cristal Angelina',15,1,0,22,1),(545,'Montenegro Rivera April Carolain',15,1,0,23,1),(546,'Moreno Alaníz  Mairim Nicole',15,1,0,24,1),(547,'Olivas Ruiz Danna Sofía',15,1,0,25,1),(548,'Peralta Tórrez Jazleny Fernanda',15,1,0,26,1),(549,'Pineda Bermúdez Ian Marcelo',15,1,0,27,1),(550,'Rivas Figueroa Zoe Marian',15,1,0,28,1),(551,'Rivera Velásquez Aslie Daniela',15,1,0,29,1),(552,'Rizo Valdivia Ariany Sofía',15,1,0,30,1),(553,'Ruiz Gutiérrez Angie Sofía',15,1,0,31,1),(554,'Ruiz Vallejos Sharon Arianna',15,1,0,32,1),(555,'Sáenz Lorio Miguel Mathias',15,1,0,33,1),(556,'Salazar Aguirre Mateo Dimaria',15,1,0,34,1),(557,'Siles Siles Mathias Alejandro',15,1,0,35,1),(558,'Tinoco Mora Ana María',15,1,0,36,1),(559,'Toruño Martínez Diosy Lenara',15,1,0,37,1),(560,'Valdivia Montenegro Alejandra Belén',15,1,0,38,1),(561,'Vargas Gutiérrez María Guadalupe',15,1,0,39,1),(562,'Virji Bustamante Irfan Zahid',15,1,0,40,1),(563,'Aguilar Benavides Farid Zahir',16,1,0,1,1),(564,'Alaníz Altamirano Brandy Said',16,1,0,2,1),(565,'Altamirano Valle Bliss Azury',16,1,0,3,1),(566,'Álvarez Gutiérrez Matheo Antonio',16,1,0,4,1),(567,'Barahona Velásquez Fernanda Isabella',16,1,0,5,1),(568,'Briones Lazo Génesis Nazareth',16,1,0,6,1),(569,'Castillo Zeledón Vivian Marcela',16,1,0,7,1),(570,'Cornavaca Arce Marvin Emilio',16,1,0,8,1),(571,'Cruz Aguilar Itzel Zuneydi',16,1,0,9,1),(572,'Flores Novoa Neymar José',16,1,0,10,1),(573,'Flores Valdivia Silvio José',16,1,0,11,1),(574,'Gadea Meneses  Anaid Francela',16,1,0,12,1),(575,'Guatemala Chavarría Kheany Vanessa',16,1,0,13,1),(576,'Gutiérrez Ibarra Norman Antonio',16,1,0,14,1),(577,'Hernández Rugama Alexa Nohemy',16,1,0,15,1),(578,'Laguna Ramos Roberto Gabriel',16,1,0,16,1),(579,'Leiva Benavides Eliazar Noé',16,1,0,17,1),(580,'López Rayo Joel Eduardo',16,1,0,18,1),(581,'Martínez Rodríguez Allisson Dayana',16,1,0,19,1),(582,'Martínez Talavera Alonzo Tadeo',16,1,1,20,1),(583,'Méndez Meza Hannah Alessandra',16,1,0,21,1),(584,'Mendoza Mejía Noel Alberto',16,1,0,22,1),(585,'Menjivar Blandón Amy Guadalupe',16,1,0,23,1),(586,'Moli Gutiérrez Gabriel Alexander',16,1,0,24,1),(587,'Navarro Ortez Diego Tomás',16,1,0,25,1),(588,'Obando Lorente Ainara Lucía',16,1,0,26,1),(589,'Orozco Peralta Adriana Angelia',16,1,0,27,1),(590,'Pineda Martínez Pedro Enmanuel',16,1,0,28,1),(591,'Quintero García Ariana Elizabeth',16,1,0,29,1),(592,'Rivas Tórrez José Gabriel',16,1,0,30,1),(593,'Rizo Gutiérrez Christy Gisselle',16,1,0,31,1),(594,'Rodríguez Torres Indira Nahomy',16,1,0,32,1),(595,'Ruiz Mendieta Diego Alejandro',16,1,0,33,1),(596,'Sánchez Somarriba Iker Gerardo',16,1,0,34,1),(597,'Sobalvarro Zamora Xochilth Antonella',16,1,0,35,1),(598,'Torres Hernández Mared Marcela',16,1,0,36,1),(599,'Toruño Talavera Cristian Alejandro',16,1,0,37,1),(600,'Vargas Blandón Brytany Jamileth',16,1,0,38,1),(601,'Zamora Altamirano Liam Mateo',16,1,0,39,1),(602,'Zeledón Rodríguez Ariana',16,1,0,40,1),(603,'Benavidez González André Said',17,1,0,1,1),(604,'Betanco Cerrato Krysta Isabella',17,1,0,2,1),(605,'Casco Hernández Mariam Guadalupe',17,1,0,3,1),(606,'Castro Rocha Francesco Ernesto',17,1,0,4,1),(607,'Centeno Espinoza Sharon Nicole',17,1,0,5,1),(608,'Cruz Blandón Oscar Mateo',17,1,0,6,1),(609,'Cruz Cruz Alexa Denisse',17,1,0,7,1),(610,'Flores Rayo Silgian',17,1,0,8,1),(611,'Gámez Zeledón Bruno Ernesto',17,1,0,9,1),(612,'García Palacios Franchesca Elena',17,1,0,10,1),(613,'García Sevilla Dereck Said',17,1,0,11,1),(614,'Gutiérrez Elsis Lisseth',17,1,0,12,1),(615,'Gutiérrez Blandón Mayce Betzabé',17,1,0,13,1),(616,'Gutiérrez Maradiaga Leandro Omar',17,1,0,14,1),(617,'Herrera Montenegro Isaias Tadeo',17,1,0,15,1),(618,'Hidalgo Pichardo  Mia Isabella',17,1,0,16,1),(619,'Larios Meza Evans Reynaldo',17,1,0,17,1),(620,'Lira Salgado Arjen Isaac',17,1,0,18,1),(621,'Luna Úbeda Lucas Mateo',17,1,0,19,1),(622,'Martínez Osorio José Joaquín',17,1,1,20,1),(623,'Molina Montenegro Cristiana Sophia',17,1,0,21,1),(624,'Montalván Gómez Kassy Francella',17,1,0,22,1),(625,'Montoya Rivas Alex Enmanuel',17,1,0,23,1),(626,'Morán Blandón José Antonio',17,1,0,24,1),(627,'Moreno Blandón Sofía Isabella',17,1,0,25,1),(628,'Moreno Sevilla Jossiel Eduardo',17,1,0,26,1),(629,'Obando Morán Rosita María',17,1,0,27,1),(630,'Rivera Zelaya Junaysi Alessandra',17,1,0,28,1),(631,'Rocha Rayo Ángel Leonardo',17,1,0,29,1),(632,'Rodríguez Alfaro Oscar Francisco',17,1,0,30,1),(633,'Rodríguez Flores Osmary Lisbeth',17,1,0,31,1),(634,'Roñac Lagos Sophía Isabella',17,1,0,32,1),(635,'Salgado Arce Dylan Rodrigo',17,1,0,33,1),(636,'Salgado Gámez Mathias Isaí',17,1,0,34,1),(637,'Tórrez Castillo Leandro Antonio',17,1,0,35,1),(638,'Toruño Benavides Cristhel Massiel',17,1,0,36,1),(639,'Ubau Rivera Ivania Estela',17,1,0,37,1),(640,'Vallejos Gutiérrez Francis Alexandra',17,1,0,38,1),(641,'Vallejos Pineda Manuel Alejandro',17,1,0,39,1),(642,'Zelaya Valenzuela Dylan Adael',17,1,0,40,1),(643,'Blandón Cerrato Michell Danisa',18,1,0,1,1),(644,'Castillo Badilla Isabella',18,1,0,2,1),(645,'Centeno Toruño  Roger Alejandro',18,1,0,3,1),(646,'Cruz Gutiérrez Nahiara Ahtziri',18,1,0,4,1),(647,'Duarte Castro Anthony Josué',18,1,0,5,1),(648,'Escorcia Montecinos José Raúl',18,1,0,6,1),(649,'Escorcia Rodríguez Anghelina Nicolle',18,1,0,7,1),(650,'Espinoza Ruiz Thais Nicole',18,1,0,8,1),(651,'García Altamirano Alicia Guadalupe',18,1,0,9,1),(652,'García Valle Roberth Joab',18,1,0,10,1),(653,'González Almendarez Mateo Emmanuel',18,1,0,11,1),(654,'González Espinoza Stephany Loana',18,1,0,12,1),(655,'González Rodríguez Verónica Renee',18,1,0,13,1),(656,'Hernández Calderón Josias José',18,1,0,14,1),(657,'Herrera Padilla Gabriela Cecilia',18,1,0,15,1),(658,'Herrera Tinoco Marelyn Dayanara',18,1,0,16,1),(659,'Hurtado Lira Ly Esmeralda',18,1,0,17,1),(660,'Lazo Machado Edwin Joao',18,1,0,18,1),(661,'Martínez Gutiérrez Emiliano Enrique',18,1,0,19,1),(662,'Mendoza Camas Laikel',18,1,1,20,1),(663,'Mendoza Tinoco Aarón Emmanuel',18,1,0,21,1),(664,'Montenegro Colindre Sixela Dayana',18,1,0,22,1),(665,'Montenegro Merlo Andrea Camila',18,1,0,23,1),(666,'Moreno Peralta Anthony Jahasiel',18,1,0,24,1),(667,'Moreno Torres Alondra Sofía',18,1,0,25,1),(668,'Olivas Larios Alisson Nicole',18,1,0,26,1),(669,'Palacios Molina Marcos Antonio',18,1,0,27,1),(670,'Palma Ruiz  Joan Matteo',18,1,0,28,1),(671,'Reyes Galeano Víctor Javier',18,1,0,29,1),(672,'Rodríguez Paiz Diego Mateo',18,1,0,30,1),(673,'Rodríguez Salgado Alexa Milagros',18,1,0,31,1),(674,'Rugama Castillo Ethan Sebastian',18,1,0,32,1),(675,'Rugama Cruz Deyvi Gabriel',18,1,0,33,1),(676,'Salgado Rodríguez Arliz Julianna',18,1,0,34,1),(677,'Sandino Lanuza Carlos Gabriel',18,1,0,35,1),(678,'Talavera Palacios María José',18,1,0,36,1),(679,'Tórrez Cárdenas Kevin Daniel',18,1,0,37,1),(680,'Urrutia Rodríguez María Celeste',18,1,0,38,1),(681,'Valdivia Castillo Sebastian',18,1,0,39,1),(682,'Vargas Rodríguez Elton José',18,1,0,40,1),(683,'Velásquez Picado Isabella Sofía',18,1,0,41,1);
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipoasistencia`
--

DROP TABLE IF EXISTS `tipoasistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipoasistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipoasistencia`
--

LOCK TABLES `tipoasistencia` WRITE;
/*!40000 ALTER TABLE `tipoasistencia` DISABLE KEYS */;
INSERT INTO `tipoasistencia` VALUES (1,'X'),(2,'J'),(3,'A');
/*!40000 ALTER TABLE `tipoasistencia` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-10 10:54:39
