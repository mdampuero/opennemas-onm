-- Changes on onm-instances
-- Table instances
-- Add new field activated
ALTER TABLE `instances` ADD `activated` TINYINT( 1 ) NOT NULL 
-- Add new field contact_mail
ALTER TABLE `instances` ADD `contact_mail` VARCHAR( 255 ) NOT NULL 