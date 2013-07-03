-- This file contains all the changes that need to be applied in the default onm-instances database.
-- Please refer to default-new-changes.sql to see what changes has to be applied

-- Please check rigth sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)


-- 11-06-2013
ALTER TABLE `users` CHANGE `login` `username` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `authorize` `activated` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1 activated - 0 deactivated';
ALTER TABLE `users` CHANGE `fk_user_group` `fk_user_group` VARCHAR( 100 ) NULL;
ALTER TABLE `users` CHANGE `pk_user` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `users` ADD `url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT  '' AFTER `sessionexpire`;
ALTER TABLE `users` ADD `bio` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT  '' AFTER `url`;
ALTER TABLE `users` ADD `avatar_img_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT 0 AFTER `bio`;