ALTER TABLE `newsletters` ADD `name` VARCHAR(512) NULL AFTER `status`;
ALTER TABLE `newsletters` ADD `params` TEXT NULL AFTER `template_id`;
