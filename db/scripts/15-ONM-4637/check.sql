SELECT CONCAT('extension: ', count(*)) FROM `extension` WHERE `id` = 82;
SELECT CONCAT('extension_meta: ', count(*)) FROM `extension_meta` WHERE `extension_id` = 82 AND `meta_value` LIKE '%es.openhost.module.events%';
