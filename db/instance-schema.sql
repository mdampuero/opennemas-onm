-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Xerado en: 07 de Abr de 2016 ás 11:30
-- Versión do servidor: 5.6.28-0ubuntu0.14.04.1-log
-- Versión do PHP: 5.6.18-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `basic`
--

-- --------------------------------------------------------

--
-- Estrutura da táboa `action_counters`
--

CREATE TABLE IF NOT EXISTS `action_counters` (
  `id` int(11) unsigned NOT NULL,
  `action_name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `counter` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `advertisements`
--

CREATE TABLE IF NOT EXISTS `advertisements` (
  `pk_advertisement` bigint(20) unsigned NOT NULL,
  `type_advertisement` smallint(2) unsigned DEFAULT '1',
  `fk_content_categories` varchar(255) DEFAULT '',
  `path` varchar(150) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type_medida` varchar(50) DEFAULT NULL,
  `num_clic` int(10) DEFAULT '0',
  `num_clic_count` int(10) unsigned DEFAULT '0',
  `num_view` int(10) unsigned DEFAULT '0',
  `with_script` smallint(1) DEFAULT '0',
  `script` text,
  `overlap` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Flag esconder eventos flash',
  `timeout` int(4) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `albums`
--

CREATE TABLE IF NOT EXISTS `albums` (
  `pk_album` bigint(20) unsigned NOT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `agency` varchar(250) DEFAULT NULL,
  `cover_id` bigint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `albums_photos`
--

CREATE TABLE IF NOT EXISTS `albums_photos` (
  `pk_album` bigint(20) unsigned NOT NULL,
  `pk_photo` bigint(20) unsigned NOT NULL,
  `position` int(10) DEFAULT '1',
  `description` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `pk_article` bigint(20) unsigned NOT NULL,
  `summary` text,
  `img1` bigint(20) unsigned DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `img1_footer` varchar(250) DEFAULT NULL,
  `img2` bigint(20) unsigned DEFAULT NULL,
  `img2_footer` varchar(250) DEFAULT NULL,
  `agency` varchar(100) DEFAULT NULL,
  `fk_video` bigint(20) unsigned DEFAULT NULL,
  `fk_video2` bigint(20) unsigned DEFAULT NULL COMMENT 'video interior',
  `footer_video2` varchar(150) DEFAULT NULL,
  `footer_video1` varchar(150) DEFAULT NULL,
  `title_int` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `attachments`
--

CREATE TABLE IF NOT EXISTS `attachments` (
  `pk_attachment` bigint(20) unsigned NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `path` varchar(200) NOT NULL,
  `category` int(10) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `pk_book` bigint(20) unsigned NOT NULL,
  `author` varchar(250) DEFAULT NULL,
  `cover_id` bigint(255) DEFAULT NULL,
  `editorial` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint(32) unsigned NOT NULL,
  `content_id` bigint(32) unsigned NOT NULL DEFAULT '0',
  `author` varchar(200) DEFAULT NULL,
  `author_email` varchar(200) DEFAULT '',
  `author_url` varchar(200) DEFAULT '',
  `author_ip` varchar(100) DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `body` text,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `agent` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `parent_id` bigint(32) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `content_type_referenced` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `commentsmeta`
--

CREATE TABLE IF NOT EXISTS `commentsmeta` (
  `fk_content` bigint(32) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `contentmeta`
--

CREATE TABLE IF NOT EXISTS `contentmeta` (
  `fk_content` bigint(20) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `contents`
--

CREATE TABLE IF NOT EXISTS `contents` (
  `pk_content` bigint(20) unsigned NOT NULL,
  `fk_content_type` int(10) unsigned NOT NULL,
  `content_type_name` varchar(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `body` text NOT NULL,
  `metadata` varchar(255) DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `changed` datetime DEFAULT NULL,
  `content_status` int(10) unsigned DEFAULT '0',
  `fk_author` int(10) unsigned DEFAULT NULL COMMENT 'Clave foranea de user',
  `fk_publisher` int(10) unsigned DEFAULT NULL COMMENT 'Clave foranea de user',
  `fk_user_last_editor` int(10) unsigned DEFAULT NULL COMMENT 'Clave foranea de user',
  `position` int(10) unsigned DEFAULT '100',
  `frontpage` tinyint(1) DEFAULT '1',
  `in_litter` tinyint(1) DEFAULT '0' COMMENT '0publicado 1papelera',
  `in_home` smallint(1) DEFAULT '0',
  `home_pos` int(10) DEFAULT '100' COMMENT '10',
  `slug` varchar(255) DEFAULT NULL,
  `available` smallint(1) DEFAULT '1',
  `params` text,
  `category_name` varchar(255) NOT NULL,
  `favorite` tinyint(1) DEFAULT NULL,
  `urn_source` varchar(255) DEFAULT NULL,
  `with_comment` smallint(6) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `contents_categories`
--

CREATE TABLE IF NOT EXISTS `contents_categories` (
  `pk_fk_content` bigint(20) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  `catName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `content_categories`
--

CREATE TABLE IF NOT EXISTS `content_categories` (
  `pk_content_category` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `inmenu` int(10) DEFAULT '0',
  `posmenu` int(10) DEFAULT '10',
  `internal_category` smallint(1) NOT NULL DEFAULT '0' COMMENT 'equal content_type & global=0 ',
  `fk_content_category` int(10) DEFAULT '0',
  `params` text,
  `logo_path` varchar(200) DEFAULT NULL,
  `color` varchar(10) DEFAULT '#638F38'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `content_positions`
--

CREATE TABLE IF NOT EXISTS `content_positions` (
  `pk_fk_content` bigint(20) NOT NULL,
  `fk_category` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `placeholder` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `params` text CHARACTER SET latin1,
  `content_type` varchar(45) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `content_views`
--

CREATE TABLE IF NOT EXISTS `content_views` (
  `pk_fk_content` bigint(20) unsigned NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da táboa `frontpages`
--

CREATE TABLE IF NOT EXISTS `frontpages` (
  `pk_frontpage` bigint(20) NOT NULL,
  `date` int(11) NOT NULL COMMENT 'date as 20110720',
  `category` int(11) NOT NULL COMMENT 'category',
  `version` bigint(20) DEFAULT NULL,
  `content_positions` text NOT NULL COMMENT 'serialized id of contents',
  `promoted` tinyint(1) DEFAULT NULL,
  `day_frontpage` tinyint(1) DEFAULT NULL,
  `params` text NOT NULL COMMENT 'serialized params'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `kioskos`
--

CREATE TABLE IF NOT EXISTS `kioskos` (
  `pk_kiosko` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-item, 1-subscription',
  `price` decimal(10,0) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `letters`
--

CREATE TABLE IF NOT EXISTS `letters` (
  `pk_letter` int(10) unsigned NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `menues`
--

CREATE TABLE IF NOT EXISTS `menues` (
  `pk_menu` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `position` varchar(50) DEFAULT NULL,
  `params` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `menu_items`
--

CREATE TABLE IF NOT EXISTS `menu_items` (
  `pk_item` int(11) NOT NULL,
  `pk_menu` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT '''category'',''external'',''static'', internal''',
  `position` int(11) NOT NULL,
  `pk_father` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `newsletter_archive`
--

CREATE TABLE IF NOT EXISTS `newsletter_archive` (
  `pk_newsletter` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `data` text,
  `html` mediumtext,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL,
  `sent` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `opinions`
--

CREATE TABLE IF NOT EXISTS `opinions` (
  `pk_opinion` bigint(20) unsigned NOT NULL,
  `fk_content_categories` int(10) unsigned DEFAULT '7',
  `fk_author` int(10) DEFAULT NULL,
  `fk_author_img` int(10) DEFAULT NULL,
  `type_opinion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `payment_id` varchar(50) NOT NULL,
  `payment_status` varchar(150) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `pclave`
--

CREATE TABLE IF NOT EXISTS `pclave` (
  `id` int(8) unsigned NOT NULL,
  `pclave` varchar(60) NOT NULL,
  `value` varchar(240) DEFAULT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'intsearch'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `pc_users`
--

CREATE TABLE IF NOT EXISTS `pc_users` (
  `pk_pc_user` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `subscription` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `pk_photo` bigint(20) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `path_file` varchar(150) NOT NULL,
  `size` float DEFAULT NULL,
  `width` int(10) DEFAULT NULL,
  `height` int(10) DEFAULT NULL,
  `nameCat` varchar(250) DEFAULT '1',
  `author_name` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `polls`
--

CREATE TABLE IF NOT EXISTS `polls` (
  `pk_poll` bigint(20) unsigned NOT NULL,
  `total_votes` int(11) NOT NULL DEFAULT '0',
  `used_ips` text,
  `subtitle` varchar(150) DEFAULT NULL,
  `visualization` smallint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `poll_items`
--

CREATE TABLE IF NOT EXISTS `poll_items` (
  `pk_item` int(10) unsigned NOT NULL,
  `fk_pk_poll` bigint(20) unsigned NOT NULL,
  `item` varchar(255) NOT NULL,
  `metadata` varchar(250) DEFAULT NULL,
  `votes` int(10) unsigned DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `pk_rating` bigint(20) unsigned NOT NULL,
  `total_votes` smallint(5) unsigned NOT NULL DEFAULT '0',
  `total_value` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ips_count_rating` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `related_contents`
--

CREATE TABLE IF NOT EXISTS `related_contents` (
  `pk_content1` bigint(20) unsigned NOT NULL,
  `pk_content2` bigint(20) unsigned NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `text` varchar(50) DEFAULT NULL,
  `position` int(10) NOT NULL DEFAULT '0',
  `posinterior` int(2) NOT NULL DEFAULT '0',
  `verportada` int(2) NOT NULL DEFAULT '0',
  `verinterior` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `specials`
--

CREATE TABLE IF NOT EXISTS `specials` (
  `pk_special` int(10) unsigned NOT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `pdf_path` varchar(250) DEFAULT '0',
  `img1` varchar(255) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `special_contents`
--

CREATE TABLE IF NOT EXISTS `special_contents` (
  `fk_content` varchar(250) NOT NULL,
  `fk_special` int(10) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `position` int(10) DEFAULT '10',
  `visible` smallint(1) DEFAULT '1',
  `type_content` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `static_pages`
--

CREATE TABLE IF NOT EXISTS `static_pages` (
  `pk_static_page` bigint(20) NOT NULL COMMENT 'BIGINT(20)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `translation_ids`
--

CREATE TABLE IF NOT EXISTS `translation_ids` (
  `pk_content_old` bigint(10) NOT NULL,
  `pk_content` bigint(10) NOT NULL,
  `type` varchar(100) NOT NULL,
  `slug` varchar(200) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `usermeta`
--

CREATE TABLE IF NOT EXISTS `usermeta` (
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) NOT NULL DEFAULT '',
  `meta_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `sessionexpire` tinyint(2) unsigned NOT NULL DEFAULT '15',
  `url` varchar(255) NOT NULL DEFAULT '',
  `bio` text NOT NULL,
  `avatar_img_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-backend, 1-frontend',
  `deposit` decimal(10,0) NOT NULL DEFAULT '0',
  `token` varchar(50) DEFAULT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 activated - 0 deactivated',
  `fk_user_group` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `users_content_categories`
--

CREATE TABLE IF NOT EXISTS `users_content_categories` (
  `pk_fk_user` int(10) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `user_groups_privileges`
--

CREATE TABLE IF NOT EXISTS `user_groups_privileges` (
  `pk_fk_user_group` int(10) unsigned NOT NULL,
  `pk_fk_privilege` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `user_notification`
--

CREATE TABLE IF NOT EXISTS `user_notification` (
  `user_id` int(10) unsigned NOT NULL,
  `notification_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da táboa `videos`
--

CREATE TABLE IF NOT EXISTS `videos` (
  `pk_video` bigint(20) unsigned NOT NULL,
  `video_url` varchar(255) NOT NULL,
  `information` text,
  `author_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `pk_vote` bigint(20) NOT NULL,
  `value_pos` smallint(4) NOT NULL DEFAULT '0',
  `value_neg` smallint(4) NOT NULL DEFAULT '0',
  `ips_count_vote` text,
  `karma` int(10) unsigned DEFAULT '100'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `pk_widget` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `renderlet` varchar(50) DEFAULT 'html'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action_counters`
--
ALTER TABLE `action_counters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_name` (`action_name`);

--
-- Indexes for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`pk_advertisement`),
  ADD KEY `type_advertisement` (`type_advertisement`),
  ADD KEY `fk_content_categories` (`fk_content_categories`);

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`pk_album`),
  ADD UNIQUE KEY `pk_album` (`pk_album`),
  ADD KEY `pk_album_2` (`pk_album`);

--
-- Indexes for table `albums_photos`
--
ALTER TABLE `albums_photos`
  ADD KEY `pk_album_2` (`pk_album`,`pk_photo`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`pk_article`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`pk_attachment`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_content_id` (`content_id`),
  ADD KEY `comment_status_date` (`status`,`date`),
  ADD KEY `comment_parent_id` (`parent_id`),
  ADD KEY `comment_date` (`date`);

--
-- Indexes for table `commentsmeta`
--
ALTER TABLE `commentsmeta`
  ADD PRIMARY KEY (`fk_content`,`meta_name`),
  ADD KEY `fk_content` (`fk_content`);

--
-- Indexes for table `contentmeta`
--
ALTER TABLE `contentmeta`
  ADD PRIMARY KEY (`fk_content`,`meta_name`),
  ADD KEY `fk_content` (`fk_content`);

--
-- Indexes for table `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`pk_content`),
  ADD KEY `fk_content_type` (`fk_content_type`),
  ADD KEY `in_litter` (`in_litter`),
  ADD KEY `content_status` (`content_status`),
  ADD KEY `in_home` (`in_home`),
  ADD KEY `frontpage` (`frontpage`),
  ADD KEY `available` (`available`),
  ADD KEY `starttime` (`starttime`,`endtime`),
  ADD KEY `created` (`created`),
  ADD KEY `urn_source` (`urn_source`),
  ADD KEY `metadata` (`metadata`);

--
-- Indexes for table `contents_categories`
--
ALTER TABLE `contents_categories`
  ADD PRIMARY KEY (`pk_fk_content`,`pk_fk_content_category`),
  ADD KEY `pk_fk_content_category` (`pk_fk_content_category`),
  ADD KEY `catName` (`catName`);

--
-- Indexes for table `content_categories`
--
ALTER TABLE `content_categories`
  ADD PRIMARY KEY (`pk_content_category`);

--
-- Indexes for table `content_positions`
--
ALTER TABLE `content_positions`
  ADD PRIMARY KEY (`pk_fk_content`,`fk_category`,`position`,`placeholder`);

--
-- Indexes for table `content_views`
--
ALTER TABLE `content_views`
  ADD PRIMARY KEY (`pk_fk_content`);

--
-- Indexes for table `frontpages`
--
ALTER TABLE `frontpages`
  ADD PRIMARY KEY (`date`,`category`);

--
-- Indexes for table `kioskos`
--
ALTER TABLE `kioskos`
  ADD PRIMARY KEY (`pk_kiosko`);

--
-- Indexes for table `letters`
--
ALTER TABLE `letters`
  ADD PRIMARY KEY (`pk_letter`);

--
-- Indexes for table `menues`
--
ALTER TABLE `menues`
  ADD PRIMARY KEY (`pk_menu`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `position` (`position`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`pk_item`,`pk_menu`),
  ADD KEY `pk_item` (`pk_item`),
  ADD KEY `pk_menu` (`pk_menu`);

--
-- Indexes for table `newsletter_archive`
--
ALTER TABLE `newsletter_archive`
  ADD PRIMARY KEY (`pk_newsletter`);

--
-- Indexes for table `opinions`
--
ALTER TABLE `opinions`
  ADD PRIMARY KEY (`pk_opinion`),
  ADD KEY `type_opinion` (`type_opinion`),
  ADD KEY `fk_author` (`fk_author`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pclave`
--
ALTER TABLE `pclave`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pc_users`
--
ALTER TABLE `pc_users`
  ADD PRIMARY KEY (`pk_pc_user`);

--
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`pk_photo`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`pk_poll`);

--
-- Indexes for table `poll_items`
--
ALTER TABLE `poll_items`
  ADD PRIMARY KEY (`pk_item`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`pk_rating`);

--
-- Indexes for table `related_contents`
--
ALTER TABLE `related_contents`
  ADD KEY `pk_content1` (`pk_content1`),
  ADD KEY `verportada` (`verportada`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `specials`
--
ALTER TABLE `specials`
  ADD PRIMARY KEY (`pk_special`);

--
-- Indexes for table `static_pages`
--
ALTER TABLE `static_pages`
  ADD PRIMARY KEY (`pk_static_page`);

--
-- Indexes for table `translation_ids`
--
ALTER TABLE `translation_ids`
  ADD PRIMARY KEY (`pk_content_old`,`pk_content`,`type`),
  ADD KEY `slug` (`slug`);

--
-- Indexes for table `usermeta`
--
ALTER TABLE `usermeta`
  ADD PRIMARY KEY (`user_id`,`meta_key`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `meta_key` (`meta_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_username` (`username`),
  ADD KEY `user_email` (`email`);

--
-- Indexes for table `users_content_categories`
--
ALTER TABLE `users_content_categories`
  ADD PRIMARY KEY (`pk_fk_user`,`pk_fk_content_category`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`pk_user_group`);

--
-- Indexes for table `user_groups_privileges`
--
ALTER TABLE `user_groups_privileges`
  ADD PRIMARY KEY (`pk_fk_user_group`,`pk_fk_privilege`);

--
-- Indexes for table `user_notification`
--
ALTER TABLE `user_notification`
  ADD PRIMARY KEY (`user_id`,`notification_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`pk_video`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`pk_vote`);

--
-- Indexes for table `widgets`
--
ALTER TABLE `widgets`
  ADD PRIMARY KEY (`pk_widget`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_counters`
--
ALTER TABLE `action_counters`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `advertisements`
--
ALTER TABLE `advertisements`
  MODIFY `pk_advertisement` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `pk_album` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `pk_article` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `pk_attachment` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(32) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `contents`
--
ALTER TABLE `contents`
  MODIFY `pk_content` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `content_categories`
--
ALTER TABLE `content_categories`
  MODIFY `pk_content_category` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `kioskos`
--
ALTER TABLE `kioskos`
  MODIFY `pk_kiosko` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `letters`
--
ALTER TABLE `letters`
  MODIFY `pk_letter` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `menues`
--
ALTER TABLE `menues`
  MODIFY `pk_menu` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `pk_item` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `newsletter_archive`
--
ALTER TABLE `newsletter_archive`
  MODIFY `pk_newsletter` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `opinions`
--
ALTER TABLE `opinions`
  MODIFY `pk_opinion` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pclave`
--
ALTER TABLE `pclave`
  MODIFY `id` int(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pc_users`
--
ALTER TABLE `pc_users`
  MODIFY `pk_pc_user` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `pk_photo` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `pk_poll` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `poll_items`
--
ALTER TABLE `poll_items`
  MODIFY `pk_item` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `pk_rating` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `static_pages`
--
ALTER TABLE `static_pages`
  MODIFY `pk_static_page` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'BIGINT(20)';
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `pk_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `pk_video` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `pk_vote` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `widgets`
--
ALTER TABLE `widgets`
  MODIFY `pk_widget` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
