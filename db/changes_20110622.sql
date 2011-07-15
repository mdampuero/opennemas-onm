-- Add new ONM_SETTINGS privilege to the database and adding it to the Admnistrator grupo
INSERT INTO `privileges` ( `name` , `description` , `module` ) VALUES ( 'ONM_SETTINGS', 'Allow to configure system wide settings', 'ONM' );
INSERT INTO `user_groups_privileges` ( `pk_fk_user_group` , `pk_fk_privilege` ) VALUES ( '5', '32');

-- Create Menu table for frontpage menu manager


-- --------------------------------------------------------

--
-- Table structure for table `menues`
--

CREATE TABLE IF NOT EXISTS `menues` (
  `pk_menu` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `categories` text NOT NULL,
  `params` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_menu`),
  UNIQUE KEY `name` (`name`),
  KEY `name_2` (`name`),
  KEY `pk_menu` (`pk_menu`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `menues`
--

INSERT INTO `menues` (`pk_menu`, `name`, `type`, `categories`, `params`) VALUES
(1, 'frontpage', '', '', NULL),
(2, 'opinion', '', '', NULL),
(3, 'mobile', '', '', NULL),
(4, 'album', '', '', NULL),
(5, 'video', '', '', NULL),
(6, 'poll', '', '', NULL);


-- Create table for settings
DROP  TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
 `name` varchar(128) NOT NULL DEFAULT '',
 `value` longtext NOT NULL,
 PRIMARY KEY (`name`)
)
INSERT INTO `settings` VALUES ( 'opinion_algoritm', 's:8:"position";');

--Save video object in database
ALTER TABLE `videos` CHANGE `videoid` `video_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `videos` CHANGE `htmlcode` `information` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

--Documentation : date (dd-mm-yyyy) - changes in DB.

ALTER TABLE `videos` ADD `favorite` SMALLINT( 1 ) NULL DEFAULT '0';

--New Feature for enable/disable users

ALTER TABLE `users` ADD `authorize` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1 authorized - 0 unauthorized' AFTER `phone`

--Config changes for LOG
INSERT INTO `nuevatribuna`.`settings` (`name` ,`value`)
VALUES ('log_level', 's:6:"normal";');
INSERT INTO `nuevatribuna`.`settings` (`name` ,`value`)
VALUES ('log_enable', 'b:1;');
