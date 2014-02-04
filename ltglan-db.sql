-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.24-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for ltglan
CREATE DATABASE IF NOT EXISTS `ltglan` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_danish_ci */;
USE `ltglan`;


-- Dumping structure for table ltglan.admins
DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `guest_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- Dumping data for table ltglan.admins: ~1 rows (approximately)
DELETE FROM `admins`;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` (`guest_id`) VALUES
	(1);
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;


-- Dumping structure for table ltglan.beskeder
DROP TABLE IF EXISTS `beskeder`;
CREATE TABLE IF NOT EXISTS `beskeder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `modtager_id` int(10) DEFAULT NULL,
  `indhold` mediumtext CHARACTER SET utf8 COLLATE utf8_danish_ci,
  `laest` tinyint(4) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `modtager` (`modtager_id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.beskeder: 0 rows
DELETE FROM `beskeder`;
/*!40000 ALTER TABLE `beskeder` DISABLE KEYS */;
/*!40000 ALTER TABLE `beskeder` ENABLE KEYS */;


-- Dumping structure for table ltglan.billetnr
DROP TABLE IF EXISTS `billetnr`;
CREATE TABLE IF NOT EXISTS `billetnr` (
  `billetnr` int(6) unsigned zerofill NOT NULL,
  KEY `billetnr` (`billetnr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.billetnr: 2 rows
DELETE FROM `billetnr`;
/*!40000 ALTER TABLE `billetnr` DISABLE KEYS */;
INSERT INTO `billetnr` (`billetnr`) VALUES
	(000001),
	(123456);
/*!40000 ALTER TABLE `billetnr` ENABLE KEYS */;


-- Dumping structure for table ltglan.brackets
DROP TABLE IF EXISTS `brackets`;
CREATE TABLE IF NOT EXISTS `brackets` (
  `t_id` int(10) unsigned DEFAULT NULL,
  `c_t_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- Dumping data for table ltglan.brackets: ~0 rows (approximately)
DELETE FROM `brackets`;
/*!40000 ALTER TABLE `brackets` DISABLE KEYS */;
/*!40000 ALTER TABLE `brackets` ENABLE KEYS */;


-- Dumping structure for table ltglan.deltagere
DROP TABLE IF EXISTS `deltagere`;
CREATE TABLE IF NOT EXISTS `deltagere` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `guest_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `pos` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `guest_id` (`guest_id`),
  KEY `tournament_id` (`tournament_id`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- Dumping data for table ltglan.deltagere: 0 rows
DELETE FROM `deltagere`;
/*!40000 ALTER TABLE `deltagere` DISABLE KEYS */;
/*!40000 ALTER TABLE `deltagere` ENABLE KEYS */;


-- Dumping structure for table ltglan.guests
DROP TABLE IF EXISTS `guests`;
CREATE TABLE IF NOT EXISTS `guests` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Et bruger id til internt brug',
  `pass_hashed` mediumtext NOT NULL,
  `billetnr` int(6) unsigned zerofill NOT NULL COMMENT 'billetnummer',
  `navn` mediumtext NOT NULL COMMENT 'personens navn (Oskar V.)',
  `klasse` char(7) NOT NULL COMMENT 'personens klasse (2. MI / 2. b)',
  PRIMARY KEY (`id`),
  KEY `billetnr` (`billetnr`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.guests: 8 rows
DELETE FROM `guests`;
/*!40000 ALTER TABLE `guests` DISABLE KEYS */;
INSERT INTO `guests` (`id`, `pass_hashed`, `billetnr`, `navn`, `klasse`) VALUES
	(1, '1a1dc91c907325c69271ddf0c944bc72', 000001, 'Admin', '3. b'),
	(3, '1a1dc91c907325c69271ddf0c944bc72', 123456, 'Admin1', '1. a'),
	(5, '1f495fd87fb25f5aa99364c03a5fa3b5', 362687, 'Simon Weber', '1. b'),
	(6, '8277e0910d750195b448797616e091ad', 453453, 'D', '1. a'),
	(7, '21232f297a57a5a743894a0e4a801fc3', 123123, 'Hans Henrik', 'Anden'),
	(8, '21232f297a57a5a743894a0e4a801fc3', 666666, 'Hans Henrik', '3. c'),
	(10, '81dc9bdb52d04dc20036dbd8313ed055', 182751, 'Kanf', '1. a'),
	(11, 'c6f057b86584942e415435ffb1fa93d4', 567876, 'ååå', '1. a');
/*!40000 ALTER TABLE `guests` ENABLE KEYS */;


-- Dumping structure for table ltglan.invites
DROP TABLE IF EXISTS `invites`;
CREATE TABLE IF NOT EXISTS `invites` (
  `hash` text NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  KEY `tournament_id` (`tournament_id`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.invites: 0 rows
DELETE FROM `invites`;
/*!40000 ALTER TABLE `invites` DISABLE KEYS */;
/*!40000 ALTER TABLE `invites` ENABLE KEYS */;


-- Dumping structure for table ltglan.teams
DROP TABLE IF EXISTS `teams`;
CREATE TABLE IF NOT EXISTS `teams` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `navn` text COLLATE utf8_danish_ci NOT NULL,
  `leader_id` tinyint(3) unsigned NOT NULL,
  `teamstatus` text COLLATE utf8_danish_ci,
  `tournament_id` int(10) unsigned NOT NULL,
  `avatarpath` text COLLATE utf8_danish_ci,
  `bord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tournament_id` (`tournament_id`),
  KEY `leader_id` (`leader_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- Dumping data for table ltglan.teams: 0 rows
DELETE FROM `teams`;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;


-- Dumping structure for table ltglan.tournaments
DROP TABLE IF EXISTS `tournaments`;
CREATE TABLE IF NOT EXISTS `tournaments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `navn` text CHARACTER SET utf8 COLLATE utf8_danish_ci,
  `max_spillere` int(11) DEFAULT NULL,
  `rules` text CHARACTER SET utf8 COLLATE utf8_danish_ci,
  `bracketlink` text NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.tournaments: 14 rows
DELETE FROM `tournaments`;
/*!40000 ALTER TABLE `tournaments` DISABLE KEYS */;
INSERT INTO `tournaments` (`id`, `navn`, `max_spillere`, `rules`, `bracketlink`, `active`) VALUES
	(1, 'League of Legends', 5, 'Format: 5v5 Tournament Draft på SR<br />\r\nFinalen og semi-finale spilles som BO3<br />\r\nSyndra er banned i alle kampe.<br />\r\n\r\n/pause kan benyttes i alle tilfælde<br />\r\n af netværks- eller computerproblemer.', 'ltglanlol', 1),
	(11, 'Super Smash Brother Brawl', 2, '2v2 turnering<br/>\r\nItems: only smash balls<br/>\r\nAll characters available<br/>\r\nBO3<br />\r\n5 stock/life matches<br/>\r\n', '', 1),
	(2, 'Minecraft Build-Off', 2, 'Opgaven: at bygge et hus & en båd.<br />\r\nHuset bliver første challenge,<br />\r\nog båden bliver nummer to.<br />\r\nHver challenge har en deadline 90 minutter,<br />\r\nog bliver bedømt af 4 mennesker fra selve lanet.', '', 1),
	(3, 'Counter-Strike: Source', 5, 'Format: 5v5<br />\r\n13 runder spilles som hver faction (T/CT)<br />\r\n\r\nKnife-fight for at bestemme hvem der starter<br />\r\n på hvilken faction. Vinderen bestemmer.', '', 0),
	(4, 'Trackmania Nations', 1, 'Format: Time attack', '', 0),
	(5, 'Bloodline Champions', 3, 'Format: 3v3 Arena BO3<br/>Finalen spilles som BO5', '', 0),
	(6, 'Ultimate Marvel vs. Capcom 3', 1, 'Format: 1v1 BO3 Random Maps', '', 0),
	(7, 'Starcraft II', 1, 'Format: 1v1 BO3<br/>Første map er bestemt af admins,<br/>taberen bestemmer næste map', '', 0),
	(8, 'Warlock i WC3', 2, 'Format: 2v2 BO1, Finals: BO5<br />\r\nv. 093<br />\r\nmodes:<br />\r\n-league<br />\r\n-teams (preset)<br />\r\n-11<br />', '', 1),
	(9, 'Left 4 Dead 2', 4, 'Format: 4v4 Versus<br/>3 random map', '', 0),
	(10, 'Audiosurf', 2, 'Format: 2v2', '', 0),
	(12, 'CS:GO', 5, 'Format: 5v5 Competetive', '', 1),
	(13, 'Hearthstone', 1, NULL, '', 1),
	(14, 'Team Fortress 2', 5, NULL, '', 1);
/*!40000 ALTER TABLE `tournaments` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
