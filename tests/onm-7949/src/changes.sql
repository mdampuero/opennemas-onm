ALTER table category
ADD COLUMN `cover_id` bigint(20) unsigned DEFAULT NULL;
ALTER TABLE `category`
  ADD CONSTRAINT `cover_id_pk_content`
  FOREIGN KEY (`cover_id`)
  REFERENCES `contents`(`pk_content`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;
ALTER TABLE `category`
  ADD INDEX cover_id (cover_id);
