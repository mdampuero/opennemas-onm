#
# Foreign keys definitions
#

# Use proper values for invalid dates
UPDATE `contents` SET `endtime`=NULL WHERE `endtime`<'1970-01-01 00:00:00'
UPDATE `contents` SET `starttime`=NULL WHERE `starttime`<'1970-01-01 00:00:00'

# References cleanup
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

# Update of content types (taked from MAy 5th, 2016 database changes)
ALTER TABLE action_counters CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL;
ALTER TABLE albums_photos CHANGE description description TEXT DEFAULT NULL;
CREATE INDEX pk_album ON albums_photos (pk_album);
CREATE INDEX pk_photo ON albums_photos (pk_photo);
ALTER TABLE albums_photos RENAME INDEX pk_album_2 TO index_album_photo;
ALTER TABLE books CHANGE pk_book pk_book BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (pk_book);
ALTER TABLE `comments` CHANGE `date` `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
DROP INDEX comment_status_date ON comments;
DROP INDEX comment_date ON comments;
ALTER TABLE comments CHANGE content_id content_id BIGINT UNSIGNED DEFAULT NULL, CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE parent_id parent_id BIGINT UNSIGNED DEFAULT 0 NOT NULL;
ALTER TABLE content_categories CHANGE color color VARCHAR(10) DEFAULT NULL;
ALTER TABLE content_positions CHANGE pk_fk_content pk_fk_content BIGINT UNSIGNED NOT NULL, CHANGE fk_category fk_category INT UNSIGNED NOT NULL, CHANGE position position INT UNSIGNED DEFAULT 0 NOT NULL;
CREATE INDEX content_position_pk_content ON content_positions (pk_fk_content);
CREATE INDEX content_position_fk_category ON content_positions (fk_category);
ALTER TABLE contentmeta CHANGE fk_content fk_content BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE contents DROP home_pos, CHANGE starttime starttime DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE changed changed DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE fk_author fk_author INT UNSIGNED DEFAULT NULL, CHANGE fk_publisher fk_publisher INT UNSIGNED DEFAULT NULL, CHANGE fk_user_last_editor fk_user_last_editor INT UNSIGNED DEFAULT NULL, CHANGE in_litter in_litter TINYINT(1) DEFAULT '0';
CREATE INDEX pk_fk_content ON contents_categories (pk_fk_content);
ALTER TABLE commentsmeta CHANGE fk_content fk_content BIGINT UNSIGNED NOT NULL;
ALTER TABLE contents_categories RENAME INDEX catname TO content_categories_catname;
ALTER TABLE content_views CHANGE pk_fk_content pk_fk_content BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE frontpages CHANGE date date INT NOT NULL, CHANGE category category INT NOT NULL, CHANGE pk_frontpage pk_frontpage BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE content_positions content_positions LONGTEXT NOT NULL;
ALTER TABLE frontpages CHANGE date date INT NOT NULL
ALTER TABLE frontpages CHANGE category category INT NOT NULL
ALTER TABLE frontpages CHANGE content_positions content_positions LONGTEXT NOT NULL
ALTER TABLE frontpages CHANGE pk_frontpage pk_frontpage BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (pk_frontpage);
ALTER TABLE letters CHANGE pk_letter pk_letter BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE menu_items CHANGE pk_item pk_item INT UNSIGNED NOT NULL, CHANGE pk_menu pk_menu INT UNSIGNED NOT NULL;
ALTER TABLE menues CHANGE pk_menu pk_menu INT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE newsletter_archive CHANGE pk_newsletter pk_newsletter INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL;
ALTER TABLE orders CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL;
CREATE INDEX fk_pk_poll ON poll_items (fk_pk_poll);
CREATE INDEX pk_content2 ON related_contents (pk_content2);
ALTER TABLE special_contents CHANGE fk_content fk_content BIGINT UNSIGNED NOT NULL, CHANGE fk_special fk_special BIGINT UNSIGNED NOT NULL, ADD PRIMARY KEY (fk_content, fk_special);
CREATE INDEX fk_content ON special_contents (fk_content);
CREATE INDEX fk_special ON special_contents (fk_special);
ALTER TABLE specials CHANGE pk_special pk_special BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE static_pages CHANGE pk_static_page pk_static_page BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE translation_ids CHANGE pk_content pk_content BIGINT UNSIGNED NOT NULL;
CREATE INDEX pk_content ON translation_ids (pk_content);
CREATE INDEX pk_fk_user_group ON user_groups_privileges (pk_fk_user_group);
ALTER TABLE usermeta CHANGE user_id user_id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE users CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE users_content_categories CHANGE pk_fk_user pk_fk_user INT UNSIGNED AUTO_INCREMENT NOT NULL;
ALTER TABLE user_notification ADD read_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL;
ALTER TABLE votes CHANGE pk_vote pk_vote BIGINT UNSIGNED AUTO_INCREMENT NOT NULL;
