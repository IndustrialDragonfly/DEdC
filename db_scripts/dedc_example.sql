-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 06, 2014 at 01:06 AM
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
('1YihLkUkFuBSTi2TiWtLFxtEgdadZTPFxTWS9Gr6S4sx', 0, 0),
('4L4SxhB3YNxLxX91dFltRAol5s3arfQbs9pw15KkSvcx', 0, 0),
('da0T8YuBCZOmK2YxXVFkk7fxr9PnoALmxktwqFM6l24x', 0, 0),
('lMVs0dD5qsGPQUxFijT8ZxRRc949DI4OwmKP3qJfAKcx', 0, 0),
('NV2GJKKKbm7DqjzPojTCo3SzjeQj3iuGmGS8jtFu22Yx', 0, 0);

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
('OSETR9gSJuYBBZg0FDw0fUxl2CdeyBxqd323oRmZgtAx', 'lMVs0dD5qsGPQUxFijT8ZxRRc949DI4OwmKP3qJfAKcx'),
('OSETR9gSJuYBBZg0FDw0fUxl2CdeyBxqd323oRmZgtAx', '4L4SxhB3YNxLxX91dFltRAol5s3arfQbs9pw15KkSvcx'),
('OSETR9gSJuYBBZg0FDw0fUxl2CdeyBxqd323oRmZgtAx', '1YihLkUkFuBSTi2TiWtLFxtEgdadZTPFxTWS9Gr6S4sx'),
('OSETR9gSJuYBBZg0FDw0fUxl2CdeyBxqd323oRmZgtAx', 'da0T8YuBCZOmK2YxXVFkk7fxr9PnoALmxktwqFM6l24x'),
('OSETR9gSJuYBBZg0FDw0fUxl2CdeyBxqd323oRmZgtAx', 'NV2GJKKKbm7DqjzPojTCo3SzjeQj3iuGmGS8jtFu22Yx');

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
('1YihLkUkFuBSTi2TiWtLFxtEgdadZTPFxTWS9Gr6S4sx', '', 'Process', ''),
('4L4SxhB3YNxLxX91dFltRAol5s3arfQbs9pw15KkSvcx', '', 'Multiprocess', ''),
('da0T8YuBCZOmK2YxXVFkk7fxr9PnoALmxktwqFM6l24x', '', 'DataStore', ''),
('lMVs0dD5qsGPQUxFijT8ZxRRc949DI4OwmKP3qJfAKcx', '', 'ExternalInteractor', ''),
('NV2GJKKKbm7DqjzPojTCo3SzjeQj3iuGmGS8jtFu22Yx', '', 'DataFlow', ''),
('OSETR9gSJuYBBZg0FDw0fUxl2CdeyBxqd323oRmZgtAx', '', 'DataFlowDiagram', '');

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
('NV2GJKKKbm7DqjzPojTCo3SzjeQj3iuGmGS8jtFu22Yx', '1YihLkUkFuBSTi2TiWtLFxtEgdadZTPFxTWS9Gr6S4sx', 'da0T8YuBCZOmK2YxXVFkk7fxr9PnoALmxktwqFM6l24x');

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
('DbzJoVaABoa8CfGttydpJRnIo15UhT9A9u4qJ269nW4=', 'fQCd3d7WToOEEXwWnAiemD5JMaladMyrdQamw6lBAgg='),
('T3SyzKqXHLdfm56jxpLiurHlpGuzeCXDQ4lrEOgApXg=', 'LxeITgUOwSA8nHhxxMLi2MB4pMSCUWmXEtrGC152E8o='),
('03DXnbAEhtiNRlUxXx5hrBkCJfrBf0jHVcVQt1VC/Gk=', 'LxeITgUOwSA8nHhxxMLi2MB4pMSCUWmXEtrGC152E8o='),
('6G48hQRYz5H/V1IOGF7HKlE1OQsrNsyWYt1ppa1BijY=', '1SnOvorKe4lRUo8s9j9sHivLmxdm1gzdktXAjufZOxA='),
('CUvShB0lDsCosC1yf7GbCkqBbV84UtImV5gpx/xdnSU=', '1SnOvorKe4lRUo8s9j9sHivLmxdm1gzdktXAjufZOxA='),
('9oMqe8xwi9xCnzOfzwAX211SB6LMVLUsnrjLfDPjCkwx', '4CykPDPGj9ZxRpEX4511g5dgjfB8WIn8aR6JsvNtcRwx'),
('QopmGxB622JJHXSKtqnL9RkOvUnwueDRBMblFtqzQK4x', '4CykPDPGj9ZxRpEX4511g5dgjfB8WIn8aR6JsvNtcRwx'),
('9eHmxuaKczduyfocCwqRQMRR1xzJKWIfViXOWLxZaUYx', 'loNrkOS8BzymmOnMoK2eK8xdN52esKVy22i6Hd1c5rIx'),
('MMntvjNNG2OtwnuuwxxifSztkFg6jqa3kZtI0wwGfBUx', 'loNrkOS8BzymmOnMoK2eK8xdN52esKVy22i6Hd1c5rIx'),
('1YihLkUkFuBSTi2TiWtLFxtEgdadZTPFxTWS9Gr6S4sx', 'NV2GJKKKbm7DqjzPojTCo3SzjeQj3iuGmGS8jtFu22Yx'),
('da0T8YuBCZOmK2YxXVFkk7fxr9PnoALmxktwqFM6l24x', 'NV2GJKKKbm7DqjzPojTCo3SzjeQj3iuGmGS8jtFu22Yx');

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
('OSETR9gSJuYBBZg0FDw0fUxl2CdeyBxqd323oRmZgtAx', '4L4SxhB3YNxLxX91dFltRAol5s3arfQbs9pw15KkSvcx');

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
