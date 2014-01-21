-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.12-log - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             8.2.0.4675
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for ltglan
CREATE DATABASE IF NOT EXISTS `ltglan` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_danish_ci */;
USE `ltglan`;


-- Dumping structure for table ltglan.beskeder
CREATE TABLE IF NOT EXISTS `beskeder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `modtager_id` int(10) DEFAULT NULL,
  `indhold` mediumtext CHARACTER SET utf8 COLLATE utf8_danish_ci,
  `laest` tinyint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modtager` (`modtager_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.beskeder: 0 rows
DELETE FROM `beskeder`;
/*!40000 ALTER TABLE `beskeder` DISABLE KEYS */;
/*!40000 ALTER TABLE `beskeder` ENABLE KEYS */;


-- Dumping structure for table ltglan.billetnr
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


-- Dumping structure for table ltglan.deltagere
CREATE TABLE IF NOT EXISTS `deltagere` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `pos` tinyint(3) unsigned NOT NULL,
  KEY `guest_id` (`guest_id`),
  KEY `tournament_id` (`tournament_id`),
  KEY `team_id` (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- Dumping data for table ltglan.deltagere: ~1 rows (approximately)
DELETE FROM `deltagere`;
/*!40000 ALTER TABLE `deltagere` DISABLE KEYS */;
INSERT INTO `deltagere` (`id`, `guest_id`, `tournament_id`, `team_id`, `pos`) VALUES
	(0, 1, 1, 4, 0);
/*!40000 ALTER TABLE `deltagere` ENABLE KEYS */;


-- Dumping structure for table ltglan.guests
CREATE TABLE IF NOT EXISTS `guests` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Et bruger id til internt brug',
  `pass_hashed` mediumtext NOT NULL,
  `billetnr` int(6) unsigned zerofill NOT NULL COMMENT 'billetnummer',
  `navn` mediumtext NOT NULL COMMENT 'personens navn (Oskar V.)',
  `klasse` char(7) NOT NULL COMMENT 'personens klasse (2. MI / 2. b)',
  PRIMARY KEY (`id`),
  KEY `billetnr` (`billetnr`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.guests: 1 rows
DELETE FROM `guests`;
/*!40000 ALTER TABLE `guests` DISABLE KEYS */;
INSERT INTO `guests` (`id`, `pass_hashed`, `billetnr`, `navn`, `klasse`) VALUES
	(1, '1a1dc91c907325c69271ddf0c944bc72', 000001, 'Admin', '3. b');
/*!40000 ALTER TABLE `guests` ENABLE KEYS */;


-- Dumping structure for table ltglan.invites
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- Dumping data for table ltglan.teams: ~1 rows (approximately)
DELETE FROM `teams`;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` (`id`, `navn`, `leader_id`, `teamstatus`, `tournament_id`, `avatarpath`, `bord`) VALUES
	(4, 'asd', 1, 'Pending', 1, '', 1);
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;


-- Dumping structure for table ltglan.tournaments
CREATE TABLE IF NOT EXISTS `tournaments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `navn` text CHARACTER SET utf8 COLLATE utf8_danish_ci,
  `max_spillere` int(11) DEFAULT NULL,
  `rules` text CHARACTER SET utf8 COLLATE utf8_danish_ci,
  `bracketlink` text NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.tournaments: 11 rows
DELETE FROM `tournaments`;
/*!40000 ALTER TABLE `tournaments` DISABLE KEYS */;
INSERT INTO `tournaments` (`id`, `navn`, `max_spillere`, `rules`, `bracketlink`, `active`) VALUES
	(1, 'League of Legends', 5, 'Format: 5v5 Tournament Draft på SR<br />\r\nFinalen og semi-finale spilles som BO3<br />\r\nSyndra er banned i alle kampe.<br />\r\n\r\n/pause kan benyttes i alle tilfælde<br />\r\n af netværks- eller computerproblemer.', '', 1),
	(11, 'Super Smash Brother Brawl', 2, '2v2 turnering<br/>\r\nItems: only smash balls<br/>\r\nAll characters available<br/>\r\nBO3<br />\r\n5 stock/life matches<br/>\r\n', '', 1),
	(2, 'Minecraft Build-Off', 2, 'Opgaven: at bygge et hus & en båd.<br />\r\nHuset bliver første challenge,<br />\r\nog båden bliver nummer to.<br />\r\nHver challenge har en deadline 90 minutter,<br />\r\nog bliver bedømt af 4 mennesker fra selve lanet.', '', 0),
	(3, 'Counter-Strike: Source', 5, 'Format: 5v5<br />\r\n13 runder spilles som hver faction (T/CT)<br />\r\n\r\nKnife-fight for at bestemme hvem der starter<br />\r\n på hvilken faction. Vinderen bestemmer.', '', 0),
	(4, 'Trackmania Nations', 1, 'Format: Time attack', '', 0),
	(5, 'Bloodline Champions', 3, 'Format: 3v3 Arena BO3<br/>Finalen spilles som BO5', '', 0),
	(6, 'Ultimate Marvel vs. Capcom 3', 1, 'Format: 1v1 BO3 Random Maps', '', 0),
	(7, 'Starcraft II', 1, 'Format: 1v1 BO3<br/>Første map er bestemt af admins,<br/>taberen bestemmer næste map', '', 0),
	(8, 'Warlock i WC3', 2, 'Format: 2v2 BO1, Finals: BO5<br />\r\nv. 093<br />\r\nmodes:<br />\r\n-league<br />\r\n-teams (preset)<br />\r\n-11<br />', 'http://flic.kr/p/daJcG3', 1),
	(9, 'Left 4 Dead 2', 4, 'Format: 4v4 Versus<br/>3 random map', '', 0),
	(10, 'Audiosurf', 2, 'Format: 2v2', '', 0);
/*!40000 ALTER TABLE `tournaments` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
