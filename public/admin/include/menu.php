<?php

$menuXml = '<?xml version="1.0"?>
<menu>
    <submenu title="'.htmlspecialchars(_("Frontpage"), ENT_QUOTES).'" link="#">
        <node
            module_name="FRONTPAGE_MANAGER"
            privilege="ARTICLE_FRONTPAGE"
            title="'.htmlspecialchars(_("Frontpage Manager"), ENT_QUOTES).'"
            link="article.php"
        />
        <node
            module_name="WIDGET_MANAGER"
            privilege="WIDGET_ADMIN"
            title="'.htmlspecialchars(_("Widget Manager"), ENT_QUOTES).'"
            link="controllers/widget/widget.php"
        />
    </submenu>

    <submenu title="'.htmlspecialchars(_("Contents"), ENT_QUOTES).'" link="#">
        <node
            module_name="ARTICLE_MANAGER"
            privilege="ARTICLE_LIST_PEND"
            title="'.htmlspecialchars(_("Articles"), ENT_QUOTES).'"
            link="article.php?action=list_pendientes"
        />
        <node
            module_name="OPINION_MANAGER"
            privilege="OPINION_ADMIN"
            title="'.htmlspecialchars(_("Opinions"), ENT_QUOTES).'"
            link="controllers/opinion/opinion.php"
        />
        <node
            module_name="COMMENT_MANAGER"
            privilege="COMMENT_ADMIN"
            title="'.htmlspecialchars(_("Comments"), ENT_QUOTES).'"
            link="controllers/comment/comment.php?action=list"
        />
        <node
            module_name="POLL_MANAGER"
            privilege="POLL_ADMIN"
            title="'.htmlspecialchars(_("Polls"), ENT_QUOTES).'"
            link="controllers/poll/poll.php"
        />
        <node
            module_name="ADS_MANAGER"
            privilege="ADVERTISEMENT_ADMIN"
            title="'.htmlspecialchars(_("Advertisements"), ENT_QUOTES).'"
            link="controllers/advertisement/advertisement.php"
        />
        <node
            module_name="STATIC_PAGES_MANAGER"
            privilege="STATIC_ADMIN"
            title="'.htmlspecialchars(_("Static Pages"), ENT_QUOTES).'"
            link="controllers/static_pages/static_pages.php"
        />
        <node
            module_name="ARTICLE_MANAGER"
            privilege="ARCHIVE_ADMIN"
            title="'.htmlspecialchars(_("Library"), ENT_QUOTES).'"
            link="article.php?action=list_hemeroteca"
        />
        <node
            module_name="CATEGORY_MANAGER"
            privilege="CATEGORY_ADMIN"
            title="'.htmlspecialchars(_("Category Manager"), ENT_QUOTES).'"
            link="controllers/category/category.php"
        />
     </submenu>

    <submenu title="'.htmlspecialchars(_("Media"), ENT_QUOTES).'" link="#" privilege="IMAGE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,FILE_ADMIN">
        <node
            module_name="IMAGE_MANAGER"
            privilege="IMAGE_ADMIN"
            title="'.htmlspecialchars(_("Images"), ENT_QUOTES).'"
            link="controllers/mediamanager/mediamanager.php"
        />
        <node
            module_name="FILE_MANAGER"
            privilege="FILE_ADMIN"
            title="'.htmlspecialchars(_("Files"), ENT_QUOTES).'"
            link="controllers/files/files.php"
        />
        <node
            module_name="VIDEO_MANAGER"
            privilege="VIDEO_ADMIN"
            title="'.htmlspecialchars(_("Videos"), ENT_QUOTES).'"
            link="controllers/video/video.php"
        />
        <node
            module_name="ALBUM_MANAGER"
            privilege="ALBUM_ADMIN"
            title="'.htmlspecialchars(_("Albums"), ENT_QUOTES).'"
            link="controllers/album/album.php"
        />
    </submenu>


    <submenu title="'.htmlspecialchars(_("Users & Groups"), ENT_QUOTES).'" link="#" privilege="USER_ADMIN">
        <node
            module_name="USER_MANAGER"
            privilege="USER_ADMIN"
            title="'.htmlspecialchars(_("Users"), ENT_QUOTES).'"
            link="controllers/acl/user.php"
        />
        <node
            module_name="USER_GROUP_MANAGER"
            privilege="USER_ADMIN"
            title="'.htmlspecialchars(_("User Groups"), ENT_QUOTES).'"
            link="controllers/acl/user_groups.php"
        />
        <node
            module_name="PRIVILEGE_MANAGER"
            privilege="USER_ADMIN"
            title="'.htmlspecialchars(_("Privileges"), ENT_QUOTES).'"
            link="controllers/acl/privileges.php"
        />
    </submenu>

    <submenu title="'.htmlspecialchars(_("Utilities"), ENT_QUOTES).'" link="#" privilege="BACKEND_ADMIN,CACHE_ADMIN,SEARCH_ADMIN,TRASH_ADMIN,PCLAVE_ADMIN,EP_IMPORTER_ADMIN">
        <node
            module_name="ADVANCED_SEARCH"
            privilege="SEARCH_ADMIN"
            title="'.htmlspecialchars(_("Advanced Search"), ENT_QUOTES).'"
            link="controllers/search_advanced/search_advanced.php"
        />
        <node
            module_name="KIOSKO_MANAGER"
            title="'.htmlspecialchars(_("News Stand"), ENT_QUOTES).'"
            link="controllers/newsstand/newsstand.php"
            privilege="KIOSKO_ADMIN" />
        <node
            module_name="NEWSLETTER_MANAGER"
            title="'.htmlspecialchars(_("Newsletter"), ENT_QUOTES).'"
            link="controllers/newsletter/newsletter.php"
            privilege="NEWSLETTER_ADMIN" />
        <node
            module_name="KEYWORD_MANAGER"
            title="'.htmlspecialchars(_("Keywords"), ENT_QUOTES).'"
            link="controllers/keywords/keywords.php"
            privilege="PCLAVE_ADMIN" />
        <node
            title="&lt;hr/&gt;"
            link="javascript:return false;"
            privilege="BACKEND_ADMIN" />
        <node
            module_name="EUROPAPRESS_IMPORTER"
            title="'.htmlspecialchars(_("EuropaPress importer"), ENT_QUOTES).'"
            link="controllers/agency_importer/europapress.php"
            privilege="IMPORT_EPRESS" />
        <node
            module_name="PAPER_IMPORT"
            privilege="IMPORT_EFE"
            title="'.htmlspecialchars(_("EFE Importer"), ENT_QUOTES).'"
            link="article.php?action=list_agency"
        />
        <node
            module_name="PAPER_IMPORT"
            privilege="IMPORT_XML"
            title="'.htmlspecialchars(_("XML Importer"), ENT_QUOTES).'"
            link="importXML.php?action=info"
        />
        <node
            title="&lt;hr/&gt;"
            link="javascript:return false;"
            privilege="BACKEND_ADMIN" />
        <node
            module_name="TRASH_MANAGER"
            title="'.htmlspecialchars(_("Trash"), ENT_QUOTES).'"
            link="controllers/trash.php"
            privilege="NOT_ADMIN" />
        <node
            module_name="LINK_CONTROL_MANAGER"
            title="'.htmlspecialchars(_("Link control"), ENT_QUOTES).'"
            link="controllers/link_control/link_control.php"
            privilege="BACKEND_ADMIN" />
        <node
            title="&lt;hr/&gt;"
            link="javascript:return false;"
            privilege="BACKEND_ADMIN" />
        <node
            module_name="ONM_STATISTICS"
            title="'.htmlspecialchars(_("Statistics"), ENT_QUOTES).'"
            link="controllers/statistics/statistics.php"
            privilege="BACKEND_ADMIN" />
    </submenu>

    <submenu title="'.htmlspecialchars(_("System"), ENT_QUOTES).'" link="#" privilege="SETTINGS_MANAGER,CACHE_ADMIN,SYSTEM_UPDATE_MANAGER,BACKEND_ADMIN">
        <node
            module_name="SETTINGS_MANAGER"
            title="'.htmlspecialchars(_("System settings"), ENT_QUOTES).'"
            link="controllers/system_settings/system_settings.php"
            privilege="ONM_SETTINGS" />
        <node
            module_name="CACHE_MANAGER"
            title="'.htmlspecialchars(_("Cache Manager"), ENT_QUOTES).'"
            link="controllers/tpl_manager/tpl_manager.php"
            privilege="CACHE_ADMIN" />
        <node
            module_name="SYSTEM_UPDATE_MANAGER"
            title="'.htmlspecialchars(_("Update System"), ENT_QUOTES).'"
            link="controllers/updatesystem/index.php"
            privilege="BACKEND_ADMIN" />
        <node
            module_name="PHP_CACHE_MANAGER"
            title="'.htmlspecialchars(_("PHP Cache Manager"), ENT_QUOTES).'"
            link="controllers/system_information/apc.php"
            privilege="CACHE_ADMIN" />
        <node
            module_name="MYSQL_MANAGER"
            title="'.htmlspecialchars(_("Database Manager"), ENT_QUOTES).'"
            link="controllers/system_information/mysql-check.php?action=check"
            privilege="BACKEND_ADMIN" />
        <node
            title="'.htmlspecialchars(_("Support and Help"), ENT_QUOTES).'"
            link="http://www.openhost.es/"
            privilege="BACKEND_ADMIN" />
    </submenu>
</menu>';
