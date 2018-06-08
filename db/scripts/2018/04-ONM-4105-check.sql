SELECT CONCAT('extension: ', count(*)) FROM `extension` WHERE `id` = 74;
SELECT CONCAT('extension-meta: ', count(*)) FROM `extension_meta` WHERE `extension_id` = 74;
SELECT CONCAT('subscription enabled: ', `enabled`) FROM `extension` WHERE `id` = 12;
SELECT CONCAT('subscription price: ', count(*)) FROM `extension_meta` WHERE `extension_id` = 12 AND `meta_value` LIKE '%i:0;%';
