-- phpMyAdmin SQL Dump
-- version 2.11.11.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 14, 2012 at 01:08 PM
-- Server version: 5.0.77
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `canariasahora20`
--

-- --------------------------------------------------------

--
-- Table structure for table `actualizar_hora`
--

CREATE TABLE IF NOT EXISTS `actualizar_hora` (
  `hora` varchar(100) NOT NULL,
  PRIMARY KEY  (`hora`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `anos_lista`
--

CREATE TABLE IF NOT EXISTS `anos_lista` (
  `ano` int(11) default '0',
  `activada` int(1) NOT NULL default '0',
  KEY `ano` (`ano`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ayuntamientos_islas`
--

CREATE TABLE IF NOT EXISTS `ayuntamientos_islas` (
  `islas` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`islas`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ayuntamientos_municipios`
--

CREATE TABLE IF NOT EXISTS `ayuntamientos_municipios` (
  `municipio` varchar(30) NOT NULL default '',
  `islas` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`municipio`),
  KEY `islas` (`islas`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ayuntamientos_noticias`
--

CREATE TABLE IF NOT EXISTS `ayuntamientos_noticias` (
  `noticia` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `antetitulo` varchar(250) NOT NULL default '',
  `titulo` varchar(250) NOT NULL default '',
  `texto` text NOT NULL,
  `isla` varchar(20) NOT NULL default '',
  `municipio` varchar(30) NOT NULL default '',
  `activar` int(1) NOT NULL default '0',
  PRIMARY KEY  (`noticia`),
  KEY `municipio` (`municipio`),
  KEY `isla` (`isla`),
  KEY `fecha` (`fecha`),
  KEY `hora` (`hora`),
  KEY `activar` (`activar`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=6092 ;

-- --------------------------------------------------------

--
-- Table structure for table `ayuntamientos_usuarios`
--

CREATE TABLE IF NOT EXISTS `ayuntamientos_usuarios` (
  `isla` varchar(250) NOT NULL default '',
  `descripcion` varchar(250) NOT NULL default '',
  `municipio` varchar(250) NOT NULL default '',
  `usuario` varchar(250) NOT NULL default '',
  `password` varchar(250) NOT NULL default '',
  `email` varchar(250) NOT NULL default '',
  `telefono` varchar(100) NOT NULL default '',
  `www` varchar(250) NOT NULL default '',
  `nombre_contacto` varchar(250) NOT NULL default '',
  `activar` int(1) NOT NULL default '0',
  `conectado` int(1) NOT NULL default '0',
  KEY `isla` (`isla`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `blog_semanal`
--

CREATE TABLE IF NOT EXISTS `blog_semanal` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `url` varchar(250) NOT NULL,
  `texto` text NOT NULL,
  `foto` varchar(250) NOT NULL,
  `activar` int(11) NOT NULL COMMENT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloques`
--

CREATE TABLE IF NOT EXISTS `bloques` (
  `id` int(11) NOT NULL auto_increment,
  `idbloques_tipos` int(11) NOT NULL,
  `idcanal` int(11) NOT NULL,
  `detalle` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idbloques_tipos` (`idbloques_tipos`),
  KEY `idcanal` (`idcanal`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `bloques_nivo-slider_effects`
--

CREATE TABLE IF NOT EXISTS `bloques_nivo-slider_effects` (
  `effect` varchar(50) NOT NULL,
  PRIMARY KEY  (`effect`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bloques_nivo-slider_opc`
--

CREATE TABLE IF NOT EXISTS `bloques_nivo-slider_opc` (
  `idbloques` int(11) NOT NULL,
  `pausetime` int(11) NOT NULL,
  `effect` varchar(50) NOT NULL,
  KEY `pausetime` (`pausetime`),
  KEY `idbloques` (`idbloques`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bloques_noticias`
--

CREATE TABLE IF NOT EXISTS `bloques_noticias` (
  `idnoticia` int(11) NOT NULL,
  `idbloque` int(11) NOT NULL,
  `orden` int(99) NOT NULL,
  KEY `orden` (`orden`),
  KEY `bloque` (`idbloque`),
  KEY `idnoticia` (`idnoticia`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bloques_tipos`
--

CREATE TABLE IF NOT EXISTS `bloques_tipos` (
  `id` int(11) NOT NULL auto_increment,
  `detalle` varchar(250) NOT NULL,
  `tabla` varchar(50) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `canales`
--

CREATE TABLE IF NOT EXISTS `canales` (
  `id` int(11) NOT NULL auto_increment,
  `canal` varchar(250) NOT NULL,
  `detalle` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `nombre` (`canal`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `canales_portadas`
--

CREATE TABLE IF NOT EXISTS `canales_portadas` (
  `id` int(11) NOT NULL auto_increment,
  `idcanal` int(11) NOT NULL,
  `seccion` varchar(250) NOT NULL,
  `detalle` varchar(250) NOT NULL,
  `activar` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `idcanal` (`idcanal`),
  KEY `seccion` (`seccion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `canales_secciones`
--

CREATE TABLE IF NOT EXISTS `canales_secciones` (
  `id` int(11) NOT NULL auto_increment,
  `idcanal` int(11) NOT NULL,
  `seccion` varchar(250) NOT NULL,
  `detalle` varchar(250) NOT NULL,
  `activar` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `idcanal` (`idcanal`),
  KEY `seccion` (`seccion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Table structure for table `canales_subsecciones`
--

CREATE TABLE IF NOT EXISTS `canales_subsecciones` (
  `id` int(11) NOT NULL auto_increment,
  `idcanal` int(11) NOT NULL,
  `idseccion` int(11) NOT NULL,
  `subseccion` varchar(250) NOT NULL,
  `detalle` varchar(250) NOT NULL,
  `activar` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `idcanal` (`idcanal`),
  KEY `idseccion` (`idseccion`),
  KEY `subseccion` (`subseccion`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `canario_en`
--

CREATE TABLE IF NOT EXISTS `canario_en` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `lugar` varchar(250) NOT NULL,
  `antetitulo` varchar(250) NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `entradilla` text NOT NULL,
  `texto` text NOT NULL,
  `foto` varchar(250) NOT NULL,
  `piedefoto` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `cartas`
--

CREATE TABLE IF NOT EXISTS `cartas` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `titulo` varchar(100) NOT NULL default '',
  `autor` varchar(250) NOT NULL default '',
  `texto` text NOT NULL,
  `orden` varchar(10) NOT NULL default '',
  `activar` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `orden` (`orden`),
  KEY `fecha` (`fecha`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=1806 ;

-- --------------------------------------------------------

--
-- Table structure for table `colores`
--

CREATE TABLE IF NOT EXISTS `colores` (
  `id` tinyint(4) NOT NULL auto_increment,
  `color` varchar(30) NOT NULL,
  `detalle` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `coverlite`
--

CREATE TABLE IF NOT EXISTS `coverlite` (
  `activar` int(1) NOT NULL,
  `activar_portada` int(11) NOT NULL,
  `evento` varchar(15) character set utf8 collate utf8_unicode_ci NOT NULL,
  `id_evento` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `id_evento` (`id_evento`),
  UNIQUE KEY `evento` (`evento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `coverlitederbi`
--

CREATE TABLE IF NOT EXISTS `coverlitederbi` (
  `activar` int(1) NOT NULL,
  `activar_portada` int(1) NOT NULL,
  PRIMARY KEY  (`activar`),
  KEY `activar_portada` (`activar_portada`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `elecciones_candidatos`
--

CREATE TABLE IF NOT EXISTS `elecciones_candidatos` (
  `nid` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `primerapellido` varchar(250) NOT NULL,
  `segundoapellido` varchar(250) NOT NULL,
  `partidopolitico` varchar(250) NOT NULL,
  `cargo` varchar(250) NOT NULL,
  `institucion` varchar(250) NOT NULL,
  `isla` varchar(250) NOT NULL,
  `municipio` varchar(250) NOT NULL,
  `foto` varchar(250) NOT NULL,
  `web` varchar(250) NOT NULL,
  `blog` varchar(250) NOT NULL,
  `facebook` varchar(250) NOT NULL,
  `facebook_partido` varchar(250) NOT NULL,
  `twitter` varchar(250) NOT NULL,
  `fechamodificacion` varchar(250) NOT NULL,
  PRIMARY KEY  (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eltiempo`
--

CREATE TABLE IF NOT EXISTS `eltiempo` (
  `titulo` varchar(250) NOT NULL default '',
  `texto` text NOT NULL,
  `imagen_tiempo` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`titulo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `encuestas`
--

CREATE TABLE IF NOT EXISTS `encuestas` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `seccion` varchar(100) NOT NULL default '',
  `pregunta` varchar(200) NOT NULL default '',
  `foto` varchar(250) NOT NULL,
  `respuesta1` varchar(60) NOT NULL default '',
  `respuesta2` varchar(60) NOT NULL default '',
  `respuesta3` varchar(60) NOT NULL default '',
  `respuesta4` varchar(60) NOT NULL default '',
  `voto1` int(11) NOT NULL default '0',
  `voto2` int(11) NOT NULL default '0',
  `voto3` int(11) NOT NULL default '0',
  `voto4` int(11) NOT NULL default '0',
  `cerrar` int(1) NOT NULL,
  `activar` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `activar` (`activar`),
  KEY `fecha` (`fecha`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=799 ;

-- --------------------------------------------------------

--
-- Table structure for table `encuestas_portada`
--

CREATE TABLE IF NOT EXISTS `encuestas_portada` (
  `columna` int(11) NOT NULL,
  `orden` int(11) NOT NULL,
  `activar` int(1) NOT NULL,
  KEY `columna` (`columna`),
  KEY `orden` (`orden`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `entrevistas`
--

CREATE TABLE IF NOT EXISTS `entrevistas` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `antetitulo` varchar(250) NOT NULL default '',
  `titulo` varchar(250) NOT NULL default '',
  `texto` longtext NOT NULL,
  `texto_foto` varchar(250) NOT NULL default '',
  `foto_peq` varchar(250) NOT NULL default '',
  `foto_gran` varchar(250) NOT NULL default '',
  `activar` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `en_imagenes`
--

CREATE TABLE IF NOT EXISTS `en_imagenes` (
  `foto` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `titulo` varchar(100) NOT NULL,
  `texto` text,
  `foto_peq` varchar(250) NOT NULL default '',
  `foto_gran` varchar(250) NOT NULL default '',
  `activar` int(1) NOT NULL default '0',
  `orden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`foto`),
  KEY `fecha` (`fecha`),
  KEY `hora` (`hora`),
  KEY `activar` (`activar`),
  FULLTEXT KEY `titulo` (`texto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `en_imagenes_portada`
--

CREATE TABLE IF NOT EXISTS `en_imagenes_portada` (
  `columna` int(11) NOT NULL,
  `orden` int(11) NOT NULL,
  `activar` int(1) NOT NULL,
  KEY `columna` (`columna`),
  KEY `orden` (`orden`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `especiales`
--

CREATE TABLE IF NOT EXISTS `especiales` (
  `nombre` varchar(250) NOT NULL default '',
  FULLTEXT KEY `nombre` (`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fauna`
--

CREATE TABLE IF NOT EXISTS `fauna` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `antetitulo` varchar(100) NOT NULL default '',
  `titulo` varchar(100) NOT NULL default '',
  `texto` longtext NOT NULL,
  `foto_peq` varchar(250) NOT NULL default '',
  `foto_gran` varchar(250) NOT NULL default '',
  `foto_ampliar` varchar(250) NOT NULL,
  `foto_texto` varchar(200) NOT NULL default '',
  `noti_relacion` varchar(250) NOT NULL,
  `opinion_relacion` varchar(250) NOT NULL,
  `documentos_relacion` varchar(250) NOT NULL,
  `audio_relacion` varchar(250) NOT NULL,
  `video_relacion` varchar(250) NOT NULL,
  `galeria_relacion` varchar(250) NOT NULL,
  `encuesta_relacion` varchar(250) NOT NULL,
  `video_youtube` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL default '0',
  `orden` int(99) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `activar` (`activar`),
  KEY `orden` (`orden`),
  FULLTEXT KEY `antetitulo` (`antetitulo`,`titulo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=132 ;

-- --------------------------------------------------------

--
-- Table structure for table `foto_denuncia`
--

CREATE TABLE IF NOT EXISTS `foto_denuncia` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `autor` varchar(100) NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `texto` text NOT NULL,
  `foto` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `activar` (`activar`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5210 ;

-- --------------------------------------------------------

--
-- Table structure for table `frase`
--

CREATE TABLE IF NOT EXISTS `frase` (
  `texto` varchar(250) NOT NULL,
  `url` varchar(250) NOT NULL,
  PRIMARY KEY  (`texto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `galeria_fotos`
--

CREATE TABLE IF NOT EXISTS `galeria_fotos` (
  `id` int(11) NOT NULL,
  `imagen` varchar(250) NOT NULL,
  `imagen_peq` varchar(250) NOT NULL,
  `piedefoto` varchar(250) NOT NULL,
  `idnoticia` int(11) NOT NULL,
  KEY `idnoticia` (`idnoticia`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `galeria_susfotos`
--

CREATE TABLE IF NOT EXISTS `galeria_susfotos` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `autor` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `imagen` varchar(250) NOT NULL,
  `imagen_peq` varchar(250) NOT NULL,
  `imagen_thb` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL,
  `idnoticia` int(11) NOT NULL,
  KEY `id` (`id`),
  KEY `idnoticia` (`idnoticia`),
  KEY `fecha` (`fecha`),
  KEY `hora` (`hora`),
  KEY `activar` (`activar`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hemeroteca_noticias`
--

CREATE TABLE IF NOT EXISTS `hemeroteca_noticias` (
  `id` int(10) NOT NULL auto_increment,
  `titulo` mediumtext,
  `antetitulo` mediumtext,
  `entradilla` text,
  `texto` mediumtext,
  `seccion` varchar(250) default NULL,
  `fecha` date default NULL,
  `hora` time default NULL,
  `data` varchar(255) default NULL,
  `firma` varchar(255) default NULL,
  KEY `id` (`id`),
  FULLTEXT KEY `titulo` (`titulo`,`antetitulo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=84445 ;

-- --------------------------------------------------------

--
-- Table structure for table `hemeroteca_textos`
--

CREATE TABLE IF NOT EXISTS `hemeroteca_textos` (
  `id` int(10) NOT NULL auto_increment,
  `texto` longtext,
  `idorden` varchar(1) default NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=84445 ;

-- --------------------------------------------------------

--
-- Table structure for table `hemeroteca_topsecret`
--

CREATE TABLE IF NOT EXISTS `hemeroteca_topsecret` (
  `id` int(10) NOT NULL auto_increment,
  `antetitulo` varchar(250) default NULL,
  `titulo` varchar(250) default NULL,
  `texto` text,
  `fecha` date default NULL,
  `hora` time default NULL,
  `activar` varchar(1) default NULL,
  `hemeroteca` varchar(1) default NULL,
  KEY `id` (`id`),
  KEY `hemeroteca` (`hemeroteca`),
  KEY `fecha` (`fecha`),
  KEY `titulo_2` (`titulo`),
  KEY `antetitulo` (`antetitulo`),
  FULLTEXT KEY `titulo` (`antetitulo`,`titulo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12994 ;

-- --------------------------------------------------------

--
-- Table structure for table `humor`
--

CREATE TABLE IF NOT EXISTS `humor` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `humor_peq` varchar(250) NOT NULL default '',
  `humor_gran` varchar(250) NOT NULL default '',
  `activar` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `hora` (`hora`),
  KEY `activar` (`activar`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=484 ;

-- --------------------------------------------------------

--
-- Table structure for table `lectores`
--

CREATE TABLE IF NOT EXISTS `lectores` (
  `lectores` text NOT NULL,
  `email` varchar(200) NOT NULL default '',
  FULLTEXT KEY `lectores` (`lectores`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lectores_usuarios`
--

CREATE TABLE IF NOT EXISTS `lectores_usuarios` (
  `lector` int(11) NOT NULL auto_increment,
  `usuario` varchar(10) NOT NULL default '',
  `password` varchar(20) NOT NULL default '',
  `nombre` varchar(100) NOT NULL default '',
  `apellidos` varchar(100) NOT NULL default '',
  `telefono` varchar(20) NOT NULL default '',
  `profesion` varchar(250) NOT NULL default '',
  `edad` char(3) NOT NULL default '0',
  `sexo` char(1) NOT NULL default '0',
  `email` varchar(250) NOT NULL default '',
  `isla` varchar(250) NOT NULL default '',
  `codigo_postal` varchar(10) NOT NULL default '0',
  `activado` int(1) NOT NULL default '0',
  `cod_activacion` varchar(250) NOT NULL default '',
  `publicidad` int(1) NOT NULL default '0',
  `topsecret` int(1) NOT NULL,
  `canarias` int(1) NOT NULL default '0',
  `nacional` int(1) NOT NULL default '0',
  `internacional` int(1) NOT NULL default '0',
  `economia` int(1) NOT NULL default '0',
  `deportes` int(1) NOT NULL default '0',
  `sociedad` int(1) NOT NULL default '0',
  `cultura` int(1) NOT NULL default '0',
  PRIMARY KEY  (`lector`),
  KEY `activado` (`activado`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=1 CHECKSUM=1 DELAY_KEY_WRITE=1 AUTO_INCREMENT=3378 ;

-- --------------------------------------------------------

--
-- Table structure for table `lomasleido`
--

CREATE TABLE IF NOT EXISTS `lomasleido` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `id_noticia` int(11) NOT NULL,
  `seccion` varchar(250) NOT NULL,
  `visita` int(2) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `visita` (`visita`),
  KEY `seccion` (`seccion`),
  KEY `id_noticia` (`id_noticia`)
) ENGINE=MEMORY  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9664 ;

-- --------------------------------------------------------

--
-- Table structure for table `noticias`
--

CREATE TABLE IF NOT EXISTS `noticias` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `fecha_activar` int(1) NOT NULL,
  `hora` time NOT NULL default '00:00:00',
  `hora_activar` int(1) NOT NULL,
  `especiales` varchar(250) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `EFE_motor` varchar(50) NOT NULL,
  `efe_id` int(111) NOT NULL,
  `version` varchar(250) NOT NULL,
  `texto_id` int(111) NOT NULL,
  `EFE_seccion` varchar(100) NOT NULL,
  `canal` varchar(250) NOT NULL,
  `seccion` varchar(250) NOT NULL default '',
  `seccion_activar` int(1) NOT NULL,
  `subseccion` varchar(250) NOT NULL,
  `islas` varchar(250) NOT NULL,
  `titulo` varchar(250) default NULL,
  `antetitulo` varchar(100) NOT NULL default '',
  `titulo_home` varchar(250) default NULL,
  `titulo_premium` varchar(250) NOT NULL,
  `antetitulo_premium` varchar(250) NOT NULL,
  `entradilla_premium` text NOT NULL,
  `antetitulo_home` varchar(100) NOT NULL default '',
  `entradilla` text NOT NULL,
  `data` varchar(150) NOT NULL default '',
  `firma` varchar(100) NOT NULL default '',
  `firma_activar` int(1) NOT NULL,
  `piedefoto` varchar(250) NOT NULL default '',
  `foto_noti_ampliada` text NOT NULL,
  `foto_portada` text,
  `foto_seccion` text NOT NULL,
  `foto_panoramica` text,
  `noti_relacion` varchar(250) NOT NULL,
  `opinion_relacion` varchar(250) NOT NULL,
  `documentos_relacion` varchar(250) NOT NULL,
  `audio_relacion` varchar(250) NOT NULL,
  `video_relacion` varchar(250) NOT NULL,
  `galeria_relacion` varchar(250) NOT NULL,
  `susfotos_relacion` varchar(250) NOT NULL,
  `encuesta_relacion` varchar(250) NOT NULL,
  `fuerteventuraahora_relacion` varchar(250) NOT NULL,
  `diversia_relacion` varchar(250) NOT NULL,
  `activar_diversia` int(1) NOT NULL default '0',
  `deportes_portada` int(1) NOT NULL,
  `empresarios_url` varchar(250) NOT NULL,
  `radio` int(1) NOT NULL,
  `activar_premium` int(1) NOT NULL,
  `activar` int(1) NOT NULL default '0',
  `texto` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `hora` (`hora`),
  KEY `seccion` (`seccion`),
  KEY `activar` (`activar`),
  KEY `EFE_motor` (`EFE_motor`),
  KEY `EFE_seccion` (`EFE_seccion`),
  KEY `tipo` (`tipo`),
  KEY `subseccion` (`subseccion`),
  KEY `deportes_portada` (`deportes_portada`),
  KEY `especiales` (`especiales`),
  KEY `titulo_2` (`titulo`),
  KEY `radio` (`radio`),
  KEY `activar_premium` (`activar_premium`),
  KEY `canal` (`canal`),
  FULLTEXT KEY `titulo` (`titulo`,`titulo_home`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=229795 ;

-- --------------------------------------------------------

--
-- Table structure for table `noticias_portada`
--

CREATE TABLE IF NOT EXISTS `noticias_portada` (
  `id` int(11) NOT NULL,
  `fecha_portada` date default NULL,
  `canal` varchar(250) NOT NULL,
  `bloque` varchar(10) NOT NULL,
  `tipo` varchar(10) NOT NULL,
  `orden` int(99) NOT NULL,
  `columna` int(11) NOT NULL,
  `color` varchar(30) NOT NULL default 'white',
  KEY `fecha_portada` (`fecha_portada`),
  KEY `tipo` (`tipo`),
  KEY `columna` (`columna`),
  KEY `orden` (`orden`),
  KEY `id` (`id`),
  KEY `canal` (`canal`),
  KEY `bloque` (`bloque`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `noticias_portada_2`
--

CREATE TABLE IF NOT EXISTS `noticias_portada_2` (
  `id` int(11) NOT NULL,
  `fecha_portada` date default NULL,
  `canal` varchar(250) NOT NULL,
  `bloque` varchar(10) NOT NULL,
  `tipo` varchar(10) NOT NULL,
  `orden` int(99) NOT NULL,
  `columna` int(11) NOT NULL,
  `color` varchar(30) NOT NULL default 'white',
  KEY `fecha_portada` (`fecha_portada`),
  KEY `tipo` (`tipo`),
  KEY `columna` (`columna`),
  KEY `orden` (`orden`),
  KEY `id` (`id`),
  KEY `canal` (`canal`),
  KEY `bloque` (`bloque`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `noticias_portada_temp`
--

CREATE TABLE IF NOT EXISTS `noticias_portada_temp` (
  `id` int(11) NOT NULL,
  `fecha_portada` date default NULL,
  `canal` varchar(250) NOT NULL,
  `bloque` varchar(10) NOT NULL,
  `tipo` varchar(10) NOT NULL,
  `orden` int(11) NOT NULL,
  `columna` int(11) NOT NULL,
  `color` varchar(30) NOT NULL default 'white',
  KEY `fecha_portada` (`fecha_portada`),
  KEY `orden_portada` (`orden`),
  KEY `colum_portada` (`columna`),
  KEY `canal` (`canal`),
  KEY `bloque` (`bloque`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `noticias_relacionadas`
--

CREATE TABLE IF NOT EXISTS `noticias_relacionadas` (
  `idnoticia` int(11) NOT NULL,
  `tipo` varchar(250) NOT NULL,
  `idrelacion` int(11) NOT NULL,
  `titulo` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ojd_link`
--

CREATE TABLE IF NOT EXISTS `ojd_link` (
  `mes` varchar(2) default NULL,
  `ano` int(4) NOT NULL,
  `visitas` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `opinion`
--

CREATE TABLE IF NOT EXISTS `opinion` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `titulo` varchar(100) NOT NULL default '',
  `antetitulo` varchar(250) NOT NULL default '',
  `entradilla` text NOT NULL,
  `autor` varchar(250) NOT NULL default '',
  `email` varchar(250) NOT NULL default '',
  `texto` longtext NOT NULL,
  `foto` varchar(250) NOT NULL,
  `orden` int(5) NOT NULL default '0',
  `activar` int(1) NOT NULL default '0',
  `nohemeroteca` int(1) NOT NULL default '0',
  `opinion_destacada` int(1) NOT NULL,
  `opinion_jorgebatista` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `activar` (`activar`),
  KEY `orden` (`orden`),
  KEY `hora` (`hora`),
  KEY `id` (`id`),
  FULLTEXT KEY `titulo` (`titulo`,`antetitulo`,`entradilla`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=8269 ;

-- --------------------------------------------------------

--
-- Table structure for table `opinion_deportesahora`
--

CREATE TABLE IF NOT EXISTS `opinion_deportesahora` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `titulo` varchar(100) NOT NULL default '',
  `antetitulo` varchar(250) NOT NULL default '',
  `entradilla` text NOT NULL,
  `autor` varchar(250) NOT NULL default '',
  `email` varchar(250) NOT NULL default '',
  `texto` longtext NOT NULL,
  `foto` varchar(250) NOT NULL,
  `orden` int(5) NOT NULL default '0',
  `activar` int(1) NOT NULL default '0',
  `nohemeroteca` int(1) NOT NULL default '0',
  `opinion_destacada` int(1) NOT NULL,
  `opinion_jorgebatista` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `activar` (`activar`),
  KEY `orden` (`orden`),
  KEY `hora` (`hora`),
  KEY `id` (`id`),
  FULLTEXT KEY `titulo` (`titulo`,`antetitulo`,`entradilla`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `opinion_lectores`
--

CREATE TABLE IF NOT EXISTS `opinion_lectores` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `nombre` varchar(250) NOT NULL default '',
  `email` varchar(250) NOT NULL default '',
  `asunto` varchar(250) NOT NULL default '',
  `texto` text NOT NULL,
  `seccion` varchar(250) NOT NULL default '',
  `idnoticia` int(11) NOT NULL default '0',
  `ip_real` varchar(255) NOT NULL,
  `revisado` int(1) NOT NULL,
  `activar` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `seccion` (`seccion`),
  KEY `activar` (`activar`),
  KEY `hora` (`hora`),
  KEY `idnoticia` (`idnoticia`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=1 AUTO_INCREMENT=76764 ;

-- --------------------------------------------------------

--
-- Table structure for table `opinion_portada`
--

CREATE TABLE IF NOT EXISTS `opinion_portada` (
  `id` int(11) NOT NULL,
  `fecha_opinion` date default NULL,
  `orden` int(99) NOT NULL,
  KEY `fecha_portada` (`fecha_opinion`),
  KEY `orden` (`orden`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `opinion_portada_deportesahora`
--

CREATE TABLE IF NOT EXISTS `opinion_portada_deportesahora` (
  `id` int(11) NOT NULL,
  `fecha_opinion` date default NULL,
  `orden` int(99) NOT NULL,
  KEY `fecha_portada` (`fecha_opinion`),
  KEY `orden` (`orden`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `opinion_portada_temp`
--

CREATE TABLE IF NOT EXISTS `opinion_portada_temp` (
  `id` int(11) NOT NULL,
  `fecha_opinion` date default NULL,
  `orden` int(99) NOT NULL,
  KEY `fecha_portada` (`fecha_opinion`),
  KEY `orden` (`orden`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `portadas`
--

CREATE TABLE IF NOT EXISTS `portadas` (
  `fecha` date NOT NULL,
  KEY `fecha` (`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `portada_bloqueo`
--

CREATE TABLE IF NOT EXISTS `portada_bloqueo` (
  `bloqueo` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `portada_bloqueo_usuario`
--

CREATE TABLE IF NOT EXISTS `portada_bloqueo_usuario` (
  `usuario` varchar(250) NOT NULL,
  `hora` time NOT NULL,
  PRIMARY KEY  (`usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `portada_topsecret`
--

CREATE TABLE IF NOT EXISTS `portada_topsecret` (
  `fecha` date NOT NULL,
  KEY `fecha` (`fecha`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `productividad`
--

CREATE TABLE IF NOT EXISTS `productividad` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `usuario` varchar(250) collate latin1_bin NOT NULL,
  `accion` varchar(250) collate latin1_bin NOT NULL,
  `id_noticia` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`,`usuario`),
  KEY `id_noticia` (`id_noticia`),
  KEY `accion` (`accion`),
  KEY `hora` (`hora`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_bin AUTO_INCREMENT=372056 ;

-- --------------------------------------------------------

--
-- Table structure for table `radio_arrancadilla`
--

CREATE TABLE IF NOT EXISTS `radio_arrancadilla` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `texto` text NOT NULL,
  `activar` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `radio_hoy`
--

CREATE TABLE IF NOT EXISTS `radio_hoy` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `texto` text NOT NULL,
  `foto` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=162 ;

-- --------------------------------------------------------

--
-- Table structure for table `radio_podcast`
--

CREATE TABLE IF NOT EXISTS `radio_podcast` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `programa` varchar(250) NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `entradilla` text NOT NULL,
  `fichero_audio` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `hora` (`hora`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rela_audio`
--

CREATE TABLE IF NOT EXISTS `rela_audio` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `url` text NOT NULL,
  `activar` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2514 ;

-- --------------------------------------------------------

--
-- Table structure for table `rela_documentos`
--

CREATE TABLE IF NOT EXISTS `rela_documentos` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `url` text NOT NULL,
  `activar` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=502 ;

-- --------------------------------------------------------

--
-- Table structure for table `rela_galerias`
--

CREATE TABLE IF NOT EXISTS `rela_galerias` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `firma` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `hora` (`hora`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2250 ;

-- --------------------------------------------------------

--
-- Table structure for table `rela_susfotos`
--

CREATE TABLE IF NOT EXISTS `rela_susfotos` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `firma` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `hora` (`hora`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `secciones`
--

CREATE TABLE IF NOT EXISTS `secciones` (
  `nombre` varchar(250) NOT NULL default '',
  FULLTEXT KEY `nombre` (`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `secciones_portada`
--

CREATE TABLE IF NOT EXISTS `secciones_portada` (
  `nombre` varchar(250) NOT NULL default '',
  FULLTEXT KEY `nombre` (`nombre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `seguridad_registros`
--

CREATE TABLE IF NOT EXISTS `seguridad_registros` (
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `seccion` varchar(100) NOT NULL,
  `ip` varchar(250) NOT NULL,
  `ordenador` text NOT NULL,
  KEY `hora` (`hora`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sus_fotos`
--

CREATE TABLE IF NOT EXISTS `sus_fotos` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `autor` varchar(100) NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `texto` text NOT NULL,
  `foto` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `activar` (`activar`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5114 ;

-- --------------------------------------------------------

--
-- Table structure for table `topsecret`
--

CREATE TABLE IF NOT EXISTS `topsecret` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL default '0000-00-00',
  `hora` time NOT NULL default '00:00:00',
  `antetitulo` varchar(100) NOT NULL default '',
  `titulo` varchar(100) NOT NULL default '',
  `texto` longtext NOT NULL,
  `foto_peq` varchar(250) NOT NULL default '',
  `foto_gran` varchar(250) NOT NULL default '',
  `foto_ampliar` varchar(250) NOT NULL,
  `foto_texto` varchar(200) NOT NULL default '',
  `noti_relacion` varchar(250) NOT NULL,
  `opinion_relacion` varchar(250) NOT NULL,
  `documentos_relacion` varchar(250) NOT NULL,
  `audio_relacion` varchar(250) NOT NULL,
  `video_relacion` varchar(250) NOT NULL,
  `galeria_relacion` varchar(250) NOT NULL,
  `encuesta_relacion` varchar(250) NOT NULL,
  `video_youtube` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL default '0',
  `orden` int(99) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`),
  KEY `activar` (`activar`),
  KEY `orden` (`orden`),
  FULLTEXT KEY `antetitulo` (`antetitulo`,`titulo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=17483 ;

-- --------------------------------------------------------

--
-- Table structure for table `ultima_hora`
--

CREATE TABLE IF NOT EXISTS `ultima_hora` (
  `id_canal` int(11) NOT NULL,
  `hora` time NOT NULL,
  `texto` varchar(250) NOT NULL,
  `url` varchar(250) NOT NULL,
  `activar` int(1) NOT NULL,
  PRIMARY KEY  (`id_canal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `usuario` int(11) NOT NULL auto_increment,
  `nombre` varchar(250) NOT NULL default '',
  `apellidos` varchar(250) NOT NULL default '',
  `email` varchar(250) NOT NULL default '',
  `login` varchar(25) NOT NULL,
  `password` varchar(250) NOT NULL default '',
  `perfil` varchar(100) NOT NULL default '',
  `activo` int(1) NOT NULL default '0',
  `conectado` int(1) NOT NULL default '0',
  PRIMARY KEY  (`usuario`),
  KEY `activo` (`activo`),
  KEY `conectado` (`conectado`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE IF NOT EXISTS `videos` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `texto` text NOT NULL,
  `imagen` varchar(250) NOT NULL,
  `video` varchar(250) NOT NULL,
  `activar` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fecha` (`fecha`,`hora`),
  KEY `video` (`video`),
  KEY `activar` (`activar`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=131 ;

-- --------------------------------------------------------

--
-- Table structure for table `videos_portada`
--

CREATE TABLE IF NOT EXISTS `videos_portada` (
  `columna` int(11) NOT NULL,
  `orden` int(11) NOT NULL,
  `id_video` int(11) NOT NULL,
  `activar` int(11) NOT NULL,
  KEY `columna` (`columna`),
  KEY `orden` (`orden`),
  KEY `id_video` (`id_video`),
  KEY `activar` (`activar`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

