SELECT CONCAT('extension: ', count(*)) FROM `extension` WHERE `id` = 79;
SELECT CONCAT('extension-meta: ', count(*)) FROM `extension_meta` WHERE `extension_id` = 79;

