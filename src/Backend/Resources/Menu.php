<?php
/**
 * Defines the backend menu
 *
 * @package  Backend
 */
global $generator;
$menuXml = '<?xml version="1.0"?>
<menu>
    <submenu title="'.htmlspecialchars(_("Web site"), ENT_QUOTES).'" id="frontpage" link="#">
        <node
            module_name="FRONTPAGE_MANAGER"
            privilege="ARTICLE_FRONTPAGE"
            title="'.htmlspecialchars(_("Frontpage Manager"), ENT_QUOTES).'"
            id="frontpage_manager"
            link="'.url('admin_frontpage_list').'"
        />
        <node
            module_name="ADS_MANAGER"
            privilege="ADVERTISEMENT_ADMIN"
            title="'.htmlspecialchars(_("Advertisements"), ENT_QUOTES).'"
            id="ads_manager"
            link="'.url('admin_ads').'"
        />
        <node
            module_name="WIDGET_MANAGER"
            privilege="WIDGET_ADMIN"
            title="'.htmlspecialchars(_("Widgets"), ENT_QUOTES).'"
            id="widget_manager"
            link="'.url('admin_widgets').'"
        />
        <node
            module_name="SIDEBAR_MANAGER"
            privilege="SIDEBAR_ADMIN"
            title="'.htmlspecialchars(_("Sidebars"), ENT_QUOTES).'"
            id="sidebar_manager"
            link="'.url('admin_sidebars').'"
        />
        <node
            module_name="MENU_MANAGER"
            privilege="MENU_ADMIN"
            title="'.htmlspecialchars(_("Menus"), ENT_QUOTES).'"
            id="menu_manager"
            link="'.url('admin_menus').'"
        />
    </submenu>

    <submenu title="'.htmlspecialchars(_("Contents"), ENT_QUOTES).'" id="contents_manager" link="#"
             privilege="ARTICLE_PENDINGS,OPINION_ADMIN,COMMENT_ADMIN,POLL_ADMIN,'
                       .'ADVERTISEMENT_ADMIN,STATIC_PAGE_ADMIN,SPECIAL_ADMIN,ARTICLE_ARCHIVE,'
                       .'CATEGORY_ADMIN,MENU_ADMIN">
        <node
            module_name="ARTICLE_MANAGER"
            privilege="ARTICLE_PENDINGS"
            title="'.htmlspecialchars(_("Articles"), ENT_QUOTES).'"
            id="article_manager"
            link="'.url('admin_articles').'"
        />
        <submenu module_name="OPINION_MANAGER"
            privilege="OPINION_ADMIN"
            title="'.htmlspecialchars(_("Opinions"), ENT_QUOTES).'"
            id="opinion_manager"
            link="#">
            <node
                module_name="OPINION_MANAGER"
                privilege="OPINION_ADMIN"
                title="'.htmlspecialchars(_("Article opinions"), ENT_QUOTES).'"
                id="opinion_manager"
                link="'.url('admin_opinions').'"
            />
            <node
                module_name="OPINION_MANAGER"
                privilege="AUTHOR_ADMIN"
                title="'.htmlspecialchars(_("Authors"), ENT_QUOTES).'"
                id="authors"
                link="'.url('admin_opinion_authors').'"
            />
        </submenu>
        <node
            module_name="COMMENT_MANAGER"
            privilege="COMMENT_ADMIN"
            title="'.htmlspecialchars(_("Comments"), ENT_QUOTES).'"
            id="comment_manager"
            link="'.url('admin_comments').'"
        />
        <node
            module_name="POLL_MANAGER"
            privilege="POLL_ADMIN"
            title="'.htmlspecialchars(_("Polls"), ENT_QUOTES).'"
            id="poll_manager"
            link="'.url('admin_polls').'"
        />
        <node
            module_name="STATIC_PAGES_MANAGER"
            privilege="STATIC_PAGE_ADMIN"
            title="'.htmlspecialchars(_("Static Pages"), ENT_QUOTES).'"
            id="static_pages_manager"
            link="'.url('admin_staticpages').'"
        />
        <node
            module_name="SPECIAL_MANAGER"
            privilege="SPECIAL_ADMIN"
            title="'.htmlspecialchars(_("Specials"), ENT_QUOTES).'"
            id="specials_manager"
            link="'.url('admin_specials').'"
        />
        <node
            module_name="LETTER_MANAGER"
            privilege="LETTER_ADMIN"
            title="'.htmlspecialchars(_("Letter to the editor"), ENT_QUOTES).'"
            id="letter_manager"
            link="'.url('admin_letters').'"
        />
        <node
            module_name="CATEGORY_MANAGER"
            privilege="CATEGORY_ADMIN"
            title="'.htmlspecialchars(_("Category manager"), ENT_QUOTES).'"
            id="category_manager"
            link="'.url('admin_categories').'"
        />
     </submenu>

    <submenu title="'.htmlspecialchars(_("Media"), ENT_QUOTES).'" id="media_manager" link="#"
            privilege="PHOTO_ADMIN,FILE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,KIOSKO_ADMIN,BOOK_ADMIN">
        <node
            module_name="IMAGE_MANAGER"
            privilege="PHOTO_ADMIN"
            title="'.htmlspecialchars(_("Images"), ENT_QUOTES).'"
            id="image_manager"
            link="'.url('admin_images').'"
        />
        <node
            module_name="FILE_MANAGER"
            privilege="FILE_ADMIN"
            title="'.htmlspecialchars(_("Files"), ENT_QUOTES).'"
            id="file_manager"
            link="'.url('admin_files').'"
        />
        <node
            module_name="VIDEO_MANAGER"
            privilege="VIDEO_ADMIN"
            title="'.htmlspecialchars(_("Videos"), ENT_QUOTES).'"
            id="video_manager"
            link="'.url('admin_videos').'"
        />
        <node
            module_name="ALBUM_MANAGER"
            privilege="ALBUM_ADMIN"
            title="'.htmlspecialchars(_("Albums"), ENT_QUOTES).'"
            id="album_manager"
            link="'.url('admin_albums').'"
        />
        <node
            module_name="KIOSKO_MANAGER"
            title="'.htmlspecialchars(_("News Stand"), ENT_QUOTES).'"
            id="kiosko_manager"
            link="'.url('admin_covers').'"
            privilege="KIOSKO_ADMIN" />
        <node
            module_name="BOOK_MANAGER"
            privilege="BOOK_ADMIN"
            title="'.htmlspecialchars(_("Books"), ENT_QUOTES).'"
            id="book_manager"
            link="'.url("admin_books").'"
        />
    </submenu>

    <submenu title="'.htmlspecialchars(_("Utilities"), ENT_QUOTES).'" id="util" link="#"
        privilege="SEARCH_ADMIN,TRASH_ADMIN,NEWSLETTER_ADMIN,PCLAVE_ADMIN,SCHEDULE_ADMIN,'
                  .'IMPORTER_ADMIN,IMPORT_EFE_FILE,IMPORT_XML,BACKEND_ADMIN">
        <node
            module_name="ADVANCED_SEARCH"
            privilege="SEARCH_ADMIN"
            title="'.htmlspecialchars(_("Global Search"), ENT_QUOTES).'"
            id="advanced_search"
            link="'.url('admin_search').'"
        />
        <node
            module_name="TRASH_MANAGER"
            title="'.htmlspecialchars(_("Trash"), ENT_QUOTES).'"
            id="trash_manager"
            link="'.url("admin_trash", array()).'"
            privilege="TRASH_ADMIN" />
        <node
            module_name="NEWSLETTER_MANAGER"
            title="'.htmlspecialchars(_("Newsletter"), ENT_QUOTES).'"
            id="newsletter_manager"
            link="'.url('admin_newsletters').'"
            privilege="NEWSLETTER_ADMIN" />
        <node
            module_name="KEYWORD_MANAGER"
            title="'.htmlspecialchars(_("Keywords"), ENT_QUOTES).'"
            id="keyword_manager"
            link="'.url('admin_keywords', array()).'"
            privilege="PCLAVE_ADMIN" />
        <submenu title="'.htmlspecialchars(_("Paywall"), ENT_QUOTES).'" id="paywall" link="#"
            privilege="PAYWALL">
            <node
            module_name="PAYWALL"
            title="'.htmlspecialchars(_("Statistics"), ENT_QUOTES).'"
            id="keyword_manager"
            link="'.url('admin_paywall', array()).'"
            privilege="PAYWALL" />
            <node
            module_name="PAYWALL"
            title="'.htmlspecialchars(_("Paywall users"), ENT_QUOTES).'"
            id="keyword_manager"
            link="'.url('admin_paywall_users', array()).'"
            privilege="PAYWALL" />
            <node
            module_name="PAYWALL"
            title="'.htmlspecialchars(_("Paywall purchases"), ENT_QUOTES).'"
            id="keyword_manager"
            link="'.url('admin_paywall_purchases', array()).'"
            privilege="PAYWALL" />
        </submenu>
        <node
            module_name="SYNC_MANAGER"
            title="'.htmlspecialchars(_("Sync Instances"), ENT_QUOTES).'"
            privilege="SYNC_ADMIN"
            id="sync_manager"
            link="'.url('admin_instance_sync').'"
        />
        <node
            module_name="NEWS_AGENCY_IMPORTER"
            privilege="IMPORT_ADMIN"
            title="'.htmlspecialchars(_("News Agency"), ENT_QUOTES).'"
            id="news_agency"
            link="'.url('admin_news_agency').'"
        />
        <node
            module_name="PAPER_IMPORT"
            privilege="IMPORT_XML"
            title="'.htmlspecialchars(_("XML Importer"), ENT_QUOTES).'"
            id="xml_importer"
            link="'.url('admin_importer_xmlfile').'"
        />

    </submenu>

    <submenu title="'.htmlspecialchars(_("Settings"), ENT_QUOTES).'" id="system" link="#"
             privilege="ONM_SETTINGS,CACHE_ADMIN,USER_ADMIN,ONLY_MASTERS,SYSTEM_UPDATE_MANAGER,BACKEND_ADMIN">
        <node
            module_name="SETTINGS_MANAGER"
            title="'.htmlspecialchars(_("General"), ENT_QUOTES).'"
            id="settings_manager"
            link="'.url("admin_system_settings", array()).'"
            privilege="ONM_SETTINGS" />

            <submenu title="'.htmlspecialchars(_("Users & Groups"), ENT_QUOTES).'" id="user-group_manager" link="#"
                     privilege="USER_ADMIN">
                <node
                    module_name="USER_MANAGER"
                    privilege="USER_ADMIN"
                    title="'.htmlspecialchars(_("Users"), ENT_QUOTES).'"
                    id="user_manager"
                    link="'.url("admin_acl_user", array()).'"
                />
                <node
                    module_name="USER_GROUP_MANAGER"
                    privilege="USER_ADMIN"
                    title="'.htmlspecialchars(_("User Groups"), ENT_QUOTES).'"
                    id="user_group_manager"
                    link="'.url("admin_acl_usergroups", array()).'"
                />
            </submenu>
        <node
            module_name="CACHE_MANAGER"
            title="'.htmlspecialchars(_("Cache manager"), ENT_QUOTES).'"
            id="cache_manager"
            link="'.url("admin_tpl_manager", array()).'"
            privilege="CACHE_ADMIN" />
        <node
            module_name="LOG_SQL"
            title="'.htmlspecialchars(_("SQL error log"), ENT_QUOTES).'"
            id="log_sql"
            link="'.url("admin_databaseerrors", array()).'"
            privilege="ONLY_MASTERS" />
    </submenu>
</menu>';
