<?php

$menuXml = <<<MENUSTRING
<?xml version="1.0"?>
<menu>
    <submenu title="Frontpage">
        <node title="Home" route="panel-index" />
        <node title="Page Manager" route="page-index" privilege="ARTICLE_FRONTPAGE" />
        <node title="Widget Manager" route="widget-index" privilege="WIDGET_ADMIN" />
        <node title="Templates Manager" link="index.php" privilege="ARTICLE_FRONTPAGE" />
    </submenu>
    
    <submenu title="Contents" link="index.php" target="centro">
        <node title="Articles" link="article.php?action=list_pendientes" privilege="ARTICLE_ADMINPEND" />
        <node title="Opinions" link="opinion.php" privilege="OPINION_ADMIN" />
        <node title="Comments" link="comment.php" privilege="COMMENT_ADMIN" />
        <node title="Polls" link="poll.php" privilege="POLL_ADMIN" />
        <node title="Advertisements" link="advertisement.php" privilege="ADVERTISEMENT_ADMIN" />        
        <node title="Static Pages" route="staticpage-index" privilege="STATIC_ADMIN" />
        <node title="Library" link="article.php?action=list_hemeroteca" privilege="ARCHIVE_ADMIN" />
        <node title="Sections Manager" route="category-index" privilege="WIDGET_ADMIN" />        
    </submenu>

    <submenu title="Media" link="#" privilege="IMAGE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,FILE_ADMIN,GRAPHICS_ADMIN">
        <node title="Images" link="mediamanager.php" privilege="IMAGE_ADMIN" />
        <node title="Files" link="ficheros.php" privilege="FILE_ADMIN" />
        <node title="Videos" link="video.php" privilege="VIDEO_ADMIN" />
        <node title="Albums" link="album.php" privilege="ALBUM_ADMIN" />
    </submenu>
    

    <submenu title="Users" link="#" privilege="USER_ADMIN">
        <node title="Users" link="user.php" />
        <node title="Group Users" link="user_groups.php" />
        <node title="Permissions" link="privileges.php" />
    </submenu>

    <submenu title="Utilities" link="#" privilege="BACKEND_ADMIN,CACHE_ADMIN,SEARCH_ADMIN,TRASH_ADMIN,PCLAVE_ADMIN">
        <node title="Avanced Search" link="search_advanced.php" privilege="SEARCH_ADMIN" />        
        <node title="Trash" link="litter.php" privilege="TRASH_ADMIN" />
        <node title="Newsstand" link="kiosko.php" privilege="CATEGORY_ADMIN" />
        <node title="Newsletter" link="newsletter.php" privilege="NEWSLETTER_ADMIN" />
        <node title="Key words"  route="keyword-keyword-index" privilege="PCLAVE_ADMIN" />
        <node title="Control link" link="link_control.php" privilege="BACKEND_ADMIN" />
        <node title="Dashboard" link="dashboard.php" privilege="BACKEND_ADMIN" />
    </submenu>
    
    <submenu title="Configuration" link="#" privilege="BACKEND_ADMIN,CACHE_ADMIN">
        <node title="Settings Manager" link="configurator.php"  privilege="BACKEND_ADMIN" />
        <node title="Cache Manager" link="tpl_manager.php" privilege="CACHE_ADMIN" />
        <node title="Information" link="index.php" privilege="BACKEND_ADMIN" />
        <node title="Help" link="index.php" privilege="BACKEND_ADMIN" />
        <node title="APC" link="index.php" privilege="CACHE_ADMIN" />
        <node title="SVN" route="svn-index"  privilege="BACKEND_ADMIN" />
        <node title="Mysql-Check" link="mysql-check.php?action=check" privilege="BACKEND_ADMIN" />
    </submenu>    
</menu>
MENUSTRING;

