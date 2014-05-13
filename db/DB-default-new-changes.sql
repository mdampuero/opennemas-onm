-- This file contains all the changes that has to be applied
-- in the default instance database.

-- Move all the applied changes into the DB-default-applied-changes.sql
-- file whenever it's possible

-- Please check rigth sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)

-- Add index in slug field in translation_ids
ALTER TABLE  `translation_ids` ADD INDEX ( `slug` ) ;

-- Add index in content field in widgets
ALTER TABLE  `widgets` ADD INDEX ( `content` ) ;

-- Add index in pk_menu field in menu_items
ALTER TABLE  `menu_items` ADD INDEX ( `pk_menu` ) ;

--
DROP INDEX `domain_name` ON instances
ALTER TABLE instances CREATE INDEX domains
ALTER TABLE contents CREATE INDEX urn_source
ALTER TABLE content_categories CREATE INDEX name
