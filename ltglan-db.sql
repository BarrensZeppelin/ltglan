-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.24-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
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
  `laest` tinyint(4) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `modtager` (`modtager_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.beskeder: 14 rows
DELETE FROM `beskeder`;
/*!40000 ALTER TABLE `beskeder` DISABLE KEYS */;
INSERT INTO `beskeder` (`id`, `modtager_id`, `indhold`, `laest`) VALUES
	(3, 3, 'Du inviterede Admin til asds', 1),
	(11, 1, 'Du inviterede Admin1 til asd', 1),
	(18, 1, 'Du inviterede Simon Weber til Det Gode Hold', 1),
	(19, 5, 'Du er blevet inviteret til at spille for holdet Det Gode Hold i League of Legends-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=8f40ca5aa5a07bf5ada7df82320b3d90\'>her</a>', 0),
	(14, 1, 'Du inviterede Admin1 til asdasd', 1),
	(16, 1, 'Du inviterede Admin1 til De Sure mongoler', 1),
	(17, 3, 'Du er blevet inviteret til at spille for holdet De Sure mongoler i Super Smash Brother Brawl-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=a6934cb95bc62788adc562c2ae93da63\'>her</a>', 1),
	(20, 1, 'Du inviterede Account Med Rigtigt Langt Navn til Det Gode Hold', 1),
	(21, 9, 'Du er blevet inviteret til at spille for holdet Det Gode Hold i League of Legends-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=144eb72e990b28c94a18a3fb30d74180\'>her</a>', 1),
	(22, 1, 'Du inviterede Hans Henrik til Hestene', 1),
	(23, 8, 'Du er blevet inviteret til at spille for holdet Hestene i Minecraft Build-Off-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=7e9c6644839bc424ecdece7d4a4ac742\'>her</a>', 0),
	(24, 1, 'Du inviterede Admin1 til Det Gode Hold', 1),
	(25, 3, 'Du er blevet inviteret til at spille for holdet Det Gode Hold i League of Legends-turneringen. Hvis du ønsker at acceptere, så klik <a href=\'accept_invite.php?hash=f45e92045c39596d0b03ae11a2a6f274\'>her</a>', 1),
	(26, 3, 'Du inviterede Account Med Rigtigt Langt Navn til asdasd', 1);
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- Dumping data for table ltglan.deltagere: 7 rows
DELETE FROM `deltagere`;
/*!40000 ALTER TABLE `deltagere` DISABLE KEYS */;
INSERT INTO `deltagere` (`id`, `guest_id`, `tournament_id`, `team_id`, `pos`) VALUES
	(0, 1, 11, 3, 0),
	(0, 3, 11, 3, 1),
	(0, 6, 1, 4, 0),
	(0, 1, 1, 11, 0),
	(0, 9, 1, 11, 1),
	(0, 1, 2, 12, 0),
	(0, 3, 1, 11, 2);
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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.guests: 9 rows
DELETE FROM `guests`;
/*!40000 ALTER TABLE `guests` DISABLE KEYS */;
INSERT INTO `guests` (`id`, `pass_hashed`, `billetnr`, `navn`, `klasse`) VALUES
	(1, '1a1dc91c907325c69271ddf0c944bc72', 000001, 'Admin', '3. b'),
	(3, '1a1dc91c907325c69271ddf0c944bc72', 123456, 'Admin1', '1. a'),
	(5, '1f495fd87fb25f5aa99364c03a5fa3b5', 362687, 'Simon Weber', '1. b'),
	(6, '8277e0910d750195b448797616e091ad', 453453, 'D', '1. a'),
	(7, '21232f297a57a5a743894a0e4a801fc3', 123123, 'Hans Henrik', 'Anden'),
	(8, '21232f297a57a5a743894a0e4a801fc3', 666666, 'Hans Henrik', '3. c'),
	(9, '1a1dc91c907325c69271ddf0c944bc72', 000002, 'Account Med Rigtigt Langt Navn', '3. b'),
	(10, '81dc9bdb52d04dc20036dbd8313ed055', 182751, 'Kanf', '1. a'),
	(11, 'c6f057b86584942e415435ffb1fa93d4', 567876, 'ååå', '1. a');
/*!40000 ALTER TABLE `guests` ENABLE KEYS */;


-- Dumping structure for table ltglan.invites
CREATE TABLE IF NOT EXISTS `invites` (
  `hash` text NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  KEY `tournament_id` (`tournament_id`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.invites: 5 rows
DELETE FROM `invites`;
/*!40000 ALTER TABLE `invites` DISABLE KEYS */;
INSERT INTO `invites` (`hash`, `tournament_id`, `team_id`) VALUES
	('144eb72e990b28c94a18a3fb30d74180', 1, 11),
	('8f40ca5aa5a07bf5ada7df82320b3d90', 1, 11),
	('a6934cb95bc62788adc562c2ae93da63', 11, 3),
	('7e9c6644839bc424ecdece7d4a4ac742', 2, 12),
	('f45e92045c39596d0b03ae11a2a6f274', 1, 11);
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
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- Dumping data for table ltglan.teams: 4 rows
DELETE FROM `teams`;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` (`id`, `navn`, `leader_id`, `teamstatus`, `tournament_id`, `avatarpath`, `bord`) VALUES
	(3, 'De Sure mongoler', 1, 'Accepted', 11, '', 2),
	(4, 'dender', 6, 'Pending', 1, '', 4),
	(11, 'Det Gode Hold', 1, 'Pending', 1, './imgs/avatars/avatar-11.png', 2),
	(12, 'Hestene', 1, 'Pending', 2, './imgs/avatars/avatar-12.png', 940);
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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Dumping data for table ltglan.tournaments: 14 rows
DELETE FROM `tournaments`;
/*!40000 ALTER TABLE `tournaments` DISABLE KEYS */;
INSERT INTO `tournaments` (`id`, `navn`, `max_spillere`, `rules`, `bracketlink`, `active`) VALUES
	(1, 'League of Legends', 5, 'Format: 5v5 Tournament Draft på SR<br />\r\nFinalen og semi-finale spilles som BO3<br />\r\nSyndra er banned i alle kampe.<br />\r\n\r\n/pause kan benyttes i alle tilfælde<br />\r\n af netværks- eller computerproblemer.', 'linket', 1),
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
