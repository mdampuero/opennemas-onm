<?php

$menuXml = <<<MENUSTRING
<?xml version="1.0"?>
<menu>
    <submenu title="Frontpage" link="index.php" target="centro">
        <node title="Inicio" link="index.php" target="centro" />
        <node title="Frontpage Manager" link="article.php" target="centro" privilege="ARTICLE_FRONTPAGE" />
        <node title="Widget Manager" link="widget.php" target="centro" privilege="WIDGET_ADMIN" />
        <node title="Templates Manager" link="index.php" target="centro" privilege="ARTICLE_FRONTPAGE" />
    </submenu>
    
    <submenu title="Contents" link="article.php" target="centro">
        <node title="Articles" link="article.php?action=list_pendientes" target="centro" privilege="ARTICLE_LIST_PEND" />
        <node title="Opinions" link="opinion.php" target="centro" privilege="OPINION_ADMIN" />
        <node title="Comments" link="comment.php" target="centro" privilege="COMMENT_ADMIN" />
        <node title="Polls" link="poll.php" target="centro" privilege="POLL_ADMIN" />
        <node title="Advertisements" link="advertisement.php" target="centro" privilege="ADVERTISEMENT_ADMIN" />
        <node title="Static Pages" link="static_pages.php" target="centro" privilege="STATIC_ADMIN" />
        <node title="Library" link="article.php?action=list_hemeroteca" target="centro" privilege="ARCHIVE_ADMIN" />
        <node title="Agency Manager" link="article.php?action=list_agency" target="centro" privilege="ARTICLE_LIST_PEND" />
        <node title="Sections Manager" link="category.php" target="centro" privilege="CATEGORY_ADMIN" />
     </submenu>

    <submenu title="Media" link="mediamanager.php" target="centro" privilege="IMAGE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,FILE_ADMIN">
        <node title="Images" link="mediamanager.php" target="centro" privilege="IMAGE_ADMIN" />
        <node title="Files" link="ficheros.php" target="centro" privilege="FILE_ADMIN" />
        <node title="Videos" link="video.php" target="centro" privilege="VIDEO_ADMIN" />
        <node title="Albums" link="album.php" target="centro" privilege="ALBUM_ADMIN" />
    </submenu>


    <submenu title="Users" link="user.php" target="centro" privilege="USER_ADMIN">
        <node title="Users" link="user.php" target="centro" />
        <node title="Group Users" link="user_groups.php" target="centro" />
        <node title="Permissions" link="privileges.php" target="centro" />
    </submenu>

    <submenu title="Utilities" link="search_advanced.php" target="centro" privilege="BACKEND_ADMIN,CACHE_ADMIN,SEARCH_ADMIN,TRASH_ADMIN,PCLAVE_ADMIN">
        <node title="Avanced Search" link="search_advanced.php" target="centro" privilege="SEARCH_ADMIN" />
        <node title="Newsstand" link="kiosko.php" target="centro" privilege="CATEGORY_ADMIN" />
        <node title="Newsletter" link="newsletter.php" target="centro" privilege="NEWSLETTER_ADMIN" />
        <node title="Key words" link="pclave.php" target="centro" privilege="PCLAVE_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="Trash" link="litter.php" target="centro" privilege="NOT_ADMIN" />
        <node title="Control link" link="link_control.php" target="centro" privilege="BACKEND_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="Statistics" link="dashboard.php" target="centro" privilege="BACKEND_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="Check PHP cache" link="index.php" target="centro" privilege="CACHE_ADMIN" />
        <node title="Check database integrity" link="mysql-check.php?action=check" target="centro" privilege="BACKEND_ADMIN" />
    </submenu>

    <submenu title="Configuration" link="configurator.php" target="centro" privilege="CACHE_ADMIN,BACKEND_ADMIN">
        <node title="System settings" link="configurator.php" target="centro"  privilege="BACKEND_ADMIN" />
        <node title="Cache Manager" link="tpl_manager.php" target="centro" privilege="CACHE_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="Update System" link="svn.php" target="centro"  privilege="BACKEND_ADMIN" />
        <node title="Information" link="index.php" target="centro" privilege="BACKEND_ADMIN" />
        <node title="Help" link="http://www.openhost.es/en/opennemas" target="centro" privilege="BACKEND_ADMIN" />
        
    </submenu>
</menu>
MENUSTRING;

