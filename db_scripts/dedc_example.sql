-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 13, 2014 at 09:34 PM
-- Server version: 5.5.35
-- PHP Version: 5.4.6-1ubuntu1.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
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
  `ancestorId` char(44) NOT NULL,
  `descendantId` char(44) NOT NULL,
  `depth` int(11) NOT NULL,
  KEY `ancestorId` (`ancestorId`),
  KEY `descendantId` (`descendantId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dianode`
--

CREATE TABLE IF NOT EXISTS `dianode` (
  `diagramId` char(44) DEFAULT NULL,
  `diaNodeId` char(44) NOT NULL,
  KEY `diaNodeId` (`diaNodeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dianode`
--

INSERT INTO `dianode` (`diagramId`, `diaNodeId`) VALUES
(NULL, 'LMvkQXmSqyBjsOzeCVDjLpQsTmBLE81vQx8LgH3F8Igx');

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
('99Zx2soDBHMjMldHxz0heLDCEeiPizpJ0kpRD707ptox', 30, 50),
('cfhEv3B3cRPigxvf6dXjbrIM0q3vxfGeaaxthaEiRVYx', 15, 22),
('LMvkQXmSqyBjsOzeCVDjLpQsTmBLE81vQx8LgH3F8Igx', 35, 10),
('ZDgSEiKx5XJPrJKDGAa1K9Zmb7C4d3OoQ4VDKBTErxYx', 10, 50),
('zJEf2kqODn3PxAU5cxj3hSsOoFxobbx1nGah0wLvob8x', 20, 50);

-- --------------------------------------------------------

--
-- Table structure for table `element_list`
--

CREATE TABLE IF NOT EXISTS `element_list` (
  `diagramId` char(44) NOT NULL,
  `elementId` char(44) NOT NULL,
  KEY `elementId` (`elementId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `element_list`
--

INSERT INTO `element_list` (`diagramId`, `elementId`) VALUES
('ljGmxv7q3E5E07bbXYjpNpfiM3wr8DeyWo5EZFseujEx', '99Zx2soDBHMjMldHxz0heLDCEeiPizpJ0kpRD707ptox'),
('ljGmxv7q3E5E07bbXYjpNpfiM3wr8DeyWo5EZFseujEx', 'zJEf2kqODn3PxAU5cxj3hSsOoFxobbx1nGah0wLvob8x'),
('ljGmxv7q3E5E07bbXYjpNpfiM3wr8DeyWo5EZFseujEx', 'ZDgSEiKx5XJPrJKDGAa1K9Zmb7C4d3OoQ4VDKBTErxYx'),
('ljGmxv7q3E5E07bbXYjpNpfiM3wr8DeyWo5EZFseujEx', 'LMvkQXmSqyBjsOzeCVDjLpQsTmBLE81vQx8LgH3F8Igx'),
('ljGmxv7q3E5E07bbXYjpNpfiM3wr8DeyWo5EZFseujEx', 'cfhEv3B3cRPigxvf6dXjbrIM0q3vxfGeaaxthaEiRVYx');

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
('99Zx2soDBHMjMldHxz0heLDCEeiPizpJ0kpRD707ptox', 'Some Store', 'DataStore', 'The Eugene'),
('cfhEv3B3cRPigxvf6dXjbrIM0q3vxfGeaaxthaEiRVYx', 'Some Dataflow', 'DataFlow', 'The Eugene'),
('ljGmxv7q3E5E07bbXYjpNpfiM3wr8DeyWo5EZFseujEx', 'New_DFD!', 'DataFlowDiagram', 'The Eugene'),
('LMvkQXmSqyBjsOzeCVDjLpQsTmBLE81vQx8LgH3F8Igx', 'Some Multiprocess', 'Multiprocess', 'The Eugene'),
('ZDgSEiKx5XJPrJKDGAa1K9Zmb7C4d3OoQ4VDKBTErxYx', 'Some Proc', 'Process', 'The Eugene'),
('zJEf2kqODn3PxAU5cxj3hSsOoFxobbx1nGah0wLvob8x', 'Some Interactor', 'ExternalInteractor', 'The Eugene');

-- --------------------------------------------------------

--
-- Table structure for table `link`
--

CREATE TABLE IF NOT EXISTS `link` (
  `id` char(44) NOT NULL,
  `originNode` char(44) DEFAULT NULL,
  `destinationNode` char(44) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `link`
--

INSERT INTO `link` (`id`, `originNode`, `destinationNode`) VALUES
('cfhEv3B3cRPigxvf6dXjbrIM0q3vxfGeaaxthaEiRVYx', 'ZDgSEiKx5XJPrJKDGAa1K9Zmb7C4d3OoQ4VDKBTErxYx', 'LMvkQXmSqyBjsOzeCVDjLpQsTmBLE81vQx8LgH3F8Igx');

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` char(44) NOT NULL,
  `linkId` char(44) NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `linkId`) VALUES
('ZDgSEiKx5XJPrJKDGAa1K9Zmb7C4d3OoQ4VDKBTErxYx', 'cfhEv3B3cRPigxvf6dXjbrIM0q3vxfGeaaxthaEiRVYx'),
('LMvkQXmSqyBjsOzeCVDjLpQsTmBLE81vQx8LgH3F8Igx', 'cfhEv3B3cRPigxvf6dXjbrIM0q3vxfGeaaxthaEiRVYx');

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
  ADD CONSTRAINT `dfd_ancestry_ibfk_1` FOREIGN KEY (`ancestorId`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dfd_ancestry_ibfk_2` FOREIGN KEY (`descendantId`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dianode`
--
ALTER TABLE `dianode`
  ADD CONSTRAINT `dianode_ibfk_1` FOREIGN KEY (`diaNodeId`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `element`
--
ALTER TABLE `element`
  ADD CONSTRAINT `element_ibfk_1` FOREIGN KEY (`id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `element_list`
--
ALTER TABLE `element_list`
  ADD CONSTRAINT `element_list_ibfk_1` FOREIGN KEY (`elementId`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
