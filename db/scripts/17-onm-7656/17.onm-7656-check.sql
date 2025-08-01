# Check new module
SELECT CONCAT('Total: ', (SELECT count(*) FROM `extension` WHERE `id` = 88) + (SELECT count(*) FROM `extension_meta` WHERE `extension_id` = 88));
