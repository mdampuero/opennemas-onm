
--
-- Estructura de tabla para la tabla `contents`
--

CREATE TABLE IF NOT EXISTS `contents` (
  `pk_content` bigint(20) unsigned NOT NULL auto_increment,
  `fk_content_type` int(10) unsigned NOT NULL,
  `title` varchar(255) character set utf8 collate utf8_spanish_ci default NULL,
  `description` text character set utf8 collate utf8_spanish_ci,
  `metadata` varchar(255) character set utf8 collate utf8_spanish_ci default NULL,
  `starttime` datetime default NULL,
  `endtime` datetime default NULL,
  `created` datetime default NULL,
  `changed` datetime default NULL,
  `published` datetime default NULL,
  `content_status` int(10) unsigned default '0', COMMENT '0- hemeroteca',
  `fk_author` int(10) unsigned default NULL COMMENT 'Clave foranea de user',
  `fk_publisher` int(10) unsigned default NULL COMMENT 'Clave foranea de user',
  `fk_user_last_editor` int(10) unsigned default NULL COMMENT 'Clave foranea de user',
  `views` int(10) unsigned default NULL,  
  `in_litter` tinyint(1) default '0' COMMENT '0publicado 1papelera',
  `permalink` varchar(255) default NULL,
  `slug` varchar(255) default NULL,
  `available` smallint(1) default '1',
   
  PRIMARY KEY  (`pk_content`),
  KEY `fk_content_type` (`fk_content_type`),
  KEY `in_litter` (`in_litter`),
  KEY `content_status` (`content_status`),  
  KEY `available` (`available`),
  KEY `starttime` (`starttime`,`endtime`),
  KEY `published` (`created`),
  FULLTEXT KEY `metadata` (`metadata`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9223372036854775807 ;



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contents_categories`
--

CREATE TABLE IF NOT EXISTS `contents_categories_sites` (
  `pk_fk_content` bigint(20) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  `pk_fk_site` int(10) unsigned NOT NULL,
  `weight` int(10) unsigned default '100' , 
  `placeholder` varchar(100) default NULL,
  `view` text,

  PRIMARY KEY  (`pk_fk_content`,`pk_fk_content_category`),
  KEY `pk_fk_content_category` (`pk_fk_content_category`),
  KEY `pk_fk_site` (`pk_fk_site`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `content_categories`
--

CREATE TABLE IF NOT EXISTS `content_categories` (
  `pk_content_category` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) default NULL,
  `inmenu` int(10) default '0',
  `posmenu` int(10) default '10',
  `internal_category` smallint(1) NOT NULL default '1' COMMENT '0 no se ve, 1 se ve, 2 especial se ve pero no se modifica, 3 albumcategory, 4 categorias de kiosko.',
  `global` smallint(1) NOT NULL default '0' COMMENT '0 solo 1 site, 1 global varios sites.',
  `fk_content_category` int(10) default '0',
  `logo_path` varchar(200) default NULL,
  `color` varchar(10) default '#638F38',
  PRIMARY KEY  (`pk_content_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=296 ;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sites`
--
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `sites` (
  `pk_content_category` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) default NULL,
  `description` text character set utf8 collate utf8_spanish_ci,
  `default_category_id` int(10) default '0' COMMENT ' ',
  `logo_path` varchar(255) default NULL,
  `template` varchar(255) default NULL,
  PRIMARY KEY  (`pk_content_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
