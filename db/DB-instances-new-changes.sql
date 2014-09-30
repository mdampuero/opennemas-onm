-- This file contains all the changes that need to be applied in the default onm-instances database.
-- Please refer to default-new-changes.sql to see what changes has to be applied

-- Please check right sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)


-- 11-06-2013
ALTER TABLE `users` CHANGE `login` `username` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `authorize` `activated` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1 activated - 0 deactivated';
ALTER TABLE `users` CHANGE `fk_user_group` `fk_user_group` VARCHAR( 100 ) NULL;
ALTER TABLE `users` CHANGE `pk_user` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `users` ADD `url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT  '' AFTER `sessionexpire`;
ALTER TABLE `users` ADD `bio` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT  '' AFTER `url`;
ALTER TABLE `users` ADD `avatar_img_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT 0 AFTER `bio`;


-- 29-09-2014
UPDATE  `books` SET  `file_img` = 61200 WHERE `file_img` LIKE '%portadachiloe.jpg%';
UPDATE  `books` SET  `file_img` = 61201 WHERE `file_img` LIKE '%portadafrancia.jpg%';
UPDATE  `books` SET  `file_img` = 61202 WHERE `file_img` LIKE '%portadasurezpicallocastellano.jpg%';
UPDATE  `books` SET  `file_img` = 61203 WHERE `file_img` LIKE '%portadasotocastellano.jpg%';
UPDATE  `books` SET  `file_img` = 61204 WHERE `file_img` LIKE '%portadasotogallego.jpg%';
UPDATE  `books` SET  `file_img` = 61205 WHERE `file_img` LIKE '%portadaloshermanosbarcia.jpg%';
UPDATE  `books` SET  `file_img` = 61206 WHERE `file_img` LIKE '%portadaosirmansbarcia.jpg%';
UPDATE  `books` SET  `file_img` = 61207 WHERE `file_img` LIKE '%portadasuarezpicallogallego.jpg%';
UPDATE  `books` SET  `file_img` = 61208 WHERE `file_img` LIKE '%portadalegadosocialdelosespanolesencuba.jpg%';
UPDATE  `books` SET  `file_img` = 61209 WHERE `file_img` LIKE '%portadadelmisterioalarealidad.jpg%';
UPDATE  `books` SET  `file_img` = 61210 WHERE `file_img` LIKE '%portada20mujeres.jpg%';
UPDATE  `books` SET  `file_img` = 61211 WHERE `file_img` LIKE '%portadacervino.jpg%';
UPDATE  `books` SET  `file_img` = 61212 WHERE `file_img` LIKE '%portadalucesdeladiaspora.jpg%';
UPDATE  `books` SET  `file_img` = 61213 WHERE `file_img` LIKE '%portadacanarios.jpg%';
UPDATE  `books` SET  `file_img` = 61214 WHERE `file_img` LIKE '%portadafalasgaiasomeuson.jpg%';
UPDATE  `books` SET  `file_img` = 61215 WHERE `file_img` LIKE '%portadavidasgrabadasenlapiedra.jpg%';
UPDATE  `books` SET  `file_img` = 61216 WHERE `file_img` LIKE '%portada-de-orujo-de-memorias.jpg%';
UPDATE  `books` SET  `file_img` = 61217 WHERE `file_img` LIKE '%webportadahistoriadelterciodegallegos.jpg%';
UPDATE  `books` SET  `file_img` = 61218 WHERE `file_img` LIKE '%portadalosterciosespañolesenladefensadebuenosaires.jpg%';
UPDATE  `books` SET  `file_img` = 61219 WHERE `file_img` LIKE '%webportadasantiagoungritodelibertad.jpg%';
UPDATE  `books` SET  `file_img` = 61220 WHERE `file_img` LIKE '%webportadamulleresdaemigracion.jpg%';
UPDATE  `books` SET  `file_img` = 61221 WHERE `file_img` LIKE '%webportadacanariosencubasusasociaciones-insignes.jpg%';
UPDATE  `books` SET  `file_img` = 61222 WHERE `file_img` LIKE '%webportadaloscanariosylasluchasemancipadoresencuba.jpg%';
UPDATE  `books` SET  `file_img` = 61223 WHERE `file_img` LIKE '%webportadadesaparecidosespanolesenargentina.jpg%';
UPDATE  `books` SET  `file_img` = 61224 WHERE `file_img` LIKE '%webportadacubalosgallegosyelche.jpg%';
UPDATE  `books` SET  `file_img` = 61226 WHERE `file_img` LIKE '%portada-asturias-en-cuba.jpg%';
UPDATE  `books` SET  `file_img` = 61227 WHERE `file_img` LIKE '%webportada.jpg%';
UPDATE  `books` SET  `file_img` = 61228 WHERE `file_img` LIKE '%webportadacanariosenlasletrasdeaquiydealla.jpg%';
UPDATE  `books` SET  `file_img` = 61229 WHERE `file_img` LIKE '%portada2-web-de-catalunya-a-cuba.jpg%';
UPDATE  `books` SET  `file_img` = 61230 WHERE `file_img` LIKE '%portadacanariasenelespiritudecuba.jpg%';
UPDATE  `books` SET  `file_img` = 61231 WHERE `file_img` LIKE '%web2.portada-asturaianos-en-cuba.jpg%';
UPDATE  `books` SET  `file_img` = 61232 WHERE `file_img` LIKE '%web2portada-protagonistas-de-una-epopeya-colectiva.jpg%';
UPDATE  `books` SET  `file_img` = 61233 WHERE `file_img` LIKE '%portadaleonorymarianopadresdemarti.jpg%';
UPDATE  `books` SET  `file_img` = 61234 WHERE `file_img` LIKE '%2webportada-gallegos-en-cuba.jpg%';
UPDATE  `books` SET  `file_img` = 61235 WHERE `file_img` LIKE '%2webportadaindustriaazucarera.jpg%';

ALTER TABLE  `books` CHANGE  `file_img`  `cover_id` BIGINT( 255 ) NULL DEFAULT NULL ;
ALTER TABLE  `books` DROP  `file` ;
