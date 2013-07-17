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
