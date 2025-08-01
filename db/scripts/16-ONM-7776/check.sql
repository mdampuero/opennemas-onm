SELECT CONCAT('Total: ', (SELECT count(*) FROM `extension` WHERE `id` = 87) + (SELECT count(*) FROM `extension_meta` WHERE `extension_id` = 87));
