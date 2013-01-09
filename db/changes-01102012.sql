-- changes-01102012.sql

-- Please check rigth sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)

-- 10-10-2012
CREATE TABLE IF NOT EXISTS `contentmeta` (
  `fk_content` bigint(20) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`fk_content`,`meta_name`),
  KEY `fk_content` (`fk_content`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- 01-08-2012
DROP TABLE privileges;

-- 01-09-2012
UPDATE users SET name = CONCAT(name, ' ', firstname, ' ', lastname);
ALTER TABLE  `users` DROP  `firstname` , DROP  `lastname` ;