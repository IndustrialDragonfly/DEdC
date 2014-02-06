-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 06, 2014 at 04:16 AM
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
('cabumEiAZdExZKbHDaumNT9KEoN0lwUJZwgyISIDre4x', 20, 50),
('hClaOolenANwol8HZhcIK7ulTfDiEwFtRM2CMo9Ppxgx', 15, 22),
('mmWyh0gmygRejKr2meuRGSfLAl9oceUAhrG7foCquFox', 30, 50),
('nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx', 35, 10),
('TgRGVyTIh0srk2zw7OndjUx9AcyNx4AymEWiOMnDMPwx', 10, 50);

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
('0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx', 'mmWyh0gmygRejKr2meuRGSfLAl9oceUAhrG7foCquFox'),
('0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx', 'cabumEiAZdExZKbHDaumNT9KEoN0lwUJZwgyISIDre4x'),
('0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx', 'TgRGVyTIh0srk2zw7OndjUx9AcyNx4AymEWiOMnDMPwx'),
('0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx', 'nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx'),
('0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx', 'hClaOolenANwol8HZhcIK7ulTfDiEwFtRM2CMo9Ppxgx');

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
('0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx', 'New_DFD!', 'DataFlowDiagram', 'The Eugene'),
('cabumEiAZdExZKbHDaumNT9KEoN0lwUJZwgyISIDre4x', 'Some Interactor', 'ExternalInteractor', 'The Eugene'),
('hClaOolenANwol8HZhcIK7ulTfDiEwFtRM2CMo9Ppxgx', 'Some Dataflow', 'DataFlow', 'The Eugene'),
('mmWyh0gmygRejKr2meuRGSfLAl9oceUAhrG7foCquFox', 'Some Store', 'DataStore', 'The Eugene'),
('nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx', 'Some Multiprocess', 'Multiprocess', 'The Eugene'),
('TgRGVyTIh0srk2zw7OndjUx9AcyNx4AymEWiOMnDMPwx', 'Some Proc', 'Process', 'The Eugene');

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
('hClaOolenANwol8HZhcIK7ulTfDiEwFtRM2CMo9Ppxgx', 'TgRGVyTIh0srk2zw7OndjUx9AcyNx4AymEWiOMnDMPwx', 'nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx');

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `id` char(44) NOT NULL,
  `link_id` char(44) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `link_id`) VALUES
('nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx', 'hClaOolenANwol8HZhcIK7ulTfDiEwFtRM2CMo9Ppxgx'),
('TgRGVyTIh0srk2zw7OndjUx9AcyNx4AymEWiOMnDMPwx', 'hClaOolenANwol8HZhcIK7ulTfDiEwFtRM2CMo9Ppxgx');

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
(NULL, 'nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx');

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;