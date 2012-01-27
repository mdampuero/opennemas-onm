<?php

$menuXml = '<?xml version="1.0"?>
<menu>
    <submenu title="'.htmlspecialchars(_("Web site"), ENT_QUOTES).'" id="frontpage" link="#">
        <node
            module_name="FRONTPAGE_MANAGER"
            privilege="ARTICLE_FRONTPAGE"
            title="'.htmlspecialchars(_("Frontpage Manager"), ENT_QUOTES).'"
            id="frontpage_manager"
            link="article.php"
        />
        <node
            module_name="STATIC_PAGES_MANAGER"
            privilege="STATIC_ADMIN"
            title="'.htmlspecialchars(_("Static Pages"), ENT_QUOTES).'"
            id="static_pages_manager"
            link="controllers/static_pages/static_pages.php"
        />
        <node class="divider" />
        <node
            module_name="MENU_MANAGER"
            privilege="MENU_ADMIN"
            title="'.htmlspecialchars(_("Menu Manager"), ENT_QUOTES).'"
            id="menu_manager"
            link="controllers/menues/menues.php"
        />
    </submenu>

    <submenu title="'.htmlspecialchars(_("Contents"), ENT_QUOTES).'" id="contents_manager" link="#"
             privilege="ARTICLE_PENDINGS,OPINION_ADMIN,COMMENT_ADMIN,POLL_ADMIN,ADVERTISEMENT_ADMIN,STATIC_ADMIN,SPECIAL_ADMIN,ARTICLE_ARCHIVE,CATEGORY_ADMIN,MENU_ADMIN">
        <node
            module_name="ARTICLE_MANAGER"
            privilege="ARTICLE_PENDINGS"
            title="'.htmlspecialchars(_("Articles"), ENT_QUOTES).'"
            id="article_manager"
            link="article.php?action=list_pendientes"
        />
        <node
            module_name="OPINION_MANAGER"
            privilege="OPINION_ADMIN"
            title="'.htmlspecialchars(_("Opinions"), ENT_QUOTES).'"
            id="opinion_manager"
            link="controllers/opinion/opinion.php"
        />
        <node
            module_name="COMMENT_MANAGER"
            privilege="COMMENT_ADMIN"
            title="'.htmlspecialchars(_("Comments"), ENT_QUOTES).'"
            id="comment_manager"
            link="controllers/comment/comment.php?action=list"
        />
        <node
            module_name="POLL_MANAGER"
            privilege="POLL_ADMIN"
            title="'.htmlspecialchars(_("Polls"), ENT_QUOTES).'"
            id="poll_manager"
            link="controllers/poll/poll.php"
        />
        <node
            module_name="ADS_MANAGER"
            privilege="ADVERTISEMENT_ADMIN"
            title="'.htmlspecialchars(_("Advertisements"), ENT_QUOTES).'"
            id="ads_manager"
            link="controllers/advertisement/advertisement.php"
        />

         <node
            module_name="SPECIAL_MANAGER"
            privilege="SPECIAL_ADMIN"
            title="'.htmlspecialchars(_("Specials"), ENT_QUOTES).'"
            id="specials_manager"
            link="controllers/specials/special.php"
        />
        <node
            module_name="ARTICLE_MANAGER"
            privilege="ARTICLE_ARCHIVE"
            title="'.htmlspecialchars(_("Library"), ENT_QUOTES).'"
            id="library_manager"
            link="article.php?action=list_hemeroteca"
        />
        <node
            module_name="WIDGET_MANAGER"
            privilege="WIDGET_ADMIN"
            title="'.htmlspecialchars(_("Widgets"), ENT_QUOTES).'"
            id="widget_manager"
            link="controllers/widget/widget.php"
        />
        <node class="divider" />
        <node
            module_name="CATEGORY_MANAGER"
            privilege="CATEGORY_ADMIN"
            title="'.htmlspecialchars(_("Category Manager"), ENT_QUOTES).'"
            id="category_manager"
            link="controllers/category/category.php"
        />
     </submenu>

    <submenu title="'.htmlspecialchars(_("Media"), ENT_QUOTES).'" id="media_manager" link="#"
            privilege="IMAGE_ADMIN,FILE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,KIOSKO_ADMIN,BOOK_ADMIN">
        <node
            module_name="IMAGE_MANAGER"
            privilege="IMAGE_ADMIN"
            title="'.htmlspecialchars(_("Images"), ENT_QUOTES).'"
            id="image_manager"
            link="controllers/image/image.php"
        />
        <node
            module_name="FILE_MANAGER"
            privilege="FILE_ADMIN"
            title="'.htmlspecialchars(_("Files"), ENT_QUOTES).'"
            id="file_manager"
            link="controllers/files/files.php"
        />
        <node
            module_name="VIDEO_MANAGER"
            privilege="VIDEO_ADMIN"
            title="'.htmlspecialchars(_("Videos"), ENT_QUOTES).'"
            id="video_manager"
            link="controllers/video/video.php"
        />
        <node
            module_name="ALBUM_MANAGER"
            privilege="ALBUM_ADMIN"
            title="'.htmlspecialchars(_("Albums"), ENT_QUOTES).'"
            id="album_manager"
            link="controllers/album/album.php"
        />
        <node
            module_name="KIOSKO_MANAGER"
            title="'.htmlspecialchars(_("News Stand"), ENT_QUOTES).'"
            id="kiosko_manager"
            link="controllers/newsstand/newsstand.php"
            privilege="KIOSKO_ADMIN" />
        <node
            module_name="BOOK_MANAGER"
            privilege="BOOK_ADMIN"
            title="'.htmlspecialchars(_("Books"), ENT_QUOTES).'"
            id="book_manager"
            link="controllers/book/book.php"
        />
    </submenu>

    <submenu title="'.htmlspecialchars(_("Utilities"), ENT_QUOTES).'" id="util" link="#"
        privilege="SEARCH_ADMIN,TRASH_ADMIN,NEWSLETTER_ADMIN,PCLAVE_ADMIN,SCHEDULE_ADMIN,IMPORT_EPRESS,IMPORT_EFE,IMPORT_EFE_FILE,IMPORT_XML,BACKEND_ADMIN">
        <node
            module_name="ADVANCED_SEARCH"
            privilege="SEARCH_ADMIN"
            title="'.htmlspecialchars(_("Advanced Search"), ENT_QUOTES).'"
            id="advanced_search"
            link="controllers/search_advanced/search_advanced.php"
        />
         <node class="divider" />
        <node
            module_name="TRASH_MANAGER"
            title="'.htmlspecialchars(_("Trash"), ENT_QUOTES).'"
            id="trash_manager"
            link="controllers/trash.php"
            privilege="TRASH_ADMIN" />
        <node class="divider" />
        <node
            module_name="NEWSLETTER_MANAGER"
            title="'.htmlspecialchars(_("Newsletter"), ENT_QUOTES).'"
            id="newsletter_manager"
            link="controllers/newsletter/newsletter.php"
            privilege="NEWSLETTER_ADMIN" />
        <node
            module_name="KEYWORD_MANAGER"
            title="'.htmlspecialchars(_("Keywords"), ENT_QUOTES).'"
            id="keyword_manager"
            link="controllers/keywords/keywords.php"
            privilege="PCLAVE_ADMIN" />
        <node class="divider" />
         <node
            module_name="SCHEDULE_MANAGER"
            title="'.htmlspecialchars(_("Agenda"), ENT_QUOTES).'"
            id="cronicas_schedule_manager"
            link="http://www.cronicasdelaemigracion.com//agenda/"
            privilege="SCHEDULE_ADMIN" />
        <node
            module_name="EUROPAPRESS_IMPORTER"
            title="'.htmlspecialchars(_("EuropaPress importer"), ENT_QUOTES).'"
            id="europaPress_importer"
            link="controllers/agency_importer/europapress.php"
            privilege="IMPORT_EPRESS" />
        <node
            module_name="EFE_IMPORTER"
            privilege="IMPORT_EFE"
            title="'.htmlspecialchars(_("EFE Importer"), ENT_QUOTES).'"
            id="efe_importer"
            link="controllers/agency_importer/efe.php"
        />
        <node
            module_name="PAPER_IMPORT"
            privilege="IMPORT_EFE_FILE"
            title="'.htmlspecialchars(_("EFE file Importer"), ENT_QUOTES).'"
            id="import_efe_file"
            link="controllers/agency_importer/efe-file.php"
        />
        <node
            module_name="PAPER_IMPORT"
            privilege="IMPORT_XML"
            title="'.htmlspecialchars(_("XML Importer"), ENT_QUOTES).'"
            id="xml_importer"
            link="controllers/agency_importer/importXMLFiles.php"
        />

        <node class="divider" />
        <node
            module_name="ONM_STATISTICS"
            title="'.htmlspecialchars(_("Statistics"), ENT_QUOTES).'"
            id="statistics"
            link="controllers/statistics/statistics.php"
            privilege="BACKEND_ADMIN" />
    </submenu>

    <submenu title="'.htmlspecialchars(_("System"), ENT_QUOTES).'" id="system" link="#"
             privilege="ONM_SETTINGS,CACHE_ADMIN,USER_ADMIN,ONLY_MASTERS,SYSTEM_UPDATE_MANAGER,BACKEND_ADMIN">
        <node
            module_name="SETTINGS_MANAGER"
            title="'.htmlspecialchars(_("System settings"), ENT_QUOTES).'"
            id="settings_manager"
            link="controllers/system_settings/system_settings.php"
            privilege="ONM_SETTINGS" />

            <submenu title="'.htmlspecialchars(_("Users & Groups"), ENT_QUOTES).'" id="user-group_manager" link="#"
                     privilege="USER_ADMIN">
                <node
                    module_name="USER_MANAGER"
                    privilege="USER_ADMIN"
                    title="'.htmlspecialchars(_("Users"), ENT_QUOTES).'"
                    id="user_manager"
                    link="controllers/acl/user.php"
                />
                <node
                    module_name="USER_GROUP_MANAGER"
                    privilege="USER_ADMIN"
                    title="'.htmlspecialchars(_("User Groups"), ENT_QUOTES).'"
                    id="user_group_manager"
                    link="controllers/acl/user_groups.php"
                />
                <node
                    module_name="PRIVILEGE_MANAGER"
                    privilege="ONLY_MASTERS"
                    title="'.htmlspecialchars(_("Privileges"), ENT_QUOTES).'"
                    id="privilege_manager"
                    link="controllers/acl/privileges.php"
                />
            </submenu>
        <node class="divider" />
        <node
            module_name="CACHE_MANAGER"
            title="'.htmlspecialchars(_("Cache Manager"), ENT_QUOTES).'"
            id="cache_manager"
            link="controllers/tpl_manager/tpl_manager.php"
            privilege="CACHE_ADMIN" />
        <node
            module_name="PHP_CACHE_MANAGER"
            title="'.htmlspecialchars(_("PHP Cache Manager"), ENT_QUOTES).'"
            id="php_cache__manager"
            link="controllers/system_information/system_information.php?action=apc_iframe"
            privilege="ONLY_MASTERS" />
        <node class="divider" />
        <node
            module_name="SYSTEM_UPDATE_MANAGER"
            title="'.htmlspecialchars(_("Update System"), ENT_QUOTES).'"
            id="system_update__manager"
            link="controllers/updatesystem/index.php"
            privilege="ONLY_MASTERS" />
        <node
            module_name="LOG_SQL"
            title="'.htmlspecialchars(_("SQL error log"), ENT_QUOTES).'"
            id="log_sql"
            link="controllers/system_information/sql_error_log.php"
            privilege="ONLY_MASTERS" />
        <node
            module_name="MYSQL_MANAGER"
            title="'.htmlspecialchars(_("Database Manager"), ENT_QUOTES).'"
            id="mysql_manager"
            link="controllers/system_information/system_information.php?action=mysql_check"
            privilege="ONLY_MASTERS" />
        <node
            title="'.htmlspecialchars(_("Support and Help"), ENT_QUOTES).'"
            id="support_help"
            link="http://www.openhost.es/"
            target="external"/>
    </submenu>
</menu>';
