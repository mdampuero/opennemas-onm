RENAME TABLE `newsletter_archive` TO `newsletters`;

UPDATE newsletters SET `sent` = 0 WHERE `sent`='';

ALTER TABLE `newsletters` CHANGE `created` `created` DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `newsletters` CHANGE `pk_newsletter` `id` BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE `newsletters` ADD `type` INT DEFAULT 0 NOT NULL AFTER `id`;
ALTER TABLE `newsletters` ADD `status` INT DEFAULT 0 NOT NULL AFTER `type`;
ALTER TABLE `newsletters` CHANGE `title` `title` VARCHAR(512) NOT NULL;
ALTER TABLE `newsletters` CHANGE `data` `contents` TEXT DEFAULT NULL;
ALTER TABLE `newsletters` CHANGE `html` `html` LONGTEXT DEFAULT NULL;
ALTER TABLE `newsletters` ADD `recipients` TEXT DEFAULT NULL AFTER `contents`;
ALTER TABLE `newsletters` CHANGE `html` `html` MEDIUMTEXT DEFAULT NULL;
ALTER TABLE `newsletters` ADD `schedule` TEXT DEFAULT NULL AFTER `recipients`;
ALTER TABLE `newsletters` CHANGE `updated` `updated` DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `newsletters` CHANGE `sent` `sent_items` INT DEFAULT 0 NOT NULL;
ALTER TABLE `newsletters` ADD `sent` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `updated`;
ALTER TABLE `newsletters` ADD `template_id` BIGINT UNSIGNED DEFAULT NULL;

CREATE INDEX newsletter_type ON newsletters (type);
CREATE INDEX newsletter_template_id ON newsletters (template_id);

UPDATE newsletters SET `sent`=`updated` WHERE `sent_items` > 0;
