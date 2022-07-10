-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 10, 2022 at 10:51 AM
-- Server version: 8.0.28-0ubuntu0.20.04.3
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

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
-- Stand-in structure for view `comments`
-- (See below for the actual view)
--
CREATE TABLE `comments` (
`content` text
,`created` datetime
,`feed_post_id` varchar(64)
,`owner` int
,`post_id` varchar(64)
,`user_name` varchar(64)
,`visibility` tinyint
);

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
-- Table structure for table `conversation`
--

CREATE TABLE `conversation` (
  `id` int NOT NULL,
  `name` varchar(64) NOT NULL,
  `started` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Stand-in structure for view `getposts`
-- (See below for the actual view)
--
CREATE TABLE `getposts` (
`content` mediumtext
,`content_type` varchar(5)
,`created` datetime
,`post_id` varchar(64)
);

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
-- Stand-in structure for view `images`
-- (See below for the actual view)
--
CREATE TABLE `images` (
`created` datetime
,`creator_id` int
,`image` varchar(383)
,`post_id` int
,`tags` text
,`type` varchar(64)
,`visibility` tinyint
);

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
  `payment_method` varchar(64) NOT NULL,
  `customer_id` varchar(64) DEFAULT NULL
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
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int NOT NULL,
  `msg_id` varchar(32) NOT NULL,
  `from_user` int NOT NULL,
  `content` text NOT NULL,
  `linked_content_id` int DEFAULT NULL,
  `conversation_id` int NOT NULL,
  `status` tinyint NOT NULL,
  `sent` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int NOT NULL,
  `type` tinyint NOT NULL,
  `from_user_id` int DEFAULT NULL,
  `to_user_id` int DEFAULT NULL,
  `notification_id` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `participant`
--

CREATE TABLE `participant` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `conversation_id` int NOT NULL,
  `status` tinyint NOT NULL,
  `joined_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `left_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Stand-in structure for view `posts3`
-- (See below for the actual view)
--
CREATE TABLE `posts3` (
`cnt` bigint
,`content` text
,`creator_id` int
,`ext` varchar(64)
,`image` varchar(5)
,`likes` bigint
,`owner` int
,`post_id` varchar(64)
,`tags` text
,`visibility` tinyint
);

-- --------------------------------------------------------

--
-- Table structure for table `post_comment`
--

CREATE TABLE `post_comment` (
  `feed_post_id` varchar(64) NOT NULL,
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
-- Stand-in structure for view `reacts`
-- (See below for the actual view)
--
CREATE TABLE `reacts` (
`likes` bigint
,`post_id` varchar(64)
);

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int NOT NULL,
  `name` varchar(32) NOT NULL,
  `status` varchar(32) NOT NULL,
  `created_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_user`
--

CREATE TABLE `room_user` (
  `user_id` int NOT NULL,
  `room_id` int NOT NULL,
  `status` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `r_message`
--

CREATE TABLE `r_message` (
  `id` int NOT NULL,
  `from_user` int NOT NULL,
  `to_user` int NOT NULL,
  `room_id` int NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `creator_id` int NOT NULL,
  `created` datetime NOT NULL,
  `visibility` tinyint NOT NULL DEFAULT '0',
  `tags` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `texts`
-- (See below for the actual view)
--
CREATE TABLE `texts` (
`content` text
,`created` datetime
,`creator_id` int
,`post_id` int
,`tags` text
,`visibility` tinyint
);

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `value` varchar(64) NOT NULL,
  `token_type` varchar(128) NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
-- Table structure for table `user_detail`
--

CREATE TABLE `user_detail` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `key_name` varchar(128) NOT NULL,
  `key_value` varchar(256) NOT NULL
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
-- Structure for view `comments`
--
DROP TABLE IF EXISTS `comments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`dug`@`localhost` SQL SECURITY DEFINER VIEW `comments`  AS  select `members91`.`post_comment`.`feed_post_id` AS `feed_post_id`,`members91`.`post`.`post_id` AS `post_id`,`members91`.`text`.`content` AS `content`,`members91`.`post`.`owner` AS `owner`,`members91`.`user`.`user_name` AS `user_name`,`members91`.`text`.`created` AS `created`,`members91`.`post`.`visibility` AS `visibility` from ((((`members91`.`post_comment` join `members91`.`post` on((`members91`.`post`.`post_id` = `members91`.`post_comment`.`post_id`))) join `members91`.`content_post` on((`members91`.`content_post`.`post_id` = `members91`.`post`.`id`))) join `members91`.`text` on((`members91`.`text`.`id` = `members91`.`content_post`.`content_id`))) join `members91`.`user` on((`members91`.`user`.`id` = `members91`.`post`.`owner`))) ;

-- --------------------------------------------------------

--
-- Structure for view `getposts`
--
DROP TABLE IF EXISTS `getposts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`dug`@`localhost` SQL SECURITY DEFINER VIEW `getposts`  AS  select 'text' AS `content_type`,`text`.`content` AS `content`,`text`.`created` AS `created`,`post`.`post_id` AS `post_id` from (((`feed_post` join `post` on((`post`.`post_id` = `feed_post`.`post_id`))) join `content_post` on((`content_post`.`post_id` = `post`.`id`))) join `text` on(((`text`.`id` = `content_post`.`content_id`) and (`content_post`.`content_type` = 'text')))) union select 'image' AS `content_type`,concat(`image`.`path`,'/',`image`.`link`) AS `content`,`image`.`created` AS `created`,`post`.`post_id` AS `post_id` from (((`feed_post` join `post` on((`post`.`post_id` = `feed_post`.`post_id`))) join `content_post` on((`content_post`.`post_id` = `post`.`id`))) join `image` on(((`image`.`id` = `content_post`.`content_id`) and (`content_post`.`content_type` = 'image')))) ;

-- --------------------------------------------------------

--
-- Structure for view `images`
--
DROP TABLE IF EXISTS `images`;

CREATE ALGORITHM=UNDEFINED DEFINER=`dug`@`localhost` SQL SECURITY DEFINER VIEW `images`  AS  select `members91`.`content_post`.`post_id` AS `post_id`,concat(`members91`.`image`.`path`,`members91`.`image`.`link`) AS `image`,`members91`.`image`.`type` AS `type`,`members91`.`image`.`creator_id` AS `creator_id`,`members91`.`image`.`visibility` AS `visibility`,`members91`.`image`.`created` AS `created`,`members91`.`image`.`tags` AS `tags` from (`members91`.`content_post` join `members91`.`image` on(((`members91`.`image`.`id` = `members91`.`content_post`.`content_id`) and (`members91`.`content_post`.`content_type` = 'image')))) ;

-- --------------------------------------------------------

--
-- Structure for view `posts3`
--
DROP TABLE IF EXISTS `posts3`;

CREATE ALGORITHM=UNDEFINED DEFINER=`dug`@`localhost` SQL SECURITY DEFINER VIEW `posts3`  AS  select `p`.`post_id` AS `post_id`,`p`.`owner` AS `owner`,`p`.`visibility` AS `visibility`,`p`.`content` AS `content`,`p`.`ext` AS `ext`,`p`.`image` AS `image`,`p`.`creator_id` AS `creator_id`,`p`.`tags` AS `tags`,`members91`.`reacts`.`likes` AS `likes`,`members91`.`count_comments`.`cnt` AS `cnt` from (((select `t1`.`post_id` AS `post_id`,`t1`.`owner` AS `owner`,`t1`.`visibility` AS `visibility`,`t1`.`content` AS `content`,`t1`.`ext` AS `ext`,`t1`.`image` AS `image`,`t1`.`creator_id` AS `creator_id`,`t1`.`tags` AS `tags` from (select `members91`.`post`.`post_id` AS `post_id`,`members91`.`post`.`owner` AS `owner`,`members91`.`images`.`visibility` AS `visibility`,`members91`.`images`.`image` AS `content`,`members91`.`images`.`type` AS `ext`,'image' AS `image`,`members91`.`images`.`creator_id` AS `creator_id`,`members91`.`images`.`tags` AS `tags` from (`members91`.`post` join `members91`.`images` on((`members91`.`images`.`post_id` = `members91`.`post`.`id`)))) `t1` union select `members91`.`post`.`post_id` AS `post_id`,`members91`.`post`.`owner` AS `owner`,`members91`.`texts`.`visibility` AS `visibility`,`members91`.`texts`.`content` AS `content`,'' AS `ext`,'' AS `image`,`members91`.`texts`.`creator_id` AS `creator_id`,`members91`.`texts`.`tags` AS `tags` from (`members91`.`post` join `members91`.`texts` on((`members91`.`texts`.`post_id` = `members91`.`post`.`id`)))) `p` join `members91`.`reacts` on((`members91`.`reacts`.`post_id` = `p`.`post_id`))) join `members91`.`count_comments` on((`members91`.`count_comments`.`feed_post_id` = `p`.`post_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `reacts`
--
DROP TABLE IF EXISTS `reacts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`dug`@`localhost` SQL SECURITY DEFINER VIEW `reacts`  AS  select `members91`.`reaction`.`post_id` AS `post_id`,count(0) AS `likes` from `members91`.`reaction` group by `members91`.`reaction`.`post_id` ;

-- --------------------------------------------------------

--
-- Structure for view `texts`
--
DROP TABLE IF EXISTS `texts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`dug`@`localhost` SQL SECURITY DEFINER VIEW `texts`  AS  select `members91`.`content_post`.`post_id` AS `post_id`,`members91`.`text`.`content` AS `content`,`members91`.`text`.`creator_id` AS `creator_id`,`members91`.`text`.`visibility` AS `visibility`,`members91`.`text`.`created` AS `created`,`members91`.`text`.`tags` AS `tags` from (`members91`.`content_post` join `members91`.`text` on(((`members91`.`text`.`id` = `members91`.`content_post`.`content_id`) and (`members91`.`content_post`.`content_type` = 'text')))) ;

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
  ADD UNIQUE KEY `idx_connection` (`from_id`,`to_id`),
  ADD KEY `idx_connection_type` (`connection_type`);

--
-- Indexes for table `connection_types`
--
ALTER TABLE `connection_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `idx_name` (`name`);

--
-- Indexes for table `content_post`
--
ALTER TABLE `content_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_content_id` (`content_id`) USING BTREE,
  ADD KEY `idx_content_type` (`content_type`);

--
-- Indexes for table `conversation`
--
ALTER TABLE `conversation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feed`
--
ALTER TABLE `feed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_owner_id` (`owner_id`);

--
-- Indexes for table `feed_post`
--
ALTER TABLE `feed_post`
  ADD KEY `idx_feed_id` (`feed_id`),
  ADD KEY `idx_post_id` (`post_id`);

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
  ADD UNIQUE KEY `idx_user_id` (`user_id`),
  ADD UNIQUE KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_member` (`member_id`);

--
-- Indexes for table `member_status`
--
ALTER TABLE `member_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `participant`
--
ALTER TABLE `participant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_user_conversation` (`conversation_id`,`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_owner` (`owner`);

--
-- Indexes for table `postcode`
--
ALTER TABLE `postcode`
  ADD PRIMARY KEY (`postcode`,`suburb`),
  ADD KEY `idx_lon` (`lon`),
  ADD KEY `idx_lat` (`lat`);

--
-- Indexes for table `post_comment`
--
ALTER TABLE `post_comment`
  ADD KEY `idx_feed_post_id` (`feed_post_id`),
  ADD KEY `idx_post_id` (`post_id`);

--
-- Indexes for table `profile_keys`
--
ALTER TABLE `profile_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reaction`
--
ALTER TABLE `reaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idxLpost_id` (`post_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_user`
--
ALTER TABLE `room_user`
  ADD UNIQUE KEY `idx_room_user` (`user_id`,`room_id`);

--
-- Indexes for table `r_message`
--
ALTER TABLE `r_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_to_user` (`to_user`),
  ADD KEY `idx_from_user` (`from_user`),
  ADD KEY `idx_room` (`room_id`);

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
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`user_name`);

--
-- Indexes for table `user_detail`
--
ALTER TABLE `user_detail`
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
-- AUTO_INCREMENT for table `conversation`
--
ALTER TABLE `conversation`
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
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `member_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `participant`
--
ALTER TABLE `participant`
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
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `r_message`
--
ALTER TABLE `r_message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `text`
--
ALTER TABLE `text`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` mediumint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_detail`
--
ALTER TABLE `user_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
