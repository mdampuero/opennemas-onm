SELECT CONCAT('extension: ', count(1)) FROM `extension` WHERE `id` = 81;
SELECT CONCAT('extension_meta: ', count(1)) FROM `extension_meta` WHERE `extension_id` = 81;
SELECT CONCAT('basic: ', count(1)) FROM `extension_meta` WHERE `extension_id` = 81 AND `meta_value` LIKE '%es.openhost.module.tags%';
