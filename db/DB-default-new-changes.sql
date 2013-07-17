-- This file contains all the changes that has to be applied
-- in the default instance database.

-- Move all the applied changes into the DB-default-applied-changes.sql
-- file whenever it's possible

-- Please check rigth sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)
ALTER TABLE translation_ids ADD `slug` VARCHAR(200) DEFAULT  '' AFTER  `type`;

-- 2013-07-17
ALTER TABLE `contents`
  DROP `placeholder`,
  DROP `home_placeholder`;

ALTER TABLE  `contents`
CHANGE  `description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `metadata`  `metadata` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `slug`  `slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `params`  `params` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `category_name`  `category_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  'name category',
CHANGE  `urn_source`  `urn_source` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
CHANGE  `title`  `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE  `users` ADD INDEX  `user_username` (  `username` );
ALTER TABLE  `users` ADD INDEX  `user_email` (  `email` );

ALTER TABLE  `photos`
DROP  `date` ,
DROP  `resolution` ,
DROP  `type_img` ,
DROP  `media_type` ,
DROP  `color` ;

ALTER TABLE  `contents`
ADD  `body` LONGTEXT NOT NULL AFTER  `description`;

ALTER TABLE `articles`
DROP `columns`,
DROP `home_columns`;

UPDATE contents INNER JOIN articles ON contents.pk_content = articles.pk_article SET contents.body=articles.body
ALTER TABLE `articles` DROP `body`;
UPDATE contents INNER JOIN static_pages ON contents.pk_content = static_pages.pk_static_page SET contents.body=static_pages.body
ALTER TABLE `static_pages` DROP `body`;
UPDATE contents INNER JOIN opinions ON contents.pk_content = opinions.pk_opinion SET contents.body=opinions.body
ALTER TABLE `opinions` DROP `body`;
UPDATE contents INNER JOIN letters ON contents.pk_content = letters.pk_letter SET contents.body=letters.body
ALTER TABLE `letters` DROP `body`;

