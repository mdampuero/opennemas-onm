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
            module_name="MENU_MANAGER"
            privilege="MENU_ADMIN"
            title="'.htmlspecialchars(_("Menus"), ENT_QUOTES).'"
            id="menu_manager"
            link="'.url('admin_menus').'"
        />
    </submenu>

    <submenu title="'.htmlspecialchars(_("Contents"), ENT_QUOTES).'" id="contents_manager" link="#"
             privilege="ARTICLE_PENDINGS,OPINION_ADMIN,COMMENT_ADMIN,POLL_ADMIN,'
                       .'ADVERTISEMENT_ADMIN,STATIC_ADMIN,SPECIAL_ADMIN,ARTICLE_ARCHIVE,'
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
                privilege="OPINION_ADMIN"
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
            module_name="COMMENT_DISQUS_MANAGER"
            privilege="COMMENT_ADMIN"
            title="'.htmlspecialchars(_("Comments (Disqus)"), ENT_QUOTES).'"
            id="comment_disqus_manager"
            link="'.url('admin_comments_disqus').'"
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
            privilege="STATIC_ADMIN"
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
        <node class="divider" />
        <node
            module_name="CATEGORY_MANAGER"
            privilege="CATEGORY_ADMIN"
            title="'.htmlspecialchars(_("Category manager"), ENT_QUOTES).'"
            id="category_manager"
            link="'.url('admin_categories').'"
        />
     </submenu>

    <submenu title="'.htmlspecialchars(_("Media"), ENT_QUOTES).'" id="media_manager" link="#"
            privilege="IMAGE_ADMIN,FILE_ADMIN,VIDEO_ADMIN,ALBUM_ADMIN,KIOSKO_ADMIN,BOOK_ADMIN">
        <node
            module_name="IMAGE_MANAGER"
            privilege="IMAGE_ADMIN"
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
        <node class="divider" />
        <node
            module_name="TRASH_MANAGER"
            title="'.htmlspecialchars(_("Trash"), ENT_QUOTES).'"
            id="trash_manager"
            link="'.url("admin_trash", array()).'"
            privilege="TRASH_ADMIN" />
        <node class="divider" />
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
        <node class="divider" />
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
        <node
            module_name="SCHEDULE_MANAGER"
            title="'.htmlspecialchars(_("Agenda"), ENT_QUOTES).'"
            id="cronicas_schedule_manager"
            link="http://www.cronicasdelaemigracion.com//agenda/"
            privilege="SCHEDULE_ADMIN"
        />
        <node
            module_name="CRONICAS_MODULES"
            title="'.htmlspecialchars(_("Statistics Google Analytics"), ENT_QUOTES).'"
            id="analytics_manager"
            target="external"
            link="https://www.google.com/analytics/web/#report/visitors-overview/a32255002w59223403p60488603/"
            privilege="STATS_ADMIN" />

        <submenu title="'.htmlspecialchars(_("Mailman Manager"), ENT_QUOTES).'" id="mailman" link="#"
                     privilege="NEWSLETTER_ADMIN">
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Newsletter"), ENT_QUOTES).'"
                id="mailman_1"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/boletin"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Priority"), ENT_QUOTES).'"
                id="mailman_2"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/boletinprioridad"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Crónicas"), ENT_QUOTES).'"
                id="mailman_3"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/cronicas"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Galicia"), ENT_QUOTES).'"
                id="mailman_4"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/galicia"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Asturias"), ENT_QUOTES).'"
                id="mailman_5"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/asturias"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Madrid"), ENT_QUOTES).'"
                id="mailman_6"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/madrid"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Castilla y León"), ENT_QUOTES).'"
                id="mailman_7"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/castillayleon"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Canarias"), ENT_QUOTES).'"
                id="mailman_8"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/canarias"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Cantabria"), ENT_QUOTES).'"
                id="mailman_9"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/cantabria"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Baleares"), ENT_QUOTES).'"
                id="mailman_10"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/baleares"
            />
            <node
                module_name="CRONICAS_MODULES"
                privilege="NEWSLETTER_ADMIN"
                target="external"
                title="'.htmlspecialchars(_("Mailman Andalucía"), ENT_QUOTES).'"
                id="mailman_11"
                link="https://listas.cronicasdelaemigracion.com/cgi-bin/mailman/admin/andalucia"
            />
        </submenu>

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
        <node class="divider" />
        <node
            module_name="CACHE_MANAGER"
            title="'.htmlspecialchars(_("Cache manager"), ENT_QUOTES).'"
            id="cache_manager"
            link="'.url("admin_tpl_manager", array()).'"
            privilege="CACHE_ADMIN" />
        <node class="divider" />
        <node
            module_name="LOG_SQL"
            title="'.htmlspecialchars(_("SQL error log"), ENT_QUOTES).'"
            id="log_sql"
            link="'.url("admin_databaseerrors", array()).'"
            privilege="ONLY_MASTERS" />
        <node
            title="'.htmlspecialchars(_("Support and Help"), ENT_QUOTES).'"
            id="support_help"
            link="http://help.opennemas.com/"
            target="external"/>
    </submenu>
</menu>';
