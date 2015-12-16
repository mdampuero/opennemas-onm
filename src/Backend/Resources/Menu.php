<?php
/**
 * Defines the backend menu
 *
 * @package  Backend
 */
$menuXml = [
    [
        'id'      => 'browse',
        'class'   => 'list-title visible-xs',
        'title'   => _("Browse")
    ],

    [
        'id'      => 'dashboard',
        'link'    => url('admin_welcome'),
        'title'   => _("Dashboard"),
        'icon'    => 'fa fa-home',
    ],

    [
        'id'      => 'my-newspaper',
        'class'   => 'visible-xs',
        'link'    => url('admin_client_info_page'),
        'title'   => _("My newspaper"),
        'icon'    => 'fa fa-bullseye',
    ],

    // Frontpage menu
    [
        'id'      => 'frontpage',
        'link'    => '#',
        'title'   => _("Website"),
        'icon'    => 'fa fa-globe',
        'submenu' => [
            [
                "id"          => "frontpage_manager",
                "title"       => _("Frontpage Manager"),
                "icon"        => "fa fa-newspaper-o",
                "link"        => url('admin_frontpage_list'),
                "module_name" => "FRONTPAGE_MANAGER",
                "privilege"   => "ARTICLE_FRONTPAGE",
            ],

            [
                "id"          => "ads_manager",
                "title"       => _("Advertisements"),
                "icon"        => "fa fa-bullhorn",
                "link"        => url('admin_ads'),
                "module_name" => "ADS_MANAGER",
                "privilege"   => "ADVERTISEMENT_ADMIN",
            ],

            [
                "id"          => "widget_manager",
                "title"       => _("Widgets"),
                "icon"        => "fa fa-puzzle-piece",
                "link"        => url('admin_widgets'),
                "module_name" => "WIDGET_MANAGER",
                "privilege"   => "WIDGET_ADMIN",
            ],

            [
                "id"          => "sidebar_manager",
                "title"       => _("Sidebars"),
                "icon"        => "fa fa-indent fa-flip-horizontal",
                "link"        => url('admin_sidebars'),
                "module_name" => "SIDEBAR_MANAGER",
                "privilege"   => "SIDEBAR_ADMIN",
            ],

            [
                "id"          => "menu_manager",
                "title"       => _("Menus"),
                "icon"        => "fa fa-list-alt",
                "link"        => url('admin_menus'),
                "module_name" => "MENU_MANAGER",
                "privilege"   => "MENU_ADMIN",
            ],
        ],
    ],

    // Contents menu
    [
        'id'      => 'contents_manager',
        'link'    => '#',
        'title'   => _("Contents"),
        'icon'    => 'fa fa-newspaper-o',
        'submenu' => [
            [
                "id"          => "article_manager",
                "title"       => _("Articles"),
                "icon"        => "fa fa-file-text",
                "link"        => url('admin_articles'),
                "module_name" => "ARTICLE_MANAGER",
                "privilege"   => "ARTICLE_PENDINGS",
            ],
            [
                "id"          => "opinion_manager",
                "title"       => _("Opinions"),
                "icon"        => "fa fa-quote-right",
                "link"        => url('admin_opinions'),
                "module_name" => "OPINION_MANAGER",
                "privilege"   => "OPINION_ADMIN",
            ],
            [
                "id"          => "comment_manager",
                "title"       => _("Comments"),
                "icon"        => "fa fa-comment",
                "link"        => url('admin_comments'),
                "module_name" => "COMMENT_MANAGER",
                "privilege"   => "COMMENT_ADMIN",
            ],

            [
                "id"          => "poll_manager",
                "title"       => _("Polls"),
                "icon"        => "fa fa-pie-chart",
                "link"        => url('admin_polls'),
                "module_name" => "POLL_MANAGER",
                "privilege"   => "POLL_ADMIN",
            ],

            [
                "id"          => "static_pages_manager",
                "title"       => _("Static Pages"),
                "icon"        => "fa fa-file",
                "link"        => url('admin_static_pages'),
                "module_name" => "STATIC_PAGES_MANAGER",
                "privilege"   => "STATIC_PAGE_ADMIN",
            ],

            [
                "id"          => "specials_manager",
                "title"       => _("Specials"),
                "icon"        => "fa fa-star",
                "link"        => url('admin_specials'),
                "module_name" => "SPECIAL_MANAGER",
                "privilege"   => "SPECIAL_ADMIN",
            ],

            [
                "id"          => "letter_manager",
                "title"       => _("Letter to the editor"),
                "icon"        => "fa fa-envelope",
                "link"        => url('admin_letters'),
                "module_name" => "LETTER_MANAGER",
                "privilege"   => "LETTER_ADMIN",
            ],

            [
                "id"          => "category_manager",
                "title"       => _("Category manager"),
                "icon"        => "fa fa-bookmark",
                "link"        => url('admin_categories'),
                "module_name" => "CATEGORY_MANAGER",
                "privilege"   => "CATEGORY_ADMIN",
            ],

        ],
    ],

    // Media menu
    [
        'id'      => 'media_manager',
        'link'    => '#',
        'title'   => _("Media"),
        'icon'    => 'fa fa-image',
        'submenu' => [
            [
                "id"          => "image_manager",
                "title"       => _("Images"),
                "icon"        => "fa fa-picture-o",
                "link"        => url('admin_images'),
                "module_name" => "IMAGE_MANAGER",
                "privilege"   => "PHOTO_ADMIN",
            ],
            [
                "id"          => "file_manager",
                "title"       => _("Files"),
                "icon"        => "fa fa-file",
                "link"        => url('admin_files'),
                "module_name" => "FILE_MANAGER",
                "privilege"   => "ATTACHMENT_ADMIN",
            ],
            [
                "id"          => "video_manager",
                "title"       => _("Videos"),
                "icon"        => "fa fa-film",
                "link"        => url('admin_videos'),
                "module_name" => "VIDEO_MANAGER",
                "privilege"   => "VIDEO_ADMIN",
            ],
            [
                "id"          => "album_manager",
                "title"       => _("Albums"),
                "icon"        => "fa fa-picture-o",
                "link"        => url('admin_albums'),
                "module_name" => "ALBUM_MANAGER",
                "privilege"   => "ALBUM_ADMIN",
            ],
            [
                "id"          => "kiosko_manager",
                "title"       => _("News Stand"),
                "icon"        => "fa fa-newspaper-o",
                "link"        => url('admin_kioskos'),
                "module_name" => "KIOSKO_MANAGER",
                "privilege"   => "KIOSKO_ADMIN",
            ],
            [
                "id"          => "book_manager",
                "title"       => _("Books"),
                "icon"        => "fa fa-book",
                "link"        => url('admin_books'),
                "module_name" => "BOOK_MANAGER",
                "privilege"   => "BOOK_ADMIN",
            ],



        ],
    ],

    // Utils menu
    [
        'id'      => 'util',
        'link'    => '#',
        'title'   => _("Utilities"),
        'icon'    => 'fa fa-wrench',
        'submenu' => [
            [
                "id"          => "advanced_search",
                "title"       => _("Global Search"),
                "icon"        => "fa fa-search",
                "link"        => url('admin_search'),
                "module_name" => "ADVANCED_SEARCH",
                "privilege"   => "SEARCH_ADMIN",
            ],
            [
                "id"          => "trash_manager",
                "title"       => _("Trash"),
                "icon"        => "fa fa-trash-o",
                "link"        => url('admin_trash'),
                "module_name" => "TRASH_MANAGER",
                "privilege"   => "TRASH_ADMIN",
            ],
            [
                "id"          => "newsletter_manager",
                "title"       => _("Newsletters"),
                "icon"        => "fa fa-envelope",
                "link"        => url('admin_newsletters'),
                "module_name" => "NEWSLETTER_MANAGER",
                "privilege"   => "NEWSLETTER_ADMIN",
            ],
            [
                "id"          => "keyword_manager",
                "title"       => _("Keywords"),
                "icon"        => "fa fa-tags",
                "link"        => url('admin_keywords'),
                "module_name" => "KEYWORD_MANAGER",
                "privilege"   => "PCLAVE_ADMIN",
            ],
            [
                "id"          => "paywall",
                "title"       => _("Paywall"),
                "icon"        => "fa fa-paypal",
                "link"        => '#',
                "module_name" => "PAYWALL",
                "privilege"   => "PAYWALL",
                "submenu" => [
                    [
                        "id"          => "paywall_stats",
                        "title"       => _("Statistics"),
                        "icon"        => "fa fa-bar-chart",
                        "link"        => url('admin_paywall'),
                        "module_name" => "PAYWALL",
                        "privilege"   => "PAYWALL",
                    ],
                    [
                        "id"          => "paywall_users",
                        "title"       => _("Paywall users"),
                        "icon"        => "fa fa-users",
                        "link"        => url('admin_paywall_users'),
                        "module_name" => "PAYWALL",
                        "privilege"   => "PAYWALL",
                    ],
                    [
                        "id"          => "paywall_purchases",
                        "title"       => _("Paywall purchases"),
                        "icon"        => "fa fa-shopping-cart",
                        "link"        => url('admin_paywall_purchases'),
                        "module_name" => "PAYWALL",
                        "privilege"   => "PAYWALL",
                    ],
                ]
            ],
            [
                "id"          => "sync_manager",
                "title"       => _("Sync Instances"),
                "icon"        => "fa fa-exchange",
                "link"        => url('admin_instance_sync'),
                "module_name" => "SYNC_MANAGER",
                "privilege"   => "SYNC_ADMIN",
            ],
            [
                "id"          => "news_agency",
                "title"       => _("News Agency"),
                "icon"        => "fa fa-microphone",
                "link"        => url('admin_news_agency'),
                "module_name" => "NEWS_AGENCY_IMPORTER",
                "privilege"   => "IMPORT_ADMIN",
            ],
        ],
    ],

    // Settings menu
    [
        'id'      => 'system',
        'link'    => '#',
        'title'   => _("System"),
        'icon'    => 'fa fa-cogs',
        'submenu' => [
            [
                "id"          => "settings_manager",
                "title"       => _("Configuration"),
                "icon"        => "fa fa-wrench",
                "link"        => url('admin_system_settings'),
                "module_name" => "SETTINGS_MANAGER",
                "privilege"   => "ONM_SETTINGS",
            ],
            [
                "id"          => "acl_manager",
                "title"       => _("Users & Groups"),
                "link"        => '#',
                "icon"        => "fa fa-users",
                "module_name" => "USER_MANAGER || USER_GROUP_MANAGER",
                "privilege"   => "USER_ADMIN",
                "submenu"     => [
                    [
                        "id"          => "user_manager",
                        "title"       => _("Users"),
                        "icon"        => "fa fa-user",
                        "link"        => url('admin_acl_user'),
                        "module_name" => "USER_MANAGER",
                        "privilege"   => "USER_ADMIN",
                    ],
                    [
                        "id"          => "user_group_manager",
                        "title"       => _("User Groups"),
                        "icon"        => "fa fa-users",
                        "link"        => url('admin_acl_usergroups'),
                        "module_name" => "USER_GROUP_MANAGER",
                        "privilege"   => "USER_ADMIN",
                    ],
                ],
            ],
            [
                "id"          => "authors",
                "title"       => _("Authors"),
                "link"        => url('admin_opinion_authors'),
                'icon'        => 'fa fa-user',
                "module_name" => "OPINION_MANAGER",
                "privilege"   => "AUTHOR_ADMIN",
            ],
            [
                "id"          => "cache_manager",
                "title"       => _("Cache manager"),
                "icon"        => "fa fa-database",
                "link"        => url('admin_tpl_manager'),
                "module_name" => "CACHE_MANAGER",
                "privilege"   => "ONLY_MASTERS",
            ],
            [
                "id"          => "log_sql",
                "title"       => _("SQL error log"),
                "icon"        => "fa fa-code",
                "link"        => url('admin_databaseerrors'),
                "privilege"   => "ONLY_MASTERS",
            ],
        ],
    ],
    [
        'id'    => 'market',
        'title' => _('Store'),
        'icon'  => 'fa fa-shopping-cart',
        'link'  => url('admin_store_list'),
        'privilege' => 'ROLE_ADMIN'
    ],
    [
        "id"          => "theme-manager",
        "title"       => _("Themes"),
        "icon"        => "fa fa-magic",
        "link"        => url('backend_theme_list'),
        "privilege"   => "USER_ADMIN",
    ],
    [
        "id"          => "domain_manager",
        "title"       => _("Domains"),
        "icon"        => "fa fa-server",
        "link"        => url('admin_domain_management'),
        "privilege"   => "USER_ADMIN",
    ],
    [
        'id'      => 'faq_and_support',
        'class'   => 'visible-xs',
        'link'    => '#',
        'title'   => _("Help & Support"),
        'icon'    => 'fa fa-support',
        'submenu' => [
            [
                "id"          => "youtube",
                "title"       => _("Youtube channel"),
                "icon"        => "fa fa-youtube",
                "link"        => 'http://www.youtube.com/user/OpennemasPublishing',
            ],
            [
                "id"          => "faq",
                "title"       => _("FAQ"),
                "link"        => 'http://help.opennemas.com',
                "icon"        => "fa fa-question-circle",
                "module_name" => "CACHE_MANAGER",
                "privilege"   => "CACHE_ADMIN",
            ],
            [
                "id"          => "support",
                "title"       => _("Contact support"),
                "icon"        => "fa fa-support",
                "link"        => 'javascript:UserVoice.showPopupWidget();',
                "module_name" => "LOG_SQL",
                "privilege"   => "ONLY_MASTERS",
            ],
        ]
    ]
];

return $menuXml;
