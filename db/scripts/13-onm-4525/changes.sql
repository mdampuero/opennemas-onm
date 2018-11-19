CREATE TABLE url (
  id INT UNSIGNED AUTO_INCREMENT NOT NULL,
  type INT UNSIGNED DEFAULT 0 NOT NULL,
  source VARCHAR(1024) NOT NULL,
  target VARCHAR(1024) NOT NULL,
  content_type VARCHAR(30) DEFAULT NULL,
  redirection TINYINT(1) DEFAULT '0' NOT NULL,
  enabled TINYINT(1) DEFAULT '0' NOT NULL,
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

INSERT INTO url(
  type,
  source,
  target,
  content_type,
  redirection,
  enabled
) SELECT
  0,
  pk_content_old,
  pk_content,
  type,
  1,
  1
FROM translation_ids;

INSERT INTO url(
  type,
  source,
  target,
  content_type,
  redirection,
  enabled
) SELECT
  1,
  slug,
  pk_content,
  type,
  1,
  1
FROM translation_ids;
