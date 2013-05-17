-- changes-01102012.sql

-- Please check rigth sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)

-- 09-04-2013
ALTER TABLE  `orders` ADD  `type` VARCHAR( 50 ) NOT NULL AFTER  `payment_method`

-- 06-05-2013
ALTER TABLE `newsletter_archive` CHANGE `created` `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `newsletter_archive` ADD `updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created`;
UPDATE `newsletter_archive` SET  `updated` = `created` WHERE `updated`='0000-00-00 00:00:00';
DROP TABLE `bulletins_archive`;
