-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 06, 2014 at 12:35 AM
-- Server version: 5.5.35-0ubuntu0.13.10.2
-- PHP Version: 5.5.3-1ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dedc`
--

-- --------------------------------------------------------

--
-- Table structure for table `dfd_ancestry`
--

CREATE TABLE IF NOT EXISTS `dfd_ancestry` (
  `ancestor_id` char(44) NOT NULL,
  `descendant_id` char(44) NOT NULL,
  `depth` int(11) NOT NULL,
  KEY `ancestor_id` (`ancestor_id`),
  KEY `descendant_id` (`descendant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `element`
--

CREATE TABLE IF NOT EXISTS `element` (
  `id` char(44) NOT NULL,
  `x` int(11) DEFAULT NULL,
  `y` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `element`
--

INSERT INTO `element` (`id`, `x`, `y`) VALUES
('BP8GFaaoQ4fZFtJxmLJa/lTQr4TOEOFluwISCq5jKAI=', 0, 0),
('csxEwfIe5rxo8hg10KSGhxK7PaoJukMUUjYcDes4up4=', 0, 0),
('DbzJoVaABoa8CfGttydpJRnIo15UhT9A9u4qJ269nW4=', 0, 0),
('fQCd3d7WToOEEXwWnAiemD5JMaladMyrdQamw6lBAgg=', 0, 0),
('neYl5HHmoE1SlnFUBAJhyrZpCyrIxVdMTqjJfCVfusc=', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `element_list`
--

CREATE TABLE IF NOT EXISTS `element_list` (
  `dfd_id` char(44) NOT NULL,
  `el_id` char(44) NOT NULL,
  KEY `el_id` (`el_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `element_list`
--

INSERT INTO `element_list` (`dfd_id`, `el_id`) VALUES
('6ueuj947ERrcl2bBEs/orw4NrSWIX/Zchcl3Fxz3Thw=', 'neYl5HHmoE1SlnFUBAJhyrZpCyrIxVdMTqjJfCVfusc='),
('6ueuj947ERrcl2bBEs/orw4NrSWIX/Zchcl3Fxz3Thw=', 'csxEwfIe5rxo8hg10KSGhxK7PaoJukMUUjYcDes4up4='),
('6ueuj947ERrcl2bBEs/orw4NrSWIX/Zchcl3Fxz3Thw=', 'BP8GFaaoQ4fZFtJxmLJa/lTQr4TOEOFluwISCq5jKAI='),
('6ueuj947ERrcl2bBEs/orw4NrSWIX/Zchcl3Fxz3Thw=', 'DbzJoVaABoa8CfGttydpJRnIo15UhT9A9u4qJ269nW4='),
('6ueuj947ERrcl2bBEs/orw4NrSWIX/Zchcl3Fxz3Thw=', 'fQCd3d7WToOEEXwWnAiemD5JMaladMyrdQamw6lBAgg=');

-- --------------------------------------------------------

--
-- Table structure for table `entity`
--

CREATE TABLE IF NOT EXISTS `entity` (
  `id` char(44) NOT NULL,
  `label` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `originator` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `entity`
--

INSERT INTO `entity` (`id`, `label`, `type`, `originator`) VALUES
('6ueuj947ERrcl2bBEs/orw4NrSWIX/Zchcl3Fxz3Thw=', '', 'DataFlowDiagram', ''),
('BP8GFaaoQ4fZFtJxmLJa/lTQr4TOEOFluwISCq5jKAI=', '', 'Process', ''),
('csxEwfIe5rxo8hg10KSGhxK7PaoJukMUUjYcDes4up4=', '', 'Multiprocess', ''),
('DbzJoVaABoa8CfGttydpJRnIo15UhT9A9u4qJ269nW4=', '', 'DataStore', ''),
('fQCd3d7WToOEEXwWnAiemD5JMaladMyrdQamw6lBAgg=', '', 'DataFlow', ''),
('neYl5HHmoE1SlnFUBAJhyrZpCyrIxVdMTqjJfCVfusc=', '', 'ExternalInteractor', '');

-- --------------------------------------------------------

--
-- Table structure for table `link`
--

CREATE TABLE IF NOT EXISTS `link` (
  `id` char(44) NOT NULL,
  `origin_id` char(44) DEFAULT NULL,
  `dest_id` char(44) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `link`
--

INSERT INTO `link` (`id`, `origin_id`, `dest_id`) VALUES
('fQCd3d7WToOEEXwWnAiemD5JMaladMyrdQamw6lBAgg=', 'BP8GFaaoQ4fZFtJxmLJa/lTQr4TOEOFluwISCq5jKAI=', 'DbzJoVaABoa8CfGttydpJRnIo15UhT9A9u4qJ269nW4=');

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` char(44) NOT NULL,
  `df_id` char(44) NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `df_id`) VALUES
('bjENhK10xVynQZTtrSgkMNxfzvLj2QDOFthZytsGa3A=', '3WxkfyP9AOiUmK2MtucD7C66QgfCcKhhf86lg8l2qGw='),
('MZZENoY79gA5pfTwEwYaD2grHaYRHEzZQfmDQWhglvk=', '3WxkfyP9AOiUmK2MtucD7C66QgfCcKhhf86lg8l2qGw='),
('OVEwncbJEEKOsNnHr49xspxcsHuwUSSXMKL9pJp09lI=', 'SfxTswom8d8azHuNmpiVXnrTzzLsHBlc3BRBsnRLx/w='),
('LxpA69Q84AX0q/VKYfUaWPimCSqDAjd0603ajdnCtiA=', 'SfxTswom8d8azHuNmpiVXnrTzzLsHBlc3BRBsnRLx/w='),
('NXVX8AFBmzoZsLyIrm0q1ZiM3b9yQWUgBDXjlrHgdLI=', 'gsJTpxuk3wsSrmpYiqZrlphxfZQCCVfY8Zy21iqJQCA='),
('OpSr1mEaeQKSO5lPNPjvFrMA1QWGxJpNv8uAzCZeWx0=', 'gsJTpxuk3wsSrmpYiqZrlphxfZQCCVfY8Zy21iqJQCA='),
('zwd3HWj2tVyMDKGNENjG44cZ163AKGEm4A6YGfBBMEE=', 'ki6IDrM1ro/1QbEkvVOcqK4nVCmmVwQ2lD9PXxEPxko='),
('drXsjeklZJ5SRIELEtPG5VLxyEDxIxdexO4QH7bDNjE=', 'ki6IDrM1ro/1QbEkvVOcqK4nVCmmVwQ2lD9PXxEPxko='),
('vcMSHaYlAGeFAIDUvflaPvaSlnxrXBado2IvR9eh9SI=', 'ZYvvksWOxBEgAZIXs9n3IRbxJpvEg/a08loGbRRCx9w='),
('w1e4Du9l2vSQuuT9yF11OOHXfa0slKId1iVXlVHxHgo=', 'ZYvvksWOxBEgAZIXs9n3IRbxJpvEg/a08loGbRRCx9w='),
('xavgtYW7TUyxSnRqM0KanW8WXiso2ohZNC4f8X8MEY8=', 'omdgRUSoRQhcpJNReAq0zG0EU1hT/BZLJcxxXHq/LBs='),
('ttoJXXFYvj9Imyi7IMQVr5GCohfJFRR9ijtBma9w2rA=', 'omdgRUSoRQhcpJNReAq0zG0EU1hT/BZLJcxxXHq/LBs='),
('BP8GFaaoQ4fZFtJxmLJa/lTQr4TOEOFluwISCq5jKAI=', 'fQCd3d7WToOEEXwWnAiemD5JMaladMyrdQamw6lBAgg='),
('DbzJoVaABoa8CfGttydpJRnIo15UhT9A9u4qJ269nW4=', 'fQCd3d7WToOEEXwWnAiemD5JMaladMyrdQamw6lBAgg=');

-- --------------------------------------------------------

--
-- Table structure for table `subdfdnode`
--

CREATE TABLE IF NOT EXISTS `subdfdnode` (
  `dfd_id` char(44) DEFAULT NULL,
  `subdfdnode_id` char(44) NOT NULL,
  KEY `subdfdnode_id` (`subdfdnode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `subdfdnode`
--

INSERT INTO `subdfdnode` (`dfd_id`, `subdfdnode_id`) VALUES
('6ueuj947ERrcl2bBEs/orw4NrSWIX/Zchcl3Fxz3Thw=', 'csxEwfIe5rxo8hg10KSGhxK7PaoJukMUUjYcDes4up4=');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`type`) VALUES
('DataFlow'),
('DataFlowDiagram'),
('DataStore'),
('ExternalInteractor'),
('Multiprocess'),
('Process');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dfd_ancestry`
--
ALTER TABLE `dfd_ancestry`
  ADD CONSTRAINT `dfd_ancestry_ibfk_1` FOREIGN KEY (`ancestor_id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dfd_ancestry_ibfk_2` FOREIGN KEY (`descendant_id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `element`
--
ALTER TABLE `element`
  ADD CONSTRAINT `element_ibfk_1` FOREIGN KEY (`id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `element_list`
--
ALTER TABLE `element_list`
  ADD CONSTRAINT `element_list_ibfk_1` FOREIGN KEY (`el_id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `entity`
--
ALTER TABLE `entity`
  ADD CONSTRAINT `entity_ibfk_1` FOREIGN KEY (`type`) REFERENCES `types` (`type`);

--
-- Constraints for table `link`
--
ALTER TABLE `link`
  ADD CONSTRAINT `link_ibfk_1` FOREIGN KEY (`id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `node_ibfk_1` FOREIGN KEY (`id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subdfdnode`
--
ALTER TABLE `subdfdnode`
  ADD CONSTRAINT `subdfdnode_ibfk_1` FOREIGN KEY (`subdfdnode_id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Database: `phpmyadmin`
--

-- --------------------------------------------------------

--
-- Table structure for table `pma_bookmark`
--

CREATE TABLE IF NOT EXISTS `pma_bookmark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dbase` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `user` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `query` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pma_column_info`
--

CREATE TABLE IF NOT EXISTS `pma_column_info` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `column_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `transformation` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `transformation_options` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pma_designer_coords`
--

CREATE TABLE IF NOT EXISTS `pma_designer_coords` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `x` int(11) DEFAULT NULL,
  `y` int(11) DEFAULT NULL,
  `v` tinyint(4) DEFAULT NULL,
  `h` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`db_name`,`table_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma_history`
--

CREATE TABLE IF NOT EXISTS `pma_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `db` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sqlquery` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`,`db`,`table`,`timevalue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pma_pdf_pages`
--

CREATE TABLE IF NOT EXISTS `pma_pdf_pages` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `page_nr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_descr` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`page_nr`),
  KEY `db_name` (`db_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pma_recent`
--

CREATE TABLE IF NOT EXISTS `pma_recent` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `tables` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma_recent`
--

INSERT INTO `pma_recent` (`username`, `tables`) VALUES
('root', '[{"db":"dedc","table":"entity"},{"db":"dedc","table":"element"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma_relation`
--

CREATE TABLE IF NOT EXISTS `pma_relation` (
  `master_db` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `master_table` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `master_field` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `foreign_db` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `foreign_table` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `foreign_field` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  KEY `foreign_field` (`foreign_db`,`foreign_table`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma_table_coords`
--

CREATE TABLE IF NOT EXISTS `pma_table_coords` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT '0',
  `x` float unsigned NOT NULL DEFAULT '0',
  `y` float unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma_table_info`
--

CREATE TABLE IF NOT EXISTS `pma_table_info` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `display_field` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`db_name`,`table_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma_table_uiprefs`
--

CREATE TABLE IF NOT EXISTS `pma_table_uiprefs` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `prefs` text COLLATE utf8_bin NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`,`db_name`,`table_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma_tracking`
--

CREATE TABLE IF NOT EXISTS `pma_tracking` (
  `db_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `table_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text COLLATE utf8_bin NOT NULL,
  `schema_sql` text COLLATE utf8_bin,
  `data_sql` longtext COLLATE utf8_bin,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') COLLATE utf8_bin DEFAULT NULL,
  `tracking_active` int(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`db_name`,`table_name`,`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma_userconfig`
--

CREATE TABLE IF NOT EXISTS `pma_userconfig` (
  `username` varchar(64) COLLATE utf8_bin NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `config_data` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';
--
-- Database: `test`
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
