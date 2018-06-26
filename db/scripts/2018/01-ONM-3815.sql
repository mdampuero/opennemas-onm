CREATE TABLE `tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `language_id` varchar(5) NOT NULL,
  `slug` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contents_tags` (
  `content_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD INDEX `language_id` (`language_id`);
--  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `contents_tags`
  ADD PRIMARY KEY (`tag_id`,`content_id`),
  ADD CONSTRAINT `contents_tags_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `content_tags_content_id` FOREIGN KEY (`content_id`) REFERENCES `contents` (`pk_content`) ON DELETE CASCADE ON UPDATE CASCADE;
