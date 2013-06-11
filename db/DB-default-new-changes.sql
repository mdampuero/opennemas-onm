-- This file contains all the changes that has to be applied
-- in the default instance database.

-- Move all the applied changes into the DB-default-applied-changes.sql
-- file whenever it's possible

-- Please check rigth sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)

-- 11-06-2013
ALTER TABLE `users` CHANGE `login` `username` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `authorize` `activated` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1 activated - 0 deactivated';
ALTER TABLE `users` ADD `url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' AFTER `sessionexpire`;
ALTER TABLE `users` ADD `bio` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' AFTER `url`;
ALTER TABLE `users` ADD `avatar_img_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT  '' AFTER `bio`;

-- 05-30-2013
DROP TABLE content_types;

-- 05-27-2013
ALTER TABLE  `comments`
    DROP    `sexo`,
    DROP    `ciudad`,
    CHANGE  `pk_comment`  `id` bigint( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE  `fk_content`  `content_id` bigint(20) unsigned NOT NULL DEFAULT '0' AFTER `id`,
    CHANGE  `email`       `author_email` varchar(100) NOT NULL DEFAULT '' AFTER `author`,
    CHANGE  `ip`          `author_ip` varchar(100) NOT NULL DEFAULT '' AFTER `author_email`,
    ADD     `author_url` varchar(200) NOT NULL DEFAULT '' AFTER  `author_email`,
    CHANGE  `published`  `date` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' AFTER `author_ip`,
    ADD     `status`     VARCHAR( 20 ) NOT NULL DEFAULT  'pending' AFTER  `body` ,
    ADD     `agent`      VARCHAR( 255 ) NOT NULL DEFAULT  '' AFTER  `status` ,
    ADD     `type`       VARCHAR( 20 ) NOT NULL DEFAULT  '' AFTER  `agent` ,
    ADD     `parent_id`  BIGINT( 20 ) NOT NULL DEFAULT  '0' AFTER  `type` ,
    ADD     `user_id`    INT( 10 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `comments`
    ADD INDEX  `comment_content_id`  (  `content_id` ),
    ADD INDEX  `comment_status_date` (  `status` ,  `date` ),
    ADD INDEX  `comment_parent_id` (  `parent_id` ),
    ADD INDEX  `comment_date` (  `date` ),
    DROP INDEX `fk_content`;

-- update comment date
UPDATE  `comments` SET  `comments`.`date` = (SELECT `created` FROM  `contents` WHERE `comments`.`id` = `contents`.`pk_content`);
-- update comment status from contents (accepted, rejected, pending)
UPDATE  `comments`,`contents` SET  `comments`.`status` = 'accepted' WHERE `comments`.`id` = `contents`.`pk_content` AND `contents`.`content_status` = 1;
UPDATE  `comments`,`contents` SET  `comments`.`status` = 'rejected' WHERE `comments`.`id` = `contents`.`pk_content` AND `contents`.`content_status` = 2;
UPDATE  `comments`,`contents` SET  `comments`.`status` = 'pending' WHERE `comments`.`id` = `contents`.`pk_content` AND `contents`.`content_status` = 0;
UPDATE  `comments`,`contents` SET  `comments`.`status` = 'rejected' WHERE `comments`.`id` = `contents`.`pk_content` AND `contents`.`in_litter` = 1;

DELETE FROM `contents` WHERE `contents`.`fk_content_type` = 6;