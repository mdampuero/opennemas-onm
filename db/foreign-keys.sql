#
# Foreign keys definitions
#
#Cleanup
DELETE FROM `advertisements` WHERE `pk_advertisement` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `albums` WHERE `pk_album` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `albums_photos` WHERE `pk_album` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `articles` WHERE `pk_article` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `attachments` WHERE `pk_attachment` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `books` WHERE `pk_book` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `comments` WHERE `content_id` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `commentsmeta` WHERE `fk_content` NOT IN (SELECT `id` FROM `comments`);
DELETE FROM `contentmeta` WHERE `fk_content` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `contents_categories` WHERE `pk_fk_content` NOT IN (SELECT `pk_content` FROM `contents`);
-- DELETE FROM `contents_categories` WHERE `pk_fk_content_category` NOT IN (SELECT pk_content_category FROM content_categories);
DELETE FROM `content_positions` WHERE `pk_fk_content` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `content_views` WHERE `pk_fk_content` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `kioskos` WHERE `pk_kiosko` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `letters` WHERE `pk_letter` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `menu_items` WHERE `pk_item` NOT IN (SELECT `pk_menu` FROM `menues`);
DELETE FROM `opinions` WHERE `pk_opinion` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `photos` WHERE `pk_photo` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `polls` WHERE `pk_poll` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `poll_items` WHERE `fk_pk_poll` NOT IN (SELECT `pk_poll` FROM `polls`);
DELETE FROM `related_contents` WHERE `pk_content1` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `related_contents` WHERE `pk_content2` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `specials` WHERE `pk_special` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `special_contents` WHERE `fk_special` NOT IN (SELECT `pk_special` FROM `specials`);
DELETE FROM `special_contents` WHERE `fk_content` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `translation_ids` WHERE `pk_content` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `usermeta` WHERE `user_id` NOT IN (SELECT id FROM users);
DELETE FROM `users_content_categories` WHERE `pk_fk_user` NOT IN (SELECT `id` FROM `users`);
DELETE FROM `user_groups_privileges` WHERE `pk_fk_user_group` NOT IN (SELECT `pk_user_group` FROM `user_groups`);
DELETE FROM `videos` WHERE `pk_video` NOT IN (SELECT `pk_content` FROM `contents`);
DELETE FROM `widgets` WHERE `pk_widget` NOT IN (SELECT `pk_content` FROM `contents`);
