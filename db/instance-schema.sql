SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `action_counters` (
  `id` int(11) UNSIGNED NOT NULL,
  `action_name` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `counter` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `advertisements` (
  `pk_advertisement` bigint(20) UNSIGNED NOT NULL,
  `type_advertisement` smallint(2) UNSIGNED DEFAULT '1',
  `fk_content_categories` varchar(255) DEFAULT '',
  `path` varchar(150) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type_medida` varchar(50) DEFAULT NULL,
  `num_clic` int(10) DEFAULT '0',
  `num_clic_count` int(10) UNSIGNED DEFAULT '0',
  `num_view` int(10) UNSIGNED DEFAULT '0',
  `with_script` smallint(1) DEFAULT '0',
  `script` text,
  `overlap` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Flag esconder eventos flash',
  `timeout` int(4) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `albums` (
  `pk_album` bigint(20) UNSIGNED NOT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `agency` varchar(250) DEFAULT NULL,
  `cover_id` bigint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `albums_photos` (
  `pk_album` bigint(20) UNSIGNED NOT NULL,
  `pk_photo` bigint(20) UNSIGNED NOT NULL,
  `position` int(10) DEFAULT '1',
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `articles` (
  `pk_article` bigint(20) UNSIGNED NOT NULL,
  `summary` text,
  `img1` bigint(20) UNSIGNED DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `img1_footer` varchar(250) DEFAULT NULL,
  `img2` bigint(20) UNSIGNED DEFAULT NULL,
  `img2_footer` varchar(250) DEFAULT NULL,
  `agency` varchar(100) DEFAULT NULL,
  `fk_video` bigint(20) UNSIGNED DEFAULT NULL,
  `fk_video2` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'video interior',
  `footer_video2` varchar(150) DEFAULT NULL,
  `footer_video1` varchar(150) DEFAULT NULL,
  `title_int` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `attachments` (
  `pk_attachment` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `path` varchar(200) NOT NULL,
  `category` int(10) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `books` (
  `pk_book` bigint(20) UNSIGNED NOT NULL,
  `author` varchar(250) DEFAULT NULL,
  `cover_id` bigint(255) DEFAULT NULL,
  `editorial` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
  `id` bigint(32) UNSIGNED NOT NULL,
  `content_id` bigint(20) UNSIGNED DEFAULT NULL,
  `author` varchar(200) DEFAULT NULL,
  `author_email` varchar(200) DEFAULT '',
  `author_url` varchar(200) DEFAULT '',
  `author_ip` varchar(100) DEFAULT '',
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `body` text,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `agent` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `parent_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `content_type_referenced` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `commentsmeta` (
  `fk_content` bigint(20) UNSIGNED NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contentmeta` (
  `fk_content` bigint(20) UNSIGNED NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contents` (
  `pk_content` bigint(20) UNSIGNED NOT NULL,
  `fk_content_type` int(10) UNSIGNED NOT NULL,
  `content_type_name` varchar(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `body` text NOT NULL,
  `metadata` varchar(255) DEFAULT NULL,
  `starttime` datetime DEFAULT CURRENT_TIMESTAMP,
  `endtime` datetime DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `changed` datetime DEFAULT CURRENT_TIMESTAMP,
  `content_status` int(10) UNSIGNED DEFAULT '0',
  `fk_author` int(10) UNSIGNED DEFAULT NULL,
  `fk_publisher` int(10) UNSIGNED DEFAULT NULL,
  `fk_user_last_editor` int(10) UNSIGNED DEFAULT NULL,
  `position` int(10) UNSIGNED DEFAULT '100',
  `frontpage` tinyint(1) DEFAULT '1',
  `in_litter` tinyint(1) DEFAULT '0',
  `in_home` smallint(1) DEFAULT '0',
  `slug` varchar(255) DEFAULT NULL,
  `available` smallint(1) DEFAULT '1',
  `params` text,
  `category_name` varchar(255) NOT NULL,
  `favorite` tinyint(1) DEFAULT NULL,
  `urn_source` varchar(255) DEFAULT NULL,
  `with_comment` smallint(6) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contents_categories` (
  `pk_fk_content` bigint(20) UNSIGNED NOT NULL,
  `pk_fk_content_category` int(10) UNSIGNED NOT NULL,
  `catName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `content_categories` (
  `pk_content_category` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `inmenu` int(10) DEFAULT '0',
  `posmenu` int(10) DEFAULT '10',
  `internal_category` smallint(1) NOT NULL DEFAULT '0' COMMENT 'equal content_type & global=0 ',
  `fk_content_category` int(10) DEFAULT '0',
  `params` text,
  `logo_path` varchar(200) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `content_positions` (
  `pk_fk_content` bigint(20) UNSIGNED NOT NULL,
  `fk_category` int(10) UNSIGNED NOT NULL,
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `placeholder` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `params` text CHARACTER SET latin1,
  `content_type` varchar(45) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `content_views` (
  `pk_fk_content` bigint(20) UNSIGNED NOT NULL,
  `views` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `frontpages` (
  `pk_frontpage` bigint(20) UNSIGNED NOT NULL,
  `date` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `version` bigint(20) DEFAULT NULL,
  `content_positions` longtext NOT NULL,
  `promoted` tinyint(1) DEFAULT NULL,
  `day_frontpage` tinyint(1) DEFAULT NULL,
  `params` text NOT NULL COMMENT 'serialized params'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `kioskos` (
  `pk_kiosko` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-item, 1-subscription',
  `price` decimal(10,0) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `letters` (
  `pk_letter` bigint(20) UNSIGNED NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `menues` (
  `pk_menu` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `position` varchar(50) DEFAULT NULL,
  `params` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `menu_items` (
  `pk_item` int(10) UNSIGNED NOT NULL,
  `pk_menu` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT '''category'',''external'',''static'', internal''',
  `position` int(11) NOT NULL,
  `pk_father` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `newsletter_archive` (
  `pk_newsletter` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `data` text,
  `html` mediumtext,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opinions` (
  `pk_opinion` bigint(20) UNSIGNED NOT NULL,
  `fk_content_categories` int(10) UNSIGNED DEFAULT '7',
  `fk_author` int(10) DEFAULT NULL,
  `fk_author_img` int(10) DEFAULT NULL,
  `type_opinion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_id` varchar(50) NOT NULL,
  `payment_status` varchar(150) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `params` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pclave` (
  `id` int(8) UNSIGNED NOT NULL,
  `pclave` varchar(60) NOT NULL,
  `value` varchar(240) DEFAULT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'intsearch'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pc_users` (
  `pk_pc_user` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `subscription` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `photos` (
  `pk_photo` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `path_file` varchar(150) NOT NULL,
  `size` float DEFAULT NULL,
  `width` int(10) DEFAULT NULL,
  `height` int(10) DEFAULT NULL,
  `nameCat` varchar(250) DEFAULT '1',
  `author_name` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `polls` (
  `pk_poll` bigint(20) UNSIGNED NOT NULL,
  `total_votes` int(11) NOT NULL DEFAULT '0',
  `used_ips` text,
  `subtitle` varchar(150) DEFAULT NULL,
  `visualization` smallint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `poll_items` (
  `pk_item` int(10) UNSIGNED NOT NULL,
  `fk_pk_poll` bigint(20) UNSIGNED NOT NULL,
  `item` varchar(255) NOT NULL,
  `metadata` varchar(250) DEFAULT NULL,
  `votes` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ratings` (
  `pk_rating` bigint(20) UNSIGNED NOT NULL,
  `total_votes` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `total_value` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `ips_count_rating` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `related_contents` (
  `pk_content1` bigint(20) UNSIGNED NOT NULL,
  `pk_content2` bigint(20) UNSIGNED NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `text` varchar(50) DEFAULT NULL,
  `position` int(10) NOT NULL DEFAULT '0',
  `posinterior` int(2) NOT NULL DEFAULT '0',
  `verportada` int(2) NOT NULL DEFAULT '0',
  `verinterior` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `settings` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `specials` (
  `pk_special` bigint(20) UNSIGNED NOT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `pdf_path` varchar(250) DEFAULT '0',
  `img1` varchar(255) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `special_contents` (
  `fk_content` bigint(20) UNSIGNED NOT NULL,
  `fk_special` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `position` int(10) DEFAULT '10',
  `visible` smallint(1) DEFAULT '1',
  `type_content` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `static_pages` (
  `pk_static_page` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `translation_ids` (
  `pk_content_old` bigint(10) NOT NULL,
  `pk_content` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `slug` varchar(200) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `usermeta` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `meta_key` varchar(255) NOT NULL DEFAULT '',
  `meta_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `sessionexpire` tinyint(2) UNSIGNED NOT NULL DEFAULT '15',
  `url` varchar(255) NOT NULL DEFAULT '',
  `bio` text NOT NULL,
  `avatar_img_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-backend, 1-frontend',
  `deposit` decimal(10,0) NOT NULL DEFAULT '0',
  `token` varchar(50) DEFAULT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 activated - 0 deactivated',
  `fk_user_group` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_content_categories` (
  `pk_fk_user` int(10) UNSIGNED NOT NULL,
  `pk_fk_content_category` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_groups` (
  `pk_user_group` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_groups_privileges` (
  `pk_fk_user_group` int(10) UNSIGNED NOT NULL,
  `pk_fk_privilege` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_notification` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `notification_id` int(10) UNSIGNED NOT NULL,
  `read_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `videos` (
  `pk_video` bigint(20) UNSIGNED NOT NULL,
  `video_url` varchar(255) NOT NULL,
  `information` text,
  `author_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `votes` (
  `pk_vote` bigint(20) UNSIGNED NOT NULL,
  `value_pos` smallint(4) NOT NULL DEFAULT '0',
  `value_neg` smallint(4) NOT NULL DEFAULT '0',
  `ips_count_vote` text,
  `karma` int(10) UNSIGNED DEFAULT '100'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `widgets` (
  `pk_widget` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `renderlet` varchar(50) DEFAULT 'html'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `action_counters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_name` (`action_name`);

ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`pk_advertisement`),
  ADD KEY `type_advertisement` (`type_advertisement`),
  ADD KEY `fk_content_categories` (`fk_content_categories`);

ALTER TABLE `albums`
  ADD PRIMARY KEY (`pk_album`),
  ADD UNIQUE KEY `pk_album` (`pk_album`),
  ADD KEY `pk_album_2` (`pk_album`);

ALTER TABLE `albums_photos`
  ADD KEY `index_album_photo` (`pk_album`,`pk_photo`),
  ADD KEY `pk_album` (`pk_album`),
  ADD KEY `pk_photo` (`pk_photo`);

ALTER TABLE `articles`
  ADD PRIMARY KEY (`pk_article`);

ALTER TABLE `attachments`
  ADD PRIMARY KEY (`pk_attachment`);

ALTER TABLE `books`
  ADD PRIMARY KEY (`pk_book`);

ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_content_id` (`content_id`),
  ADD KEY `comment_parent_id` (`parent_id`);

ALTER TABLE `commentsmeta`
  ADD PRIMARY KEY (`fk_content`,`meta_name`),
  ADD KEY `fk_content` (`fk_content`);

ALTER TABLE `contentmeta`
  ADD PRIMARY KEY (`fk_content`,`meta_name`),
  ADD KEY `fk_content` (`fk_content`);

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

ALTER TABLE `contents_categories`
  ADD PRIMARY KEY (`pk_fk_content`,`pk_fk_content_category`),
  ADD KEY `pk_fk_content_category` (`pk_fk_content_category`),
  ADD KEY `content_categories_catname` (`catName`),
  ADD KEY `pk_fk_content` (`pk_fk_content`);

ALTER TABLE `content_categories`
  ADD PRIMARY KEY (`pk_content_category`);

ALTER TABLE `content_positions`
  ADD PRIMARY KEY (`pk_fk_content`,`fk_category`,`position`,`placeholder`),
  ADD KEY `content_position_pk_content` (`pk_fk_content`),
  ADD KEY `content_position_fk_category` (`fk_category`);

ALTER TABLE `content_views`
  ADD PRIMARY KEY (`pk_fk_content`);

ALTER TABLE `frontpages`
  ADD PRIMARY KEY (`pk_frontpage`);

ALTER TABLE `kioskos`
  ADD PRIMARY KEY (`pk_kiosko`);

ALTER TABLE `letters`
  ADD PRIMARY KEY (`pk_letter`);

ALTER TABLE `menues`
  ADD PRIMARY KEY (`pk_menu`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `position` (`position`);

ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`pk_item`,`pk_menu`),
  ADD KEY `pk_item` (`pk_item`),
  ADD KEY `pk_menu` (`pk_menu`);

ALTER TABLE `newsletter_archive`
  ADD PRIMARY KEY (`pk_newsletter`);

ALTER TABLE `opinions`
  ADD PRIMARY KEY (`pk_opinion`),
  ADD KEY `type_opinion` (`type_opinion`),
  ADD KEY `fk_author` (`fk_author`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pclave`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pc_users`
  ADD PRIMARY KEY (`pk_pc_user`);

ALTER TABLE `photos`
  ADD PRIMARY KEY (`pk_photo`);

ALTER TABLE `polls`
  ADD PRIMARY KEY (`pk_poll`);

ALTER TABLE `poll_items`
  ADD PRIMARY KEY (`pk_item`),
  ADD KEY `fk_pk_poll` (`fk_pk_poll`);

ALTER TABLE `ratings`
  ADD PRIMARY KEY (`pk_rating`);

ALTER TABLE `related_contents`
  ADD KEY `pk_content1` (`pk_content1`),
  ADD KEY `verportada` (`verportada`),
  ADD KEY `pk_content2` (`pk_content2`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`name`);

ALTER TABLE `specials`
  ADD PRIMARY KEY (`pk_special`);

ALTER TABLE `special_contents`
  ADD PRIMARY KEY (`fk_content`,`fk_special`),
  ADD KEY `fk_content` (`fk_content`),
  ADD KEY `fk_special` (`fk_special`);

ALTER TABLE `static_pages`
  ADD PRIMARY KEY (`pk_static_page`);

ALTER TABLE `translation_ids`
  ADD PRIMARY KEY (`pk_content_old`,`pk_content`,`type`),
  ADD KEY `slug` (`slug`),
  ADD KEY `pk_content` (`pk_content`);

ALTER TABLE `usermeta`
  ADD PRIMARY KEY (`user_id`,`meta_key`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `meta_key` (`meta_key`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_username` (`username`),
  ADD KEY `user_email` (`email`);

ALTER TABLE `users_content_categories`
  ADD PRIMARY KEY (`pk_fk_user`,`pk_fk_content_category`);

ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`pk_user_group`);

ALTER TABLE `user_groups_privileges`
  ADD PRIMARY KEY (`pk_fk_user_group`,`pk_fk_privilege`),
  ADD KEY `pk_fk_user_group` (`pk_fk_user_group`);

ALTER TABLE `user_notification`
  ADD PRIMARY KEY (`user_id`,`notification_id`);

ALTER TABLE `videos`
  ADD PRIMARY KEY (`pk_video`);

ALTER TABLE `votes`
  ADD PRIMARY KEY (`pk_vote`);

ALTER TABLE `widgets`
  ADD PRIMARY KEY (`pk_widget`);
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
