SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `adm_banners` (
  `bID` int(11) NOT NULL AUTO_INCREMENT,
  `bCategoryID` int(11) NOT NULL,
  `bMachineName` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `bDescription` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bCode` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bImage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bText` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bStyleCSS` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bURL` varchar(768) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bStartDate` date NOT NULL DEFAULT '0000-00-00',
  `bEndDate` date NOT NULL DEFAULT '0000-00-00',
  `bPosition` smallint(6) NOT NULL DEFAULT '1',
  `bActive` tinyint(1) NOT NULL DEFAULT '1',
  `bSWF` tinyint(1) NOT NULL DEFAULT '0',
  `bCreationDate` date NOT NULL DEFAULT '2011-02-01',
  `bContentType` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:Text, 1:Image, 2:Code',
  `bSWFBgColor` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#FFFFFF',
  `bSWFWidth` smallint(6) NOT NULL DEFAULT '0',
  `bSWFHeight` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bID`),
  KEY `CategoryID` (`bCategoryID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=28 ;


CREATE TABLE IF NOT EXISTS `adm_categories` (
  `cID` int(11) NOT NULL AUTO_INCREMENT,
  `cMachineName` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '_',
  `cDescription` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cWidth` smallint(6) NOT NULL DEFAULT '0',
  `cHeight` smallint(6) NOT NULL DEFAULT '0',
  `cActive` tinyint(1) NOT NULL DEFAULT '1',
  `cCreationDate` date NOT NULL DEFAULT '2011-02-01',
  `cUserID` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cID`),
  KEY `cUserID` (`cUserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;


CREATE TABLE IF NOT EXISTS `adm_statistics` (
  `sID` int(11) NOT NULL AUTO_INCREMENT,
  `sBannerID` int(11) NOT NULL,
  `sDate` datetime NOT NULL,
  `sClicks` bigint(20) NOT NULL DEFAULT '0',
  `sImpressions` bigint(20) NOT NULL DEFAULT '0',
  `sIP` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sID`),
  KEY `sBannerID` (`sBannerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=149 ;


CREATE TABLE IF NOT EXISTS `adm_users` (
  `uID` int(11) NOT NULL AUTO_INCREMENT,
  `uUsername` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uPassword` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uPrivilege` tinyint(4) NOT NULL DEFAULT '0',
  `uCookie` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

INSERT INTO `adm_users` (`uID`, `uUsername`, `uPassword`, `uPrivilege`, `uCookie`) VALUES
(1, 'admin', 'admin', 0, ''),
(2, 'user', 'user', 1, '');


ALTER TABLE `adm_banners`
  ADD CONSTRAINT `adm_banners_ibfk_1` FOREIGN KEY (`bCategoryID`) REFERENCES `adm_categories` (`cID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `adm_categories`
  ADD CONSTRAINT `adm_categories_ibfk_1` FOREIGN KEY (`cUserID`) REFERENCES `adm_users` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `adm_statistics`
  ADD CONSTRAINT `adm_statistics_ibfk_1` FOREIGN KEY (`sBannerID`) REFERENCES `adm_banners` (`bID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
