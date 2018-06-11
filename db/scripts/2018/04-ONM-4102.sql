INSERT INTO `extension` (`uuid`, `name`, `type`, `author`, `description`, `about`, `images`, `created`, `updated`, `enabled`, `url`) VALUES
('es.openhost.module.whiteLabel', 'a:3:{s:2:\"en\";s:19:\"Backend White label\";s:2:\"es\";s:36:\"Panel de administracion marca blanca\";s:2:\"gl\";s:37:\"Panel de administraciÃ³n marca blanca\";}', 'module', 'OpenHost S.L.', 'a:3:{s:2:\"en\";s:0:\"\";s:2:\"es\";s:0:\"\";s:2:\"gl\";s:0:\"\";}', 'a:3:{s:2:\"en\";s:0:\"\";s:2:\"es\";s:0:\"\";s:2:\"gl\";s:0:\"\";}', NULL, '2018-06-08 13:59:49', '2018-06-08 13:59:49', 0, 'http://www.openhost.es');

SELECT @extension := `id`
FROM extension WHERE uuid = 'es.openhost.module.whiteLabel';

INSERT INTO `extension_meta` (`extension_id`, `meta_key`, `meta_value`) VALUES
(@extension, 'category', 'module'),
(@extension, 'notes', 'a:3:{s:2:\"en\";s:0:\"\";s:2:\"es\";s:0:\"\";s:2:\"gl\";s:0:\"\";}'),
(@extension, 'price', 'a:1:{i:0;a:2:{s:5:\"value\";i:0;s:4:\"type\";s:7:\"monthly\";}}'),
(@extension, 'terms', 'a:3:{s:2:\"en\";s:0:\"\";s:2:\"es\";s:0:\"\";s:2:\"gl\";s:0:\"\";}');
