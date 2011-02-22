<?php

$menuXml = '<?xml version="1.0"?>
<menu>
    <submenu title="'.htmlspecialchars(_("Frontpage"), ENT_QUOTES).'" link="index.php" target="centro">
        <node title="'.htmlspecialchars(_("Frontpage Manager"), ENT_QUOTES).'" link="article.php" privilege="ARTICLE_FRONTPAGE" />
        <node title="'.htmlspecialchars(_("Widget Manager"), ENT_QUOTES).'" link="controllers/widget/widget.php" privilege="WIDGET_ADMIN" />
        <!--<node title="'.htmlspecialchars(_("Templates Manager"), ENT_QUOTES).'" link="index.php" privilege="ARTICLE_FRONTPAGE" />-->
    </submenu>

    <submenu title="'.htmlspecialchars(_("Contents"), ENT_QUOTES).'" link="#" target="centro">
        <node title="'.htmlspecialchars(_("Articles"), ENT_QUOTES).'" link="article.php?action=list_pendientes" privilege="ARTICLE_LIST_PEND" />
        <node title="'.htmlspecialchars(_("Opinions"), ENT_QUOTES).'" link="controllers/opinion/opinion.php" privilege="OPINION_ADMIN" />
        <node title="'.htmlspecialchars(_("Comments"), ENT_QUOTES).'" link="controllers/comment.php" privilege="COMMENT_ADMIN" />
        <!--<node title="'.htmlspecialchars(_("Polls"), ENT_QUOTES).'" link="poll.php" privilege="POLL_ADMIN" />-->
        <node title="'.htmlspecialchars(_("Advertisements"), ENT_QUOTES).'" link="controllers/advertisement/advertisement.php" privilege="ADVERTISEMENT_ADMIN" />
        <node title="'.htmlspecialchars(_("Static Pages"), ENT_QUOTES).'" link="static_pages.php" privilege="STATIC_ADMIN" />
        <node title="'.htmlspecialchars(_("Library"), ENT_QUOTES).'" link="article.php?action=list_hemeroteca" privilege="ARCHIVE_ADMIN" />
        <!--<node title="'.htmlspecialchars(_("Agency Manager"), ENT_QUOTES).'" link="article.php?action=list_agency" privilege="ARTICLE_LIST_PEND" />-->
        <node title="'.htmlspecialchars(_("Sections Manager"), ENT_QUOTES).'" link="controllers/category/category.php" privilege="CATEGORY_ADMIN" />
     </submenu>

    <submenu title="'.htmlspecialchars(_("Media"), ENT_QUOTES).'" link="controllers/mediamanager/mediamanager.php" privilege="IMAGE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,FILE_ADMIN">
        <node title="'.htmlspecialchars(_("Images"), ENT_QUOTES).'" link="controllers/mediamanager/mediamanager.php" privilege="IMAGE_ADMIN" />
        <node title="'.htmlspecialchars(_("Files"), ENT_QUOTES).'" link="controllers/files/files.php" privilege="FILE_ADMIN" />
        <!--<node title="'.htmlspecialchars(_("Videos"), ENT_QUOTES).'" link="controllers/video/video.php" privilege="VIDEO_ADMIN" />-->
        <!--<node title="'.htmlspecialchars(_("Albums"), ENT_QUOTES).'" link="controllers/album/album.php" privilege="ALBUM_ADMIN" />-->
    </submenu>


    <submenu title="'.htmlspecialchars(_("Users"), ENT_QUOTES).'" link="user.php" privilege="USER_ADMIN">
        <node title="'.htmlspecialchars(_("Users"), ENT_QUOTES).'" link="user.php" />
        <node title="'.htmlspecialchars(_("User Groups"), ENT_QUOTES).'" link="user_groups.php" />
        <node title="'.htmlspecialchars(_("Privileges"), ENT_QUOTES).'" link="privileges.php" />
    </submenu>

    <submenu title="'.htmlspecialchars(_("Utilities"), ENT_QUOTES).'" link="search_advanced.php" privilege="BACKEND_ADMIN,CACHE_ADMIN,SEARCH_ADMIN,TRASH_ADMIN,PCLAVE_ADMIN">
        <node title="'.htmlspecialchars(_("Advanced Search"), ENT_QUOTES).'" link="search_advanced.php" privilege="SEARCH_ADMIN" />
        <node title="'.htmlspecialchars(_("News Stand"), ENT_QUOTES).'" link="kiosko.php" privilege="CATEGORY_ADMIN" />
        <node title="'.htmlspecialchars(_("Newsletter"), ENT_QUOTES).'" link="newsletter.php" privilege="NEWSLETTER_ADMIN" />
        <node title="'.htmlspecialchars(_("Keywords"), ENT_QUOTES).'" link="pclave.php" privilege="PCLAVE_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Trash"), ENT_QUOTES).'" link="controllers/trash.php" privilege="NOT_ADMIN" />
        <node title="'.htmlspecialchars(_("Link control"), ENT_QUOTES).'" link="controllers/link_control.php" privilege="BACKEND_ADMIN" />
        <node title="&lt;hr/&gt;" link="javascript:return false;" privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Statistics"), ENT_QUOTES).'" link="dashboard.php" privilege="BACKEND_ADMIN" />
        <!--<node title="&lt;hr/&gt;" link="javascript:return false;" privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Check PHP cache"), ENT_QUOTES).'" link="index.php" privilege="CACHE_ADMIN" />
        <node title="'.htmlspecialchars(_("Check database integrity"), ENT_QUOTES).'" link="mysql-check.php?action=check" privilege="BACKEND_ADMIN" />-->
    </submenu>

    <submenu title="'.htmlspecialchars(_("Configuration"), ENT_QUOTES).'" link="configurator.php" privilege="CACHE_ADMIN,BACKEND_ADMIN">
        <node title="'.htmlspecialchars(_("System settings"), ENT_QUOTES).'" link="configurator.php"  privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Cache Manager"), ENT_QUOTES).'" link="tpl_manager.php" privilege="CACHE_ADMIN" />
        <!--<node title="&lt;hr/&gt;" link="javascript:return false;" privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Update System"), ENT_QUOTES).'" link="update-system.php"  privilege="BACKEND_ADMIN" />
        <node title="'.htmlspecialchars(_("Information"), ENT_QUOTES).'" link="index.php" privilege="BACKEND_ADMIN" />-->
        <node title="'.htmlspecialchars(_("Support and Help"), ENT_QUOTES).'" link="http://www.openhost.es/" privilege="BACKEND_ADMIN" />
    </submenu>
</menu>';
