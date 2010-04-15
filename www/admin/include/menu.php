<?php

$menuXml = <<<MENUSTRING
<?xml version="1.0"?>
<menu>
    <submenu title="Contenidos" link="index.php" target="centro">
        <node title="Inicio" link="index.php" target="centro" />
        <node title="Gestor de Portada" link="article.php" target="centro" privilege="ARTICLE_FRONTPAGE" />
        <node title="Gestor de Pendientes" link="article.php?action=list_pendientes" target="centro" privilege="ARTICLE_ADMINPEND" />
        <node title="Gestor de Comentarios" link="comment.php" target="centro" privilege="COMMENT_ADMIN" />
       <!--  <node title="Gestor de Opinión" link="opinion.php" target="centro" privilege="OPINION_ADMIN" /> -->
        <node title="Gestor de Publicidad" link="advertisement.php" target="centro" privilege="ADVERTISEMENT_ADMIN" />
        <node title="Hemeroteca" link="article.php?action=list_hemeroteca" target="centro" privilege="ARCHIVE_ADMIN" />
        <node title="Páginas Estáticas" link="static_pages.php" target="centro" privilege="STATIC_ADMIN" />
        <node title="Gestor Widgets" link="widget.php" target="centro" privilege="WIDGET_ADMIN" />
        <node title="Portadas papel" link="kiosko.php" target="centro" privilege="FILE_ADMIN" />
    </submenu>

    <submenu title="Multimedia" link="#" target="centro" privilege="IMAGE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,FILE_ADMIN,GRAPHICS_ADMIN">
        <node title="Imágenes" link="mediamanager.php" target="centro" privilege="IMAGE_ADMIN" />
        <node title="Vídeos" link="video.php" target="centro" privilege="VIDEO_ADMIN" />
        <node title="Álbum" link="album.php" target="centro" privilege="ALBUM_ADMIN" />
        <node title="Ficheros" link="ficheros.php" target="centro" privilege="FILE_ADMIN" />
        <node title="Gráficos" link="mediagraficos.php" target="centro" privilege="GRAPHICS_ADMIN" />
    </submenu>
    
    <submenu title="Secciones" link="category.php" target="centro" privilege="CATEGORY_ADMIN"></submenu>
    
    <submenu title="Usuarios" link="#" target="centro" privilege="USER_ADMIN">
        <node title="Usuarios" link="user.php" target="centro" />
        <node title="Grupos de Usuarios" link="user_groups.php" target="centro" />
        <node title="Permisos" link="privileges.php" target="centro" />
        <node title="Autores" link="author.php" target="centro" />
    </submenu>
    
    <submenu title="Boletín" link="#" target="centro" privilege="NEWSLETTER_ADMIN">
        <node title="Envío de boletín" link="newsletter.php" target="centro" />        
    </submenu>
    
    <submenu title="Utiles" link="#" target="centro" privilege="BACKEND_ADMIN,CACHE_ADMIN,SEARCH_ADMIN,TRASH_ADMIN,PCLAVE_ADMIN">
        <node link="search_advanced.php" target="centro" title="Busqueda Avanzada" privilege="SEARCH_ADMIN" />
        <node link="pclave.php" target="centro" title="Palabras clave" privilege="PCLAVE_ADMIN" />
        <node link="link_control.php" target="centro" title="Control link" privilege="BACKEND_ADMIN" />
        <node link="litter.php" target="centro" title="Papelera" privilege="TRASH_ADMIN" />
        <node link="http://piwik.xornal.com" target="centro" title="Webstats" privilege="BACKEND_ADMIN" />
        <node link="dashboard.php" target="centro" title="Dashboard" privilege="BACKEND_ADMIN" />
    </submenu>
    
    <submenu title="Configuración" link="#" target="centro" privilege="BACKEND_ADMIN,CACHE_ADMIN">
        <node link="configurator.php" target="centro" title="Config Manager" privilege="BACKEND_ADMIN" />
        <node link="tpl_manager.php" target="centro" title="Cache Manager" privilege="CACHE_ADMIN" />
        <node link="apc.php" target="centro" title="APC" privilege="CACHE_ADMIN" />
        <node link="svn.php" target="centro" title="SVN" privilege="BACKEND_ADMIN" />
        <node link="mysql-check.php?action=check" target="centro" title="Mysql-Check" privilege="BACKEND_ADMIN" />
    </submenu>    
</menu>
MENUSTRING;

