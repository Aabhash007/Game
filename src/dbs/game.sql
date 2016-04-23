SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------


--
-- Tabellenstruktur für Tabelle `Artefact`
--

CREATE TABLE IF NOT EXISTS `Artefact` (
  `artefactID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `artefactClassID` int(11) unsigned NOT NULL DEFAULT '0',
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `initiated` int(4) NOT NULL DEFAULT '0',
  `pet` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`artefactID`),
  KEY `global` (`pet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Artefact_class`
--

CREATE TABLE IF NOT EXISTS `Artefact_class` (
  `artefactClassID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name_initiated` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `resref` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'artefact_test',
  `initiationImg` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'dummy.gif',
  `uninitiationImg` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'dummy.gif',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `description_initiated` text COLLATE utf8_unicode_ci NOT NULL,
  `initiationText` text COLLATE utf8_unicode_ci NOT NULL,
  `initiationID` int(11) NOT NULL DEFAULT '1',
  `getArtefactBySpy` int(1) unsigned NOT NULL DEFAULT '0',
  `noZeroFood` int(1) unsigned NOT NULL DEFAULT '0',
  `destroyOnMove` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`artefactClassID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Artefact_merge_general`
--

CREATE TABLE IF NOT EXISTS `Artefact_merge_general` (
  `keyClassID` int(11) unsigned NOT NULL DEFAULT '0',
  `lockClassID` int(11) unsigned NOT NULL DEFAULT '0',
  `resultClassID` int(11) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `keyClass_lockClass` (`keyClassID`,`lockClassID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Artefact_merge_special`
--

CREATE TABLE IF NOT EXISTS `Artefact_merge_special` (
  `keyID` int(11) unsigned NOT NULL DEFAULT '0',
  `lockID` int(11) unsigned NOT NULL DEFAULT '0',
  `resultID` int(11) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `key_lock` (`keyID`,`lockID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Artefact_rituals`
--

CREATE TABLE IF NOT EXISTS `Artefact_rituals` (
  `ritualID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `duration` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '3600',
  PRIMARY KEY (`ritualID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Awards`
--

CREATE TABLE IF NOT EXISTS `Awards` (
  `awardID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`awardID`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Cave`
--

CREATE TABLE IF NOT EXISTS `Cave` (
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `xCoord` int(11) unsigned NOT NULL DEFAULT '0',
  `yCoord` int(11) unsigned NOT NULL DEFAULT '0',
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `terrain` int(11) unsigned NOT NULL DEFAULT '0',
  `takeoverable` tinyint(1) NOT NULL DEFAULT '0',
  `takeover_level` int(11) unsigned NOT NULL DEFAULT '0',
  `starting_position` tinyint(1) NOT NULL DEFAULT '0',
  `secureCave` tinyint(1) NOT NULL DEFAULT '0',
  `noStatistic` tinyint(1) NOT NULL DEFAULT '0',
  `protection_end` char(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `toreDownTimeout` char(14) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `artefacts` int(11) unsigned NOT NULL DEFAULT '0',
  `monsterID` int(11) unsigned NOT NULL DEFAULT '0',
  `regionID` int(11) NOT NULL DEFAULT '0',
  `hero` int(11) NOT NULL DEFAULT '0',
  `lastAttackingTribeID` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`caveID`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `Coords` (`xCoord`,`yCoord`),
  KEY `playerID` (`playerID`),
  KEY `noStatistic` (`noStatistic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `CaveBookmarks`
--

CREATE TABLE IF NOT EXISTS `CaveBookmarks` (
  `bookmarkID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bookmarkID`),
  UNIQUE KEY `cavebookmark` (`playerID`,`caveID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Cave_Orginalname`
--

CREATE TABLE IF NOT EXISTS `Cave_Orginalname` (
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `xCoord` int(11) unsigned NOT NULL DEFAULT '0',
  `yCoord` int(11) unsigned NOT NULL DEFAULT '0',
  `name` char(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`caveID`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `Coords` (`xCoord`,`yCoord`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Cave_takeover`
--

CREATE TABLE IF NOT EXISTS `Cave_takeover` (
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `xCoord` int(11) unsigned NOT NULL DEFAULT '0',
  `yCoord` int(11) unsigned NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `lastAction` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`playerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ChatQueue`
--

CREATE TABLE IF NOT EXISTS `ChatQueue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `jabberName` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `success` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `blocked` (`blocked`),
  KEY `success` (`success`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ChatRoom`
--

CREATE TABLE IF NOT EXISTS `ChatRoom` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tribeID` int(11) unsigned NOT NULL,
  `tag` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `autojoin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `success` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`),
  KEY `tribeID` (`tribeID`),
  KEY `deleted` (`deleted`),
  KEY `success` (`success`),
  KEY `blocked` (`blocked`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ChatUser`
--

CREATE TABLE IF NOT EXISTS `ChatUser` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `roomID` int(11) unsigned NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `success` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UserRoom` (`roomID`,`name`),
  KEY `tribeID` (`roomID`),
  KEY `success` (`success`),
  KEY `blocked` (`blocked`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Contacts`
--

CREATE TABLE IF NOT EXISTS `Contacts` (
  `contactID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `contactplayerID` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`contactID`),
  UNIQUE KEY `contact` (`playerID`,`contactplayerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_artefact`
--

CREATE TABLE IF NOT EXISTS `Event_artefact` (
  `event_artefactID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `caveID` int(10) unsigned NOT NULL DEFAULT '0',
  `artefactID` int(10) unsigned NOT NULL DEFAULT '0',
  `event_typeID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_artefactID`),
  UNIQUE KEY `caveID` (`caveID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_defenseSystem`
--

CREATE TABLE IF NOT EXISTS `Event_defenseSystem` (
  `event_defenseSystemID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `defenseSystemID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_defenseSystemID`),
  UNIQUE KEY `caveID` (`caveID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_expansion`
--

CREATE TABLE IF NOT EXISTS `Event_expansion` (
  `event_expansionID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `expansionID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_expansionID`),
  UNIQUE KEY `caveID` (`caveID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_movement`
--

CREATE TABLE IF NOT EXISTS `Event_movement` (
  `event_movementID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `source_caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `target_caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `movementID` int(11) unsigned NOT NULL DEFAULT '0',
  `speedFactor` double NOT NULL DEFAULT '0',
  `exposeChance` double NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `artefactID` int(11) unsigned NOT NULL DEFAULT '0',
  `heroID` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_movementID`),
  KEY `caveID` (`caveID`),
  KEY `source_caveID` (`source_caveID`),
  KEY `target_caveID` (`target_caveID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_science`
--

CREATE TABLE IF NOT EXISTS `Event_science` (
  `event_scienceID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `scienceID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_scienceID`),
  UNIQUE KEY `playerScienceUnique` (`playerID`,`scienceID`),
  UNIQUE KEY `caveID` (`caveID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_trade`
--

CREATE TABLE IF NOT EXISTS `Event_trade` (
  `event_tradeID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `targetID` int(11) unsigned NOT NULL DEFAULT '0',
  `tradeID` int(11) unsigned NOT NULL DEFAULT '0',
  `impactID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `specialdurationminutes` int(6) DEFAULT '0',
  PRIMARY KEY (`event_tradeID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_tradeEnd`
--

CREATE TABLE IF NOT EXISTS `Event_tradeEnd` (
  `activeTradeID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `tradeID` int(11) unsigned NOT NULL DEFAULT '0',
  `impactID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`activeTradeID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_unit`
--

CREATE TABLE IF NOT EXISTS `Event_unit` (
  `event_unitID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `unitID` int(11) unsigned NOT NULL DEFAULT '0',
  `quantity` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_unitID`),
  UNIQUE KEY `caveID` (`caveID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_weather`
--

CREATE TABLE IF NOT EXISTS `Event_weather` (
  `event_weatherID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `regionID` int(11) unsigned NOT NULL DEFAULT '0',
  `weatherID` int(11) unsigned NOT NULL DEFAULT '0',
  `impactID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_weatherID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_weatherEnd`
--

CREATE TABLE IF NOT EXISTS `Event_weatherEnd` (
  `activeWeatherID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `regionID` int(11) unsigned NOT NULL DEFAULT '0',
  `weatherID` int(11) unsigned NOT NULL DEFAULT '0',
  `impactID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`activeWeatherID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_wonder`
--

CREATE TABLE IF NOT EXISTS `Event_wonder` (
  `event_wonderID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `casterID` int(11) unsigned NOT NULL DEFAULT '0',
  `sourceID` int(11) unsigned NOT NULL DEFAULT '0',
  `targetID` int(11) unsigned NOT NULL DEFAULT '0',
  `wonderID` int(11) unsigned NOT NULL DEFAULT '0',
  `impactID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `specialdurationminutes` int(6) DEFAULT '0',
  `tribeCaveWonderCaveID` int( 11 ) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_wonderID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_wonderEnd`
--

CREATE TABLE IF NOT EXISTS `Event_wonderEnd` (
  `activeWonderID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `casterID` int(11) unsigned NOT NULL DEFAULT '0',
  `wonderID` int(11) unsigned NOT NULL DEFAULT '0',
  `impactID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `tribeCaveWonderCaveID` int( 11 ) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`activeWonderID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Event_hero`
--

CREATE TABLE IF NOT EXISTS `Event_hero` (
  `event_heroID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `heroID` int(11) unsigned NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_heroID`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Hero`
--

CREATE TABLE IF NOT EXISTS `Hero` (
  `heroID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `heroTypeID` int(11) unsigned NOT NULL DEFAULT '0',
  `exp` int(11) unsigned NOT NULL DEFAULT '0',
  `lvl` int(11) unsigned NOT NULL DEFAULT '0',
  `healPoints` int(11) unsigned NOT NULL DEFAULT '0',
  `maxHealPoints` int(11) unsigned NOT NULL DEFAULT '0',
  `caveID` int(11) unsigned NOT NULL DEFAULT '0',
  `isAlive` tinyint(1) NOT NULL DEFAULT '0',
  `isMoving` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `tpFree` int(11) unsigned NOT NULL DEFAULT '0',
  `maxHpLvl` int(11) unsigned NOT NULL DEFAULT '0',
  `forceLvl` int(11) unsigned NOT NULL DEFAULT '0',
  `force` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `regHpLvl` int(11) unsigned NOT NULL DEFAULT '0',
  `regHP` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`heroID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Hero_rituals`
--

CREATE TABLE IF NOT EXISTS `Hero_rituals` (
  `ritualID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `duration` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '43200',
  PRIMARY KEY (`ritualID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Log_0`
--

CREATE TABLE IF NOT EXISTS `Log_0` (
  `logID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned DEFAULT NULL,
  `caveID` int(11) unsigned DEFAULT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request` mediumtext COLLATE utf8_unicode_ci,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sessionID` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`logID`),
  KEY `playerID` (`playerID`,`caveID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Log_1`
--

CREATE TABLE IF NOT EXISTS `Log_1` (
  `logID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned DEFAULT NULL,
  `caveID` int(11) unsigned DEFAULT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request` mediumtext COLLATE utf8_unicode_ci,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sessionID` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`logID`),
  KEY `playerID` (`playerID`,`caveID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Log_2`
--

CREATE TABLE IF NOT EXISTS `Log_2` (
  `logID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned DEFAULT NULL,
  `caveID` int(11) unsigned DEFAULT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request` mediumtext COLLATE utf8_unicode_ci,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sessionID` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`logID`),
  KEY `playerID` (`playerID`,`caveID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Log_3`
--

CREATE TABLE IF NOT EXISTS `Log_3` (
  `logID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned DEFAULT NULL,
  `caveID` int(11) unsigned DEFAULT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request` mediumtext COLLATE utf8_unicode_ci,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sessionID` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`logID`),
  KEY `playerID` (`playerID`,`caveID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Log_4`
--

CREATE TABLE IF NOT EXISTS `Log_4` (
  `logID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned DEFAULT NULL,
  `caveID` int(11) unsigned DEFAULT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request` mediumtext COLLATE utf8_unicode_ci,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sessionID` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`logID`),
  KEY `playerID` (`playerID`,`caveID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Log_5`
--

CREATE TABLE IF NOT EXISTS `Log_5` (
  `logID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned DEFAULT NULL,
  `caveID` int(11) unsigned DEFAULT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request` mediumtext COLLATE utf8_unicode_ci,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sessionID` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`logID`),
  KEY `playerID` (`playerID`,`caveID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Log_6`
--

CREATE TABLE IF NOT EXISTS `Log_6` (
  `logID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned DEFAULT NULL,
  `caveID` int(11) unsigned DEFAULT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request` mediumtext COLLATE utf8_unicode_ci,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sessionID` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`logID`),
  KEY `playerID` (`playerID`,`caveID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Message`
--

CREATE TABLE IF NOT EXISTS `Message` (
  `messageID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipientID` int(10) unsigned NOT NULL DEFAULT '0',
  `senderID` int(11) unsigned NOT NULL DEFAULT '0',
  `messageClass` int(11) unsigned NOT NULL DEFAULT '0',
  `messageSubject` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `messageText` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `messageXML` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `messageTime` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `recipientDeleted` int(11) unsigned NOT NULL DEFAULT '0',
  `senderDeleted` int(11) unsigned NOT NULL DEFAULT '0',
  `read` int(11) unsigned NOT NULL DEFAULT '0',
  `flag` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`messageID`),
  KEY `recipientID` (`recipientID`),
  KEY `recipientDeleted` (`recipientDeleted`),
  KEY `senderID` (`senderID`),
  KEY `senderDeleted` (`senderDeleted`),
  KEY `read` (`read`),
  KEY `messageClass` (`messageClass`),
  KEY `messageTime` (`messageTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `OldTribes`
--

CREATE TABLE IF NOT EXISTS `OldTribes` (
  `tribeID` int(11) unsigned NOT NULL DEFAULT '0',
  `tag` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `used` int(1) NOT NULL DEFAULT '0',
  `points_rank` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Player`
--

CREATE TABLE IF NOT EXISTS `Player` (
  `playerID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `npcID` int(11) unsigned NOT NULL DEFAULT '0',
  `email2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(90) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fame` int(11) NOT NULL DEFAULT '0',
  `tribeID` int(11) unsigned NOT NULL DEFAULT '0',
  `jabberName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `jabberPassword` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tribeBlockEnd` varchar(14) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sex` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `origin` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `icq` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template` int(2) unsigned NOT NULL DEFAULT '0',
  `caveOrderbyCoords` tinyint(1) NOT NULL DEFAULT '0',
  `secureCaveCredits` int(2) unsigned NOT NULL DEFAULT '0',
  `questionCredits` int(10) unsigned NOT NULL DEFAULT '0',
  `takeover_max_caves` int(11) unsigned NOT NULL DEFAULT '0',
  `description` mediumtext COLLATE utf8_unicode_ci,
  `gfxpath` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'http://www.uga-agga.de/game/gfx',
  `awards` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lastVote` int(10) unsigned NOT NULL DEFAULT '0',
  `language` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'de_DE',
  `timeCorrection` tinyint(4) NOT NULL DEFAULT '0',
  `body_count` int(11) unsigned NOT NULL DEFAULT '0',
  `warpoints_pos` int(11) unsigned NOT NULL DEFAULT '0',
  `warpoints_neg` int(11) unsigned NOT NULL DEFAULT '0',
  `suggestion_credits` smallint(6) DEFAULT '0',
  `referer_count` int(11) NOT NULL DEFAULT '0',
  `noStatistic` tinyint(1) NOT NULL DEFAULT '0',
  `notInactive` tinyint(1) NOT NULL DEFAULT '0',
  `heroID` tinyint(1) NOT NULL DEFAULT '0',
  `auth` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `donateLocked` varchar(4096) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tutorialID` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`playerID`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `jabberName` (`jabberName`),
  KEY `tribeID` (`tribeID`),
  KEY `noStatistic` (`noStatistic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `player_history`
--

CREATE TABLE IF NOT EXISTS `player_history` (
  `historyID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `entry` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`historyID`),
  KEY `timestamp` (`timestamp`),
  KEY `playerID` (`playerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Questionnaire_answers`
--

CREATE TABLE IF NOT EXISTS `Questionnaire_answers` (
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `questionID` int(11) unsigned NOT NULL DEFAULT '0',
  `choiceID` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`playerID`,`questionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Questionnaire_choices`
--

CREATE TABLE IF NOT EXISTS `Questionnaire_choices` (
  `questionID` int(11) unsigned NOT NULL DEFAULT '0',
  `choiceID` int(11) unsigned NOT NULL DEFAULT '0',
  `choiceText` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`questionID`,`choiceID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Questionnaire_presents`
--

CREATE TABLE IF NOT EXISTS `Questionnaire_presents` (
  `presentID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hour` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '*',
  `day_of_month` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '*',
  `month` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '*',
  `phase_of_moon` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '*',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `credits` int(11) unsigned NOT NULL DEFAULT '0',
  `use_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`presentID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Questionnaire_questions`
--

CREATE TABLE IF NOT EXISTS `Questionnaire_questions` (
  `questionID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `questionText` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `expiresOn` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credits` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`questionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Ranking`
--

CREATE TABLE IF NOT EXISTS `Ranking` (
  `playerID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(90) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `religion` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `rank` int(11) unsigned NOT NULL DEFAULT '0',
  `average` int(11) unsigned NOT NULL DEFAULT '0',
  `average_0` int(11) unsigned NOT NULL DEFAULT '0',
  `average_1` int(11) unsigned NOT NULL DEFAULT '0',
  `average_2` int(11) unsigned NOT NULL DEFAULT '0',
  `military` int(11) unsigned NOT NULL DEFAULT '0',
  `military_rank` int(11) unsigned NOT NULL DEFAULT '0',
  `resources` int(11) unsigned NOT NULL DEFAULT '0',
  `resources_rank` int(11) unsigned NOT NULL DEFAULT '0',
  `buildings` int(11) unsigned NOT NULL DEFAULT '0',
  `buildings_rank` int(11) unsigned NOT NULL DEFAULT '0',
  `sciences` int(11) unsigned NOT NULL DEFAULT '0',
  `sciences_rank` int(11) unsigned NOT NULL DEFAULT '0',
  `artefacts` int(11) unsigned NOT NULL DEFAULT '0',
  `artefacts_rank` int(11) unsigned NOT NULL DEFAULT '0',
  `tribePoints` int(11) unsigned NOT NULL DEFAULT '0',
  `caves` int(11) unsigned NOT NULL DEFAULT '0',
  `tribeFame` int(11) NOT NULL DEFAULT '0',
  `playerPoints` int(11) unsigned NOT NULL DEFAULT '0',
  `fame` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`playerID`),
  KEY `rank` (`rank`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `RankingTribe`
--

CREATE TABLE IF NOT EXISTS `RankingTribe` (
  `rankingID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `calculateTime` int(11) unsigned NOT NULL DEFAULT '0',
  `tribeID` int(11) unsigned NOT NULL DEFAULT '0',
  `rank` int(11) unsigned NOT NULL DEFAULT '0',
  `members` int(11) unsigned NOT NULL DEFAULT '0',
  `fame` int(11) NOT NULL DEFAULT '0',
  `fame_rank` int(11) NOT NULL DEFAULT '0',
  `caves` int(11) unsigned NOT NULL DEFAULT '0',
  `points_rank` int(11) unsigned NOT NULL DEFAULT '0',
  `playerAverage` int(11) NOT NULL DEFAULT '0',
  `warpoints` int(11) unsigned NOT NULL DEFAULT '0',
  `glory` int(11) unsigned NOT NULL DEFAULT '0',
  `war_won` int(11) unsigned NOT NULL DEFAULT '0',
  `war_lost` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rankingID`),
  UNIQUE KEY `tribeID` (`tribeID`),
  KEY `rank` (`rank`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Regions`
--

CREATE TABLE IF NOT EXISTS `Regions` (
  `regionID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(90) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `startRegion` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `weather` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`regionID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Relation`
--

CREATE TABLE IF NOT EXISTS `Relation` (
  `relationID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tribeID` int(11) unsigned NOT NULL DEFAULT '0',
  `tribeID_target` int(11) unsigned NOT NULL DEFAULT '0',
  `relationType` int(11) NOT NULL DEFAULT '0',
  `timestamp` char(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `duration` char(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tribe_rankingPoints` int(11) unsigned NOT NULL DEFAULT '0',
  `target_rankingPoints` int(11) unsigned NOT NULL DEFAULT '0',
  `defenderMultiplicator` double NOT NULL DEFAULT '0',
  `attackerMultiplicator` double NOT NULL DEFAULT '0',
  `attackerReceivesFame` int(11) unsigned NOT NULL DEFAULT '0',
  `defenderReceivesFame` int(11) unsigned NOT NULL DEFAULT '0',
  `fame` int(11) NOT NULL DEFAULT '0',
  `moral` int(11) NOT NULL DEFAULT '0',
  `target_members` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`relationID`),
  UNIQUE KEY `oneRelationUnique` (`tribeID`,`tribeID_target`),
  KEY `tribeID` (`tribeID`),
  KEY `tribeID_target` (`tribeID_target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Session`
--

CREATE TABLE IF NOT EXISTS `Session` (
  `sessionID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `s_id` varchar(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `s_id_used` tinyint(4) NOT NULL DEFAULT '0',
  `lastAction` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `ident` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `microtime` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `loginip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `loginchecksum` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `loginnoscript` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessionID`),
  UNIQUE KEY `playerID` (`playerID`),
  UNIQUE KEY `s_id` (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `StartValue`
--

CREATE TABLE IF NOT EXISTS `StartValue` (
  `dbFieldName` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` int(11) unsigned NOT NULL DEFAULT '0',
  `easyStart` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `dbFieldName` (`dbFieldName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Statistic`
--

CREATE TABLE IF NOT EXISTS `Statistic` (
  `type` int(2) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `unique` (`type`,`name`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `StatisticUnit`
--

CREATE TABLE IF NOT EXISTS `StatisticUnit` (
  `type` int(11) NOT NULL,
  `type_sub` int(11) NOT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Suggestions`
--

CREATE TABLE IF NOT EXISTS `Suggestions` (
  `suggestionID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  `Suggestion` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`suggestionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tradelock`
--

CREATE TABLE IF NOT EXISTS `tradelock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PlayerID` int(11) NOT NULL,
  `cat` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `LockTill` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `PlayerID` (`PlayerID`),
  KEY `group` (`cat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Tribe`
--

CREATE TABLE IF NOT EXISTS `Tribe` (
  `tribeID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(90) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `leaderID` int(11) unsigned NOT NULL DEFAULT '0',
  `created` varchar(14) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `governmentID` enum('1','2') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `duration` varchar(14) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fame` int(11) NOT NULL DEFAULT '0',
  `awards` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `valid` int(1) NOT NULL DEFAULT '0',
  `validatetime` varchar(14) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `body_count` int(11) unsigned NOT NULL DEFAULT '0',
  `warpoints_pos` int(11) NOT NULL DEFAULT '0',
  `warpoints_neg` int(11) NOT NULL DEFAULT '0',
  `war_won` int(11) unsigned NOT NULL DEFAULT '0',
  `war_lost` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tribeID`),
  UNIQUE KEY `name` (`name`),
  KEY `leaderID` (`leaderID`),
  KEY `tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `TribeHistory`
--

CREATE TABLE IF NOT EXISTS `TribeHistory` (
  `tribeHistoryID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tribeID` int(11) unsigned NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ingameTime` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`tribeHistoryID`),
  KEY `timestamp` (`timestamp`),
  KEY `tribeID` (`tribeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `TribeMessage`
--

CREATE TABLE IF NOT EXISTS `TribeMessage` (
  `tribeMessageID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tribeID` int(11) unsigned NOT NULL DEFAULT '0',
  `messageClass` int(11) unsigned NOT NULL DEFAULT '0',
  `messageSubject` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `messageText` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `messageTime` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `recipientDeleted` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tribeMessageID`),
  KEY `messageClass` (`messageClass`),
  KEY `recipientDeleted` (`recipientDeleted`),
  KEY `tribeID` (`tribeID`),
  KEY `messageTime` (`messageTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `TribeStorageDonations`
--

CREATE TABLE IF NOT EXISTS `TribeStorageDonations` (
  `donationID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playerID` int(11) NOT NULL,
  `tribeID` int(11) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`donationID`),
  KEY `tribeID` (`tribeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `TribeElection`
--

CREATE TABLE IF NOT EXISTS `TribeElection` (
  `electionID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tribe` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tribeID` int(11) unsigned NOT NULL DEFAULT '0',
  `voterID` int(11) unsigned NOT NULL DEFAULT '0',
  `playerID` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`electionID`),
  UNIQUE KEY `voterID` (`voterID`),
  KEY `playerID` (`playerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
