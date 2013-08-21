-- This file contains all the changes that has to be applied
-- in the default instance database.

-- Move all the applied changes into the DB-default-applied-changes.sql
-- file whenever it's possible

-- Please check rigth sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)
ALTER TABLE translation_ids ADD `slug` VARCHAR(200) DEFAULT  '' AFTER  `type`;

-- Added config parameters to monetize the newsletter.
REPLACE INTO settings (`name`, `value`) VALUES ('newsletter_sender', 's:30:"no-reply@postman.opennemas.com"');
REPLACE INTO settings (`name`, `value`) VALUES ('max_mailing', 's:1:"0"');
REPLACE INTO settings (`name`, `value`) VALUES ('last_invoice', 's:19:"2013-07-28 10:00:00"');