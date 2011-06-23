------------------------------------------------------------------------------------------------------

--Documentation : date (dd-mm-yyyy) - changes in DB.

ALTER TABLE `videos` ADD `favorite` SMALLINT( 1 ) NULL DEFAULT '0';

--New Feature for enable/disable users 

ALTER TABLE `users` ADD `authorize` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1 authorized - 0 unauthorized' AFTER `phone` 