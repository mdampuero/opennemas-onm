<?php

$menuXml = '<?xml version="1.0"?>
<menu>
    <submenu title="'.htmlspecialchars(_("Frontpage"), ENT_QUOTES).'" link="index.php" target="centro">
        <node title="'.htmlspecialchars(_("Inicio"), ENT_QUOTES).'" link="index.php" target="centro" />
        <node title="'.htmlspecialchars(_("Frontpage Manager"), ENT_QUOTES).'" link="article.php" target="centro" privilege="ARTICLE_FRONTPAGE" />
        <node title="'.htmlspecialchars(_("Widget Manager"), ENT_QUOTES).'" link="controllers/widget/widget.php" target="centro" privilege="WIDGET_ADMIN" />
        <!--<node title="'.htmlspecialchars(_("Templates Manager"), ENT_QUOTES).'" link="index.php" target="centro" privilege="ARTICLE_FRONTPAGE" />-->
    </submenu>

    <submenu title="'.htmlspecialchars(_("Contents"), ENT_QUOTES).'" link="article.php" target="centro">
        <node title="'.htmlspecialchars(_("Articles"), ENT_QUOTES).'" link="article.php?action=list_pendientes" target="centro" privilege="ARTICLE_LIST_PEND" />
        <node title="'.htmlspecialchars(_("Opinions"), ENT_QUOTES).'" link="controllers/opinion/opinion.php" target="centro" privilege="OPINION_ADMIN" />
        <node title="'.htmlspecialchars(_("Comments"), ENT_QUOTES).'" link="controllers/comment.php" target="centro" privilege="COMMENT_ADMIN" />
        <!--<node title="'.htmlspecialchars(_("Polls"), ENT_QUOTES).'" link="poll.php" target="centro" privilege="POLL_ADMIN" />-->
        <node title="'.htmlspecialchars(_("Advertisements"), ENT_QUOTES).'" link="controllers/advertisement/advertisement.php" target="centro" privilege="ADVERTISEMENT_ADMIN" />
        <node title="'.htmlspecialchars(_("Static Pages"), ENT_QUOTES).'" link="static_pages.php" target="centro" privilege="STATIC_ADMIN" />
        <node title="'.htmlspecialchars(_("Library"), ENT_QUOTES).'" link="article.php?action=list_hemeroteca" target="centro" privilege="ARCHIVE_ADMIN" />
        <node title="'.htmlspecialchars(_("Agency Manager"), ENT_QUOTES).'" link="article.php?action=list_agency" target="centro" privilege="ARTICLE_LIST_PEND" />
        <node title="'.htmlspecialchars(_("Sections Manager"), ENT_QUOTES).'" link="controllers/category/category.php" target="centro" privilege="CATEGORY_ADMIN" />
     </submenu>

    <submenu title="'.htmlspecialchars(_("Media"), ENT_QUOTES).'" link="mediamanager.php" target="centro" privilege="IMAGE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,FILE_ADMIN">
        <node title="'.htmlspecialchars(_("Images"), ENT_QUOTES).'" link="mediamanager.php" target="centro" privilege="IMAGE_ADMIN" />
        <node title="'.htmlspecialchars(_("Files"), ENT_QUOTES).'" link="controllers/files/files.php" target="centro" privilege="FILE_ADMIN" />
        <!--<node title="'.htmlspecialchars(_("Videos"), ENT_QUOTES).'" link="controllers/video/video.php" target="centro" privilege="VIDEO_ADMIN" />-->
        <!--<node title="'.htmlspecialchars(_("Albums"), ENT_QUOTES).'" link="controllers/album/album.php" target="centro" privilege="ALBUM_ADMIN" />-->
    </submenu>


    <submenu title="'.htmlspecialchars(_("Users"), ENT_QUOTES).'" link="user.php" target="centro" privilege="USER_ADMIN">
        <node title="'.htmlspecialchars(_("Users"), ENT_QUOTES).'" link="user.php" target="centro" />
        <node title="'.htmlspecialchars(_("User Groups"), ENT_QUOTES).'" link="user_groups.php" target="centro" />
        <node title="'.htmlspecialchars(_("Privileges"), ENT_QUOTES).'" link="privileges.php" target="centro" />
    </submenu>

    <submenu title="'.htmlspecialchars(_("Utilities"), ENT_QUOTES).'" link="search_advanced.php" target="centro" privilege="BACKEND_ADMIN,CACHE_ADMIN,SEARCH_ADMIN,TRASH_ADMIN,PCLAVE_ADMIN">
        <node title="'.htmlspecialchars(_("Advanced Search"), ENT_QUOTES).'" link="search_advanced.php" target="centro" privilege="SEARCH_ADMIN" />
        <node title="'.htmlspecialchars(_("News Stand"), ENT_QUOTES).'" link="kiosko.php" target="centro" privilege="CATEGORY_ADMIN" />
        <node title="'.htmlspecialchars(_("Newsletter"), ENT_QUOTES).'" link="newsletter.php" target="centro" privilege="NEWSLETTER_ADMIN" />
        <node title="'.htmlspecialchars(_("Keywords"), ENT_QUOTES).'" link="pclave.php" target="centro" privilege="PCLAVE_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Trash"), ENT_QUOTES).'" link="controllers/trash.php" target="centro" privilege="NOT_ADMIN" />
        <node title="'.htmlspecialchars(_("Link control"), ENT_QUOTES).'" link="controllers/link_control.php" target="centro" privilege="BACKEND_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Statistics"), ENT_QUOTES).'" link="dashboard.php" target="centro" privilege="BACKEND_ADMIN" />
        <!--<node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Check PHP cache"), ENT_QUOTES).'" link="index.php" target="centro" privilege="CACHE_ADMIN" />
        <node title="'.htmlspecialchars(_("Check database integrity"), ENT_QUOTES).'" link="mysql-check.php?action=check" target="centro" privilege="BACKEND_ADMIN" />-->
    </submenu>

    <submenu title="'.htmlspecialchars(_("Configuration"), ENT_QUOTES).'" link="configurator.php" target="centro" privilege="CACHE_ADMIN,BACKEND_ADMIN">
        <node title="'.htmlspecialchars(_("System settings"), ENT_QUOTES).'" link="configurator.php" target="centro"  privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Cache Manager"), ENT_QUOTES).'" link="tpl_manager.php" target="centro" privilege="CACHE_ADMIN" />
        <!--<node title="&lt;hr/&gt;" link="javascript:return false;" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Update System"), ENT_QUOTES).'" link="update-system.php" target="centro"  privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Information"), ENT_QUOTES).'" link="index.php" target="centro" privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Help"), ENT_QUOTES).'" link="http://www.openhost.es/en/opennemas" target="centro" privilege="BACKEND_ADMIN" />-->
    </submenu>
</menu>';
