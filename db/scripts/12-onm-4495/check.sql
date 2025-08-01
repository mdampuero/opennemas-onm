SELECT CONCAT('extension: ', COUNT(*)) FROM `extension` WHERE `id` = 80;
SELECT CONCAT('extension_meta: ', COUNT(*)) FROM `extension_meta` WHERE `extension_id` = 80;
