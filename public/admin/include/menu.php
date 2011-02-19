<?php

$menuXml = '<?xml version="1.0"?>
<menu>
    <submenu title="'._("Frontpage").'" link="index.php" target="centro">
        <node title="'._("Inicio").'" link="index.php" target="centro" />
        <node title="'._("Frontpage Manager").'" link="article.php" target="centro" privilege="ARTICLE_FRONTPAGE" />
        <node title="'._("Widget Manager").'" link="controllers/widget/widget.php" target="centro" privilege="WIDGET_ADMIN" />
        <!--<node title="'._("Templates Manager").'" link="index.php" target="centro" privilege="ARTICLE_FRONTPAGE" />-->
    </submenu>

    <submenu title="'._("Contents").'" link="article.php" target="centro">
        <node title="'._("Articles").'" link="article.php?action=list_pendientes" target="centro" privilege="ARTICLE_LIST_PEND" />
        <node title="'._("Opinions").'" link="controllers/opinion/opinion.php" target="centro" privilege="OPINION_ADMIN" />
        <node title="'._("Comments").'" link="controllers/comment.php" target="centro" privilege="COMMENT_ADMIN" />
        <!--<node title="'._("Polls").'" link="poll.php" target="centro" privilege="POLL_ADMIN" />-->
        <node title="'._("Advertisements").'" link="controllers/advertisement/advertisement.php" target="centro" privilege="ADVERTISEMENT_ADMIN" />
        <node title="'._("Static Pages").'" link="static_pages.php" target="centro" privilege="STATIC_ADMIN" />
        <node title="'._("Library").'" link="article.php?action=list_hemeroteca" target="centro" privilege="ARCHIVE_ADMIN" />
        <node title="'._("Agency Manager").'" link="article.php?action=list_agency" target="centro" privilege="ARTICLE_LIST_PEND" />
        <node title="'._("Sections Manager").'" link="category.php" target="centro" privilege="CATEGORY_ADMIN" />
     </submenu>

    <submenu title="'._("Media").'" link="mediamanager.php" target="centro" privilege="IMAGE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,FILE_ADMIN">
        <node title="'._("Images").'" link="mediamanager.php" target="centro" privilege="IMAGE_ADMIN" />
        <node title="'._("Files").'" link="ficheros.php" target="centro" privilege="FILE_ADMIN" />
        <!--<node title="'._("Videos").'" link="controllers/video/video.php" target="centro" privilege="VIDEO_ADMIN" />-->
        <!--<node title="'._("Albums").'" link="controllers/album/album.php" target="centro" privilege="ALBUM_ADMIN" />-->
    </submenu>


    <submenu title="'._("Users").'" link="user.php" target="centro" privilege="USER_ADMIN">
        <node title="'._("Users").'" link="user.php" target="centro" />
        <node title="'._("User Groups").'" link="user_groups.php" target="centro" />
        <node title="'._("Privileges").'" link="privileges.php" target="centro" />
    </submenu>

    <submenu title="'._("Utilities").'" link="search_advanced.php" target="centro" privilege="BACKEND_ADMIN,CACHE_ADMIN,SEARCH_ADMIN,TRASH_ADMIN,PCLAVE_ADMIN">
        <node title="'._("Advanced Search").'" link="search_advanced.php" target="centro" privilege="SEARCH_ADMIN" />
        <node title="'._("News Stand").'" link="kiosko.php" target="centro" privilege="CATEGORY_ADMIN" />
        <node title="'._("Newsletter").'" link="newsletter.php" target="centro" privilege="NEWSLETTER_ADMIN" />
        <node title="'._("Keywords").'" link="pclave.php" target="centro" privilege="PCLAVE_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'._("Trash").'" link="controllers/trash.php" target="centro" privilege="NOT_ADMIN" />
        <node title="'._("Link control").'" link="controllers/link_control.php" target="centro" privilege="BACKEND_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'._("Statistics").'" link="dashboard.php" target="centro" privilege="BACKEND_ADMIN" />
        <!--<node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'._("Check PHP cache").'" link="index.php" target="centro" privilege="CACHE_ADMIN" />
        <node title="'._("Check database integrity").'" link="mysql-check.php?action=check" target="centro" privilege="BACKEND_ADMIN" />-->
    </submenu>

    <submenu title="'._("Configuration").'" link="configurator.php" target="centro" privilege="CACHE_ADMIN,BACKEND_ADMIN">
        <node title="'._("System settings").'" link="configurator.php" target="centro"  privilege="BACKEND_ADMIN" />
        <node title="'._("Cache Manager").'" link="tpl_manager.php" target="centro" privilege="CACHE_ADMIN" />
        <!--<node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'._("Update System").'" link="update-system.php" target="centro"  privilege="BACKEND_ADMIN" />
        <node title="'._("Information").'" link="index.php" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'._("Help").'" link="http://www.openhost.es/en/opennemas" target="centro" privilege="BACKEND_ADMIN" />-->
    </submenu>
</menu>';
