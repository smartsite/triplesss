-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 08, 2020 at 10:25 AM
-- Server version: 8.0.22-0ubuntu0.20.04.2
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `members9`
--

-- --------------------------------------------------------

--
-- Table structure for table `aggregator`
--

CREATE TABLE `aggregator` (
  `id` int NOT NULL,
  `aggregator_id` int NOT NULL,
  `feed_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `channel`
--

CREATE TABLE `channel` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `name` varchar(126) NOT NULL,
  `description` text NOT NULL,
  `aggregator_id` int NOT NULL,
  `status` varchar(64) NOT NULL,
  `active` tinyint NOT NULL,
  `created_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `connection`
--

CREATE TABLE `connection` (
  `connection_type` smallint NOT NULL,
  `from_id` int NOT NULL,
  `to_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `connection_types`
--

CREATE TABLE `connection_types` (
  `id` int NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `content_post`
--

CREATE TABLE `content_post` (
  `id` int NOT NULL,
  `content_id` int NOT NULL,
  `content_type` varchar(64) NOT NULL,
  `post_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `feed`
--

CREATE TABLE `feed` (
  `id` int NOT NULL,
  `owner_id` int NOT NULL,
  `feed_name` varchar(64) NOT NULL,
  `feed_description` text NOT NULL,
  `created` date NOT NULL,
  `active` tinyint NOT NULL,
  `status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `feed_post`
--

CREATE TABLE `feed_post` (
  `id` varchar(64) NOT NULL,
  `feed_id` int NOT NULL,
  `post_id` varchar(64) NOT NULL,
  `visibility` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `id` int NOT NULL,
  `link` varchar(128) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `type` varchar(64) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `creator_id` int DEFAULT NULL,
  `visibility` tinyint NOT NULL DEFAULT '0',
  `tags` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `member_id` int NOT NULL,
  `user_id` int NOT NULL,
  `joined_date` date NOT NULL,
  `status` tinyint NOT NULL,
  `active` tinyint NOT NULL,
  `renewal_interval` varchar(16) NOT NULL,
  `renewal_date` date NOT NULL,
  `payment_method` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member_status`
--

CREATE TABLE `member_status` (
  `id` tinyint NOT NULL,
  `description` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int NOT NULL,
  `type` tinyint NOT NULL,
  `user_id` int NOT NULL,
  `notification_id` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int NOT NULL,
  `post_id` varchar(64) NOT NULL,
  `owner` int NOT NULL,
  `title` text NOT NULL,
  `visibility` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `postcode`
--

CREATE TABLE `postcode` (
  `postcode` int UNSIGNED NOT NULL,
  `suburb` varchar(45) NOT NULL,
  `state` varchar(4) NOT NULL,
  `dc` varchar(45) NOT NULL,
  `type` varchar(45) NOT NULL,
  `lat` double NOT NULL DEFAULT '0',
  `lon` double NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `post_comment`
--

CREATE TABLE `post_comment` (
  `feed_post_id` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `post_id` varchar(64) NOT NULL,
  `visibility` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `owner_id` int NOT NULL,
  `profile_key` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `profile_keys`
--

CREATE TABLE `profile_keys` (
  `id` smallint NOT NULL,
  `key_name` varchar(64) NOT NULL,
  `description` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reaction`
--

CREATE TABLE `reaction` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `post_id` varchar(64) NOT NULL,
  `level` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` varchar(128) NOT NULL,
  `user_id` mediumint NOT NULL,
  `expires` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscriber`
--

CREATE TABLE `subscriber` (
  `feed_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `content_id` varchar(64) NOT NULL,
  `tags` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `text`
--

CREATE TABLE `text` (
  `id` int NOT NULL,
  `text_id` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `creator_id` int NOT NULL,
  `created` datetime NOT NULL,
  `visibility` tinyint NOT NULL DEFAULT '0',
  `tags` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` mediumint NOT NULL,
  `user_name` varchar(64) NOT NULL,
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address_1` text,
  `address_2` text,
  `state` varchar(3) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postcode` varchar(8) DEFAULT NULL,
  `country_code` varchar(4) DEFAULT NULL,
  `phone` varchar(14) DEFAULT NULL,
  `profile_post_id` int DEFAULT NULL,
  `profile_image_id` int DEFAULT NULL,
  `bio_post_id` int DEFAULT NULL,
  `last_login` varchar(11) DEFAULT NULL,
  `reg_link` varchar(64) DEFAULT NULL,
  `is_logged_in` tinyint(1) NOT NULL DEFAULT '0',
  `user_level` smallint NOT NULL DEFAULT '5',
  `active` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `visibility`
--

CREATE TABLE `visibility` (
  `id` tinyint NOT NULL,
  `description` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `_content`
--

CREATE TABLE `_content` (
  `id` int NOT NULL,
  `content_id` varchar(64) NOT NULL,
  `content_type` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aggregator`
--
ALTER TABLE `aggregator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_aggregator_id` (`aggregator_id`),
  ADD KEY `idx_feed_id` (`feed_id`);

--
-- Indexes for table `connection`
--
ALTER TABLE `connection`
  ADD UNIQUE KEY `idx_connection` (`from_id`,`to_id`);

--
-- Indexes for table `connection_types`
--
ALTER TABLE `connection_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `content_post`
--
ALTER TABLE `content_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_content_id` (`content_id`) USING BTREE;

--
-- Indexes for table `feed`
--
ALTER TABLE `feed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`creator_id`),
  ADD KEY `path` (`path`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD KEY `idx_member` (`member_id`);

--
-- Indexes for table `member_status`
--
ALTER TABLE `member_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_post_id` (`post_id`);

--
-- Indexes for table `postcode`
--
ALTER TABLE `postcode`
  ADD PRIMARY KEY (`postcode`,`suburb`),
  ADD KEY `idx_lon` (`lon`),
  ADD KEY `idx_lat` (`lat`);

--
-- Indexes for table `profile_keys`
--
ALTER TABLE `profile_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reaction`
--
ALTER TABLE `reaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `text`
--
ALTER TABLE `text`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `_content`
--
ALTER TABLE `_content`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aggregator`
--
ALTER TABLE `aggregator`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_post`
--
ALTER TABLE `content_post`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feed`
--
ALTER TABLE `feed`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile_keys`
--
ALTER TABLE `profile_keys`
  MODIFY `id` smallint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reaction`
--
ALTER TABLE `reaction`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `text`
--
ALTER TABLE `text`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` mediumint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `_content`
--
ALTER TABLE `_content`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
