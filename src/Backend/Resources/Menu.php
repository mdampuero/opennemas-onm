<?php
/**
 * Defines the backend menu
 *
 * @package  Backend
 */
$menuXml = [
    [
        'id'      => 'welcome',
        'link'    => url('admin_welcome'),
        'title'   => _("Welcome"),
        'icon'    => 'fa fa-home',
    ],

    // Frontpage menu
    [
        'id'      => 'frontpage',
        'link'    => '#',
        'title'   => _("Website"),
        'icon'    => 'fa fa-th',
        'submenu' => [
            [
                "id"          => "frontpage_manager",
                "title"       => _("Frontpage Manager"),
                "link"        => url('admin_frontpage_list'),
                "module_name" => "FRONTPAGE_MANAGER",
                "privilege"   => "ARTICLE_FRONTPAGE",
            ],

            [
                "id"          => "ads_manager",
                "title"       => _("Advertisements"),
                "link"        => url('admin_ads'),
                "module_name" => "ADS_MANAGER",
                "privilege"   => "ADVERTISEMENT_ADMIN",
            ],

            [
                "id"          => "widget_manager",
                "title"       => _("Widgets"),
                "link"        => url('admin_widgets'),
                "module_name" => "WIDGET_MANAGER",
                "privilege"   => "WIDGET_ADMIN",
            ],

            [
                "id"          => "sidebar_manager",
                "title"       => _("Sidebars"),
                "link"        => url('admin_sidebars'),
                "module_name" => "SIDEBAR_MANAGER",
                "privilege"   => "SIDEBAR_ADMIN",
            ],

            [
                "id"          => "menu_manager",
                "title"       => _("Menus"),
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
                "link"        => url('admin_articles'),
                "module_name" => "ARTICLE_MANAGER",
                "privilege"   => "ARTICLE_PENDINGS",
            ],
            [
                "id"          => "opinion_manager",
                "title"       => _("Opinions"),
                "link"        => '#',
                "module_name" => "OPINION_MANAGER",
                "privilege"   => "OPINION_ADMIN",
                "submenu"     => [
                    [
                        "id"          => "opinion_manager",
                        "title"       => _("Article opinions"),
                        "link"        => url('admin_opinions'),
                        "module_name" => "OPINION_MANAGER",
                        "privilege"   => "OPINION_ADMIN",
                    ],
                    [
                        "id"          => "authors",
                        "title"       => _("Authors"),
                        "link"        => url('admin_opinion_authors'),
                        "module_name" => "OPINION_MANAGER",
                        "privilege"   => "AUTHOR_ADMIN",
                    ],
                ],
            ],
            [
                "id"          => "comment_manager",
                "title"       => _("Comments"),
                "link"        => url('admin_comments'),
                "module_name" => "COMMENT_MANAGER",
                "privilege"   => "COMMENT_ADMIN",
            ],

            [
                "id"          => "poll_manager",
                "title"       => _("Polls"),
                "link"        => url('admin_polls'),
                "module_name" => "POLL_MANAGER",
                "privilege"   => "POLL_ADMIN",
            ],

            [
                "id"          => "static_pages_manager",
                "title"       => _("Static Pages"),
                "link"        => url('admin_staticpages'),
                "module_name" => "STATIC_PAGES_MANAGER",
                "privilege"   => "STATIC_PAGE_ADMIN",
            ],

            [
                "id"          => "specials_manager",
                "title"       => _("Specials"),
                "link"        => url('admin_specials'),
                "module_name" => "SPECIAL_MANAGER",
                "privilege"   => "SPECIAL_ADMIN",
            ],

            [
                "id"          => "letter_manager",
                "title"       => _("Letter to the editor"),
                "link"        => url('admin_letters'),
                "module_name" => "LETTER_MANAGER",
                "privilege"   => "LETTER_ADMIN",
            ],

            [
                "id"          => "category_manager",
                "title"       => _("Category manager"),
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
                "link"        => url('admin_images'),
                "module_name" => "IMAGE_MANAGER",
                "privilege"   => "PHOTO_ADMIN",
            ],
            [
                "id"          => "file_manager",
                "title"       => _("Files"),
                "link"        => url('admin_files'),
                "module_name" => "FILE_MANAGER",
                "privilege"   => "ATTACHMENT_ADMIN",
            ],
            [
                "id"          => "video_manager",
                "title"       => _("Videos"),
                "link"        => url('admin_videos'),
                "module_name" => "VIDEO_MANAGER",
                "privilege"   => "VIDEO_ADMIN",
            ],
            [
                "id"          => "album_manager",
                "title"       => _("Albums"),
                "link"        => url('admin_albums'),
                "module_name" => "ALBUM_MANAGER",
                "privilege"   => "ALBUM_ADMIN",
            ],
            [
                "id"          => "kiosko_manager",
                "title"       => _("News Stand"),
                "link"        => url('admin_covers'),
                "module_name" => "KIOSKO_MANAGER",
                "privilege"   => "KIOSKO_ADMIN",
            ],
            [
                "id"          => "book_manager",
                "title"       => _("Books"),
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
                "link"        => url('admin_search'),
                "module_name" => "ADVANCED_SEARCH",
                "privilege"   => "SEARCH_ADMIN",
            ],
            [
                "id"          => "trash_manager",
                "title"       => _("Trash"),
                "link"        => url('admin_trash'),
                "module_name" => "TRASH_MANAGER",
                "privilege"   => "TRASH_ADMIN",
            ],
            [
                "id"          => "newsletter_manager",
                "title"       => _("Newsletter"),
                "link"        => url('admin_newsletters'),
                "module_name" => "NEWSLETTER_MANAGER",
                "privilege"   => "NEWSLETTER_ADMIN",
            ],
            [
                "id"          => "keyword_manager",
                "title"       => _("Keywords"),
                "link"        => url('admin_keywords'),
                "module_name" => "KEYWORD_MANAGER",
                "privilege"   => "PCLAVE_ADMIN",
            ],
            [
                "id"          => "paywall",
                "title"       => _("Paywall"),
                "link"        => '#',
                "submenu" => [
                    [
                        "id"          => "paywall_stats",
                        "title"       => _("Statistics"),
                        "link"        => url('admin_paywall'),
                        "module_name" => "PAYWALL",
                        "privilege"   => "PAYWALL",
                    ],
                    [
                        "id"          => "paywall_users",
                        "title"       => _("Paywall users"),
                        "link"        => url('admin_paywall_users'),
                        "module_name" => "PAYWALL",
                        "privilege"   => "PAYWALL",
                    ],
                    [
                        "id"          => "paywall_purchases",
                        "title"       => _("Paywall purchases"),
                        "link"        => url('admin_paywall_purchases'),
                        "module_name" => "PAYWALL",
                        "privilege"   => "PAYWALL",
                    ],
                ]
            ],
            [
                "id"          => "sync_manager",
                "title"       => _("Sync Instances"),
                "link"        => url('admin_instance_sync'),
                "module_name" => "SYNC_MANAGER",
                "privilege"   => "SYNC_ADMIN",
            ],
            [
                "id"          => "news_agency",
                "title"       => _("News Agency"),
                "link"        => url('admin_news_agency'),
                "module_name" => "NEWS_AGENCY_IMPORTER",
                "privilege"   => "IMPORT_ADMIN",
            ],
            [
                "id"          => "xml_importer",
                "title"       => _("XML Importer"),
                "link"        => url('admin_importer_xmlfile'),
                "module_name" => "PAPER_IMPORT",
                "privilege"   => "IMPORT_XML",
            ],
        ],
    ],

    // Settings menu
    [
        'id'      => 'system',
        'link'    => '#',
        'title'   => _("Settings"),
        'icon'    => 'fa fa-cogs',
        'submenu' => [
            [
                "id"          => "settings_manager",
                "title"       => _("General"),
                "link"        => url('admin_system_settings'),
                "module_name" => "SETTINGS_MANAGER",
                "privilege"   => "ONM_SETTINGS",
            ],
            [
                "id"          => "acl_manager",
                "title"       => _("Users & Groups"),
                "link"        => '#',
                "module_name" => "SETTINGS_MANAGER",
                "privilege"   => "ONM_SETTINGS",
                "submenu"     => [
                    [
                        "id"          => "user_manager",
                        "title"       => _("Users"),
                        "link"        => url('admin_acl_user'),
                        "module_name" => "USER_MANAGER",
                        "privilege"   => "USER_ADMIN",
                    ],
                    [
                        "id"          => "user_group_manager",
                        "title"       => _("User Groups"),
                        "link"        => url('admin_acl_usergroups'),
                        "module_name" => "USER_GROUP_MANAGER",
                        "privilege"   => "USER_ADMIN",
                    ],
                ],
            ],
            [
                "id"          => "cache_manager",
                "title"       => _("Cache manager"),
                "link"        => url('admin_tpl_manager'),
                "module_name" => "CACHE_MANAGER",
                "privilege"   => "CACHE_ADMIN",
            ],
            [
                "id"          => "log_sql",
                "title"       => _("SQL error log"),
                "link"        => url('admin_databaseerrors'),
                "module_name" => "LOG_SQL",
                "privilege"   => "ONLY_MASTERS",
            ],

        ],
    ],
    [
        'id'      => 'faq_and_support',
        'link'    => '#',
        'title'   => _("Help & Support"),
        'icon'    => 'fa fa-support',
        'submenu' => [
            [
                "id"          => "faq",
                "title"       => _("FAQ"),
                "link"        => 'http://help.opennemas.com',
                "module_name" => "CACHE_MANAGER",
                "privilege"   => "CACHE_ADMIN",
            ],
            [
                "id"          => "support",
                "title"       => _("Contact support"),
                "link"        => 'javascript:UserVoice.showPopupWidget();',
                'class'       => 'support-button',
                "module_name" => "LOG_SQL",
                "privilege"   => "ONLY_MASTERS",
            ],
        ]
    ]
];

return $menuXml;