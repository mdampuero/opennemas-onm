--Eliminar:

--Todos los articulos de la hemeroteca
--Todos los comentarios
--Todas las opiniones (director, autores, editorial)

-- Delete comments /type 6
--DELETE FROM contents WHERE pk_content IN (SELECT `pk_comment` FROM `comments`);
DELETE FROM contents WHERE fk_content_type='6';
DELETE FROM contents_categories WHERE pk_fk_content IN (SELECT `pk_comment` FROM `comments`);
TRUNCATE TABLE `comments`;

-- Delete opinions /type 4

--DELETE FROM contents WHERE pk_content IN (SELECT `pk_opinion` FROM `opinions`);
DELETE FROM contents_categories WHERE pk_fk_content IN (SELECT `pk_content` FROM `contents` WHERE fk_content_type='4');
DELETE FROM contents WHERE fk_content_type='4';
TRUNCATE TABLE opinions;


-- Delete articles /type 1

DELETE FROM contents_categories WHERE pk_fk_content IN (SELECT `pk_content` FROM `contents` WHERE content_status=0  AND fk_content_type=1);
DELETE FROM `articles` WHERE `pk_article`IN (SELECT `pk_content` FROM `contents` WHERE content_status=0  AND fk_content_type=1);
DELETE FROM `articles_clone` WHERE `pk_original` IN (SELECT `pk_content` FROM `contents` WHERE content_status=0  AND fk_content_type=1);
DELETE FROM `articles_clone` WHERE `pk_clone` IN (SELECT `pk_content` FROM `contents` WHERE content_status=0  AND fk_content_type=1);
DELETE FROM related_contents WHERE `pk_content1` IN (SELECT `pk_content` FROM `contents` WHERE content_status=0  AND fk_content_type=1);
DELETE FROM related_contents WHERE `pk_content2` IN (SELECT `pk_content` FROM `contents` WHERE content_status=0  AND fk_content_type=1);
DELETE FROM `contents` WHERE content_status=0 AND fk_content_type=1;

