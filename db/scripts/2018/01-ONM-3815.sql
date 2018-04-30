CREATE TABLE `tags` (
  `pk_tag` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `pk_language` varchar(5) NOT NULL,
  `slug` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contents_tags` (
  `pk_content` bigint(20) UNSIGNED NOT NULL,
  `pk_tag` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `tags`
  ADD PRIMARY KEY (`pk_tag`),
  MODIFY `pk_tag` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD INDEX `pk_language` (`pk_language`);
--  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `contents_tags`
  ADD PRIMARY KEY (`pk_tag`,`pk_content`),
  ADD CONSTRAINT `tag_id_tag_id` FOREIGN KEY (`pk_tag`) REFERENCES `tags` (`pk_tag`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `content_id_content_id` FOREIGN KEY (`pk_content`) REFERENCES `contents` (`pk_content`) ON DELETE CASCADE ON UPDATE CASCADE;
