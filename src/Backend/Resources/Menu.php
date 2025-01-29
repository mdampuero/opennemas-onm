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
        'title'   => _('Browse')
    ],

    [
        'id'      => 'dashboard',
        'link'    => url('admin_welcome'),
        'title'   => _('Dashboard'),
        'icon'    => 'fa fa-home',
    ],

    [
        'id'      => 'my-newspaper',
        'class'   => 'visible-xs',
        'link'    => url('backend_account_show'),
        'title'   => _('My newspaper'),
        'icon'    => 'fa fa-bullseye',
    ],

    // Frontpage menu
    [
        'id'      => 'frontpage',
        'link'    => '#',
        'title'   => _('Website'),
        'icon'    => 'fa fa-globe',
        'submenu' => [
            [
                'id'          => 'frontpage_manager',
                'title'       => _('Frontpage Manager'),
                'icon'        => 'fa fa-newspaper-o',
                'link'        => url('admin_frontpage_list'),
                'module_name' => 'FRONTPAGE_MANAGER',
                'privilege'   => 'ARTICLE_FRONTPAGE',
            ],

            [
                'id'          => 'ads_manager',
                'title'       => _('Advertisements'),
                'icon'        => 'fa fa-bullhorn',
                'link'        => url('admin_ads'),
                'module_name' => 'ADS_MANAGER',
                'privilege'   => 'ADVERTISEMENT_ADMIN',
            ],

            [
                'id'          => 'widget_manager',
                'title'       => _('Widgets'),
                'icon'        => 'fa fa-puzzle-piece',
                'link'        => url('backend_widgets_list'),
                'module_name' => 'WIDGET_MANAGER',
                'privilege'   => 'WIDGET_ADMIN',
            ],

            [
                'id'          => 'menu_manager',
                'title'       => _('Menus'),
                'icon'        => 'fa fa-list-alt',
                'link'        => url('backend_menus_list'),
                'module_name' => 'MENU_MANAGER',
                'privilege'   => 'MENU_ADMIN',
            ],
            [
                'id'          => 'authors',
                'title'       => _('Authors'),
                'link'        => url('backend_authors_list'),
                'icon'        => 'fa fa-edit',
                'module_name' => 'OPINION_MANAGER',
                'privilege'   => 'AUTHOR_ADMIN',
            ],
            [
                'id'          => 'tags',
                'title'       => _('Tags'),
                'link'        => url('backend_tags_list'),
                'icon'        => 'fa fa-tags',
                // 'module_name' => 'TAG_MANAGER',
                'privilege'   => 'TAG_ADMIN',
            ],
        ],
    ],

    // Contents menu
    [
        'id'      => 'contents_manager',
        'link'    => '#',
        'title'   => _('Contents'),
        'icon'    => 'fa fa-newspaper-o',
        'submenu' => [
            [
                'id'          => 'article_manager',
                'title'       => _('Articles'),
                'icon'        => 'fa fa-file-text',
                'link'        => url('backend_articles_list'),
                'module_name' => 'ARTICLE_MANAGER',
                'privilege'   => 'ARTICLE_ADMIN',
            ],
            [
                'id'          => 'opinion_manager',
                'title'       => _('Opinions'),
                'icon'        => 'fa fa-quote-right',
                'link'        => url('backend_opinions_list'),
                'module_name' => 'OPINION_MANAGER',
                'privilege'   => 'OPINION_ADMIN',
            ],
            [
                'id'          => 'comment_manager',
                'title'       => _('Comments'),
                'icon'        => 'fa fa-comment',
                'link'        => url('backend_comments_list'),
                'module_name' => 'COMMENT_MANAGER',
                'privilege'   => 'COMMENT_ADMIN',
            ],

            [
                'id'          => 'events_manager',
                'title'       => _('Events'),
                'icon'        => 'fa fa-calendar',
                'link'        => url('backend_events_list'),
                'module_name' => 'es.openhost.module.events',
                'privilege'   => 'EVENT_ADMIN',
            ],

            [
                'id'          => 'obituaries_manager',
                'title'       => _('Obituaries'),
                'icon'        => 'fa fa-shield fa-flip-vertical',
                'link'        => url('backend_obituaries_list'),
                'module_name' => 'es.openhost.module.obituaries',
                'privilege'   => 'OBITUARY_ADMIN',
            ],

            [
                'id'          => 'companies_manager',
                'title'       => _('Companies'),
                'icon'        => 'fa fa-building',
                'link'        => url('backend_companies_list'),
                'module_name' => 'es.openhost.module.companies',
                'privilege'   => 'COMPANY_ADMIN',
            ],

            [
                'id'          => 'poll_manager',
                'title'       => _('Polls'),
                'icon'        => 'fa fa-pie-chart',
                'link'        => url('backend_polls_list'),
                'module_name' => 'POLL_MANAGER',
                'privilege'   => 'POLL_ADMIN',
            ],

            [
                'id'          => 'static_pages_manager',
                'title'       => _('Static Pages'),
                'icon'        => 'fa fa-file',
                'link'        => url('backend_static_pages_list'),
                'module_name' => 'STATIC_PAGES_MANAGER',
                'privilege'   => 'STATIC_PAGE_ADMIN',
            ],

            [
                'id'          => 'letter_manager',
                'title'       => _('Letter to the editor'),
                'icon'        => 'fa fa-envelope',
                'link'        => url('backend_letters_list'),
                'module_name' => 'LETTER_MANAGER',
                'privilege'   => 'LETTER_ADMIN',
            ],

            [
                'id'          => 'category_manager',
                'title'       => _('Categories'),
                'icon'        => 'fa fa-bookmark',
                'link'        => url('backend_categories_list'),
                'module_name' => 'CATEGORY_MANAGER',
                'privilege'   => 'CATEGORY_ADMIN',
            ],

        ],
    ],

    // Media menu
    [
        'id'      => 'media_manager',
        'link'    => '#',
        'title'   => _('Media'),
        'icon'    => 'fa fa-image',
        'submenu' => [
            [
                'id'          => 'photo_manager',
                'title'       => _('Photos'),
                'icon'        => 'fa fa-picture-o',
                'link'        => url('backend_photos_list'),
                'module_name' => 'IMAGE_MANAGER',
                'privilege'   => 'PHOTO_ADMIN',
            ],
            [
                'id'          => 'file_manager',
                'title'       => _('Files'),
                'icon'        => 'fa fa-paperclip',
                'link'        => url('backend_attachments_list'),
                'module_name' => 'FILE_MANAGER',
                'privilege'   => 'ATTACHMENT_ADMIN',
            ],
            [
                'id'          => 'video_manager',
                'title'       => _('Videos'),
                'icon'        => 'fa fa-film',
                'link'        => url('backend_videos_list'),
                'module_name' => 'VIDEO_MANAGER',
                'privilege'   => 'VIDEO_ADMIN',
            ],
            [
                'id'          => 'album_manager',
                'title'       => _('Albums'),
                'icon'        => 'fa fa-camera',
                'link'        => url('backend_albums_list'),
                'module_name' => 'ALBUM_MANAGER',
                'privilege'   => 'ALBUM_ADMIN',
            ],
            [
                'id'          => 'newsstand_manager',
                'title'       => _('News Stand'),
                'icon'        => 'fa fa-newspaper-o',
                'link'        => url('backend_newsstands_list'),
                'module_name' => 'KIOSKO_MANAGER',
                'privilege'   => 'KIOSKO_ADMIN',
            ],
        ],
    ],

    [
        'id'          => 'subscription',
        'link'        => '#',
        'title'       => _('Subscriptions'),
        'module_name' => 'CONTENT_SUBSCRIPTIONS',
        'privilege'   => 'CONTENT_SUBSCRIPTIONS_LIST',
        'icon'        => 'fa fa-check-square-o',
        'submenu'     => [
            [
                'id'          => 'subscriber',
                'link'        => url('backend_subscribers_list'),
                'title'       => _('Subscribers'),
                'icon'        => 'fa fa-address-card',
                'module_name' => 'CONTENT_SUBSCRIPTIONS',
                'privilege'   => 'CONTENT_SUBSCRIPTIONS_LIST',
            ],
            [
                'id'          => 'lists',
                'link'        => url('backend_subscriptions_list'),
                'title'       => _('Lists'),
                'icon'        => 'fa fa-list',
                'module_name' => 'CONTENT_SUBSCRIPTIONS',
                'privilege'   => 'CONTENT_SUBSCRIPTIONS_LIST',
            ]
        ]
    ],

    [
        'id'          => 'newsletter_manager',
        'title'       => _('Newsletters'),
        'icon'        => 'fa fa-envelope',
        'link'        => url('backend_newsletters_list'),
        'module_name' => 'NEWSLETTER_MANAGER',
        'privilege'   => 'NEWSLETTER_ADMIN',
    ],

    [
        'id'          => 'webpush_notifications_manager',
        'title'       => _('Web Push'),
        'icon'        => 'fa fa-bell',
        'link'        => '#',
        'privilege'   => 'WEBPUSH_ADMIN',
        'submenu' => [
            [
                'id'          => 'webpush_notifications_dashboard_manager',
                'title'       => _('Dashboard'),
                'icon'        => 'fa fa-tachometer',
                'link'        => url('backend_webpush_notifications_dashboard'),
                'privilege'   => 'WEBPUSH_ADMIN'
            ],
            [
                'id'          => 'webpush_notifications_history_manager',
                'title'       => _('History'),
                'icon'        => 'fa fa-history',
                'link'        => url('backend_webpush_notifications_list'),
                'privilege'   => 'WEBPUSH_ADMIN'
            ],
            [
                'id'          => 'webpush_notifications_config_manager',
                'title'       => _('Configuration'),
                'icon'        => 'fa fa-cog fa-lg',
                'link'        => url('backend_webpush_notifications_config'),
                'module_name' => 'es.openhost.module.webpush_notifications',
                'privilege'   => 'MASTER'
            ],
        ]
    ],

    // PressClipping

    [
        'id'          => 'pressclipping_manager',
        'title'       => _('PressClipping'),
        'icon'        => 'fa fa-paperclip',
        'link'        => '#',
        'module_name' => 'es.openhost.module.pressclipping',
        'submenu' => [
            [
                'id'          => 'pressclipping_history_manager',
                'title'       => _('History'),
                'icon'        => 'fa fa-history',
                'link'        => url('backend_pressclipping_dashboard'),
                'privilege'   => 'PRESSCLIPPING_ADMIN'
            ],
            [
                'id'          => 'pressclipping_config_manager',
                'title'       => _('Configuration'),
                'icon'        => 'fa fa-cog fa-lg',
                'link'        => url('backend_pressclipping_settings'),
                'privilege'   => 'PRESSCLIPPING_ADMIN'
            ],
        ]
    ],
    [
        'id'          => 'onmai',
        'title'       => _('ONM AI'),
        'badge'       => '<span class="badge badge-primary pull-left m-t-10 m-r-5">' . _('NEW') . '</span> ',
        'icon'        => 'fa fa-bell',
        'link'        => '#',
        'module_name' => 'es.openhost.module.openai',
        'privilege'   => 'ADMIN',
        'submenu' => [
            [
                'id'          => 'openai_dashboard',
                'title'       => _('Dashboard'),
                'icon'        => 'fa fa-tachometer',
                'link'        => url('backend_openai_dashboard'),
                'module_name' => 'es.openhost.module.openai',
                'privilege'   => 'ADMIN'
            ],
            [
                'id'          => 'openai_promts',
                'title'       => _('Prompts'),
                'icon'        => 'fa fa-terminal fa-lg',
                'link'        => url('backend_openai_prompts_list'),
                'module_name' => 'es.openhost.module.openai',
                'privilege'   => 'ADMIN'
            ],
            [
                'id'          => 'openai_config',
                'title'       => _('Configuration'),
                'icon'        => 'fa fa-cog fa-lg',
                'link'        => url('backend_openai_config'),
                'module_name' => 'es.openhost.module.openai',
                'privilege'   => 'ADMIN'
            ]
        ]
    ],
    // Utils menu
    [
        'id'      => 'util',
        'link'    => '#',
        'title'   => _('Utilities'),
        'icon'    => 'fa fa-wrench',
        'submenu' => [
            [
                'id'          => 'advanced_search',
                'title'       => _('Global Search'),
                'icon'        => 'fa fa-search',
                'link'        => url('admin_search'),
                'module_name' => 'ADVANCED_SEARCH',
                'privilege'   => 'SEARCH_ADMIN',
            ],
            [
                'id'          => 'trash_manager',
                'title'       => _('Trash'),
                'icon'        => 'fa fa-trash-o',
                'link'        => url('admin_trash'),
                'module_name' => 'TRASH_MANAGER',
                'privilege'   => 'TRASH_ADMIN',
            ],
            [
                'id'          => 'keyword_manager',
                'title'       => _('Keywords'),
                'icon'        => 'fa fa-tags',
                'link'        => url('backend_keywords_list'),
                'module_name' => 'KEYWORD_MANAGER',
                'privilege'   => 'KEYWORD_ADMIN',
            ],
            [
                'id'          => 'sync_manager',
                'title'       => _('Sync Instances'),
                'icon'        => 'fa fa-exchange',
                'link'        => url('admin_instance_sync'),
                'module_name' => 'SYNC_MANAGER',
                'privilege'   => 'INSTANCE_SYNC_ADMIN',
            ],
            [
                'id'          => 'news_agency',
                'title'       => _('News Agency'),
                'icon'        => 'fa fa-microphone',
                'link'        => url('backend_news_agency_resource_list'),
                'module_name' => 'NEWS_AGENCY_IMPORTER',
                'privilege'   => 'IMPORT_ADMIN',
            ],
        ],
    ],

    [
        'id'          => 'acl_manager',
        'title'       => _('Users & Groups'),
        'link'        => '#',
        'icon'        => 'fa fa-users',
        'module_name' => 'USER_MANAGER || USER_GROUP_MANAGER',
        'privilege'   => 'USER_ADMIN',
        'submenu'     => [
            [
                'id'          => 'user_manager',
                'title'       => _('Users'),
                'icon'        => 'fa fa-user',
                'link'        => url('backend_users_list'),
                'module_name' => 'USER_MANAGER',
                'privilege'   => 'USER_ADMIN',
            ],
            [
                'id'          => 'user_group_manager',
                'title'       => _('User groups'),
                'icon'        => 'fa fa-users',
                'link'        => url('backend_user_groups_list'),
                'module_name' => 'USER_GROUP_MANAGER',
                'privilege'   => 'USER_ADMIN',
            ],
        ],
    ],
    [
        'id'      => 'store_dropdown',
        'link'    => '#',
        'title'   => _('Store'),
        'icon'    => 'fa fa-shopping-cart',
        'privilege' => 'ADMIN',
        'submenu' => [
            // Disabled Temporary (ONM-8729)
            // [
            //     'id'    => 'store',
            //     'title' => _('Modules'),
            //     'icon'  => 'fa fa-archive',
            //     'link'  => url('admin_store_list'),
            //     'privilege' => 'ADMIN'
            // ],
            // [
            //     'id'          => 'theme-manager',
            //     'title'       => _('Themes'),
            //     'icon'        => 'fa fa-magic',
            //     'link'        => url('backend_theme_list'),
            //     'privilege'   => 'ADMIN',
            // ],
            [
                'id'          => 'domain_manager',
                'title'       => _('Domains'),
                'icon'        => 'fa fa-at',
                'link'        => url('backend_domains_list'),
                'privilege'   => 'ADMIN',
            ]
        ]
    ],
    [
        'id'      => 'faq_and_support',
        'class'   => 'visible-xs',
        'link'    => '#',
        'title'   => _('Help & Support'),
        'icon'    => 'fa fa-support',
        'submenu' => [
            [
                'id'          => 'youtube',
                'title'       => _('Youtube channel'),
                'icon'        => 'fa fa-youtube',
                'link'        => 'http://www.youtube.com/user/OpennemasPublishing',
            ],
            [
                'id'          => 'faq',
                'title'       => _('FAQ'),
                'link'        => 'http://help.opennemas.com',
                'icon'        => 'fa fa-question-circle',
            ],
            [
                'id'          => 'support',
                'title'       => _('Contact support'),
                'icon'        => 'fa fa-support',
                'link'        => 'javascript:UserVoice.showPopupWidget();',
            ],
        ]
    ],

    [
        'id'          => 'settings_manager',
        'title'       => _('Settings'),
        'icon'        => 'fa fa-cogs',
        'link'        => '#',
        'module_name' => 'SETTINGS_MANAGER',
        'privilege'   => 'ONM_SETTINGS',
        'submenu' => [
            [
                'id'          => 'urls',
                'title'       => _('URLs'),
                'link'        => url('backend_urls_list'),
                'icon'        => 'fa fa-globe',
                'privilege'   => 'MASTER'
            ],
            [
                'id'          => 'cache',
                'title'       => _('Cache manager'),
                'icon'        => 'fa fa-database',
                'link'        => url('backend_cache_list'),
                'privilege'   => 'MASTER'
            ],
            [
                'id'          => 'general_settings',
                'title'       => _('General & SEO'),
                'icon'        => 'fa fa-cog',
                'link'        => url('backend_settings_general'),
            ],
            [
                'id'          => 'appearance_settings',
                'title'       => _('Appearance'),
                'icon'        => 'fa fa-magic',
                'link'        => url('backend_settings_appearance'),
            ],
            [
                'id'          => 'language_settings',
                'title'       => _('Language & time'),
                'icon'        => 'fa fa-globe',
                'link'        => url('backend_settings_language'),
            ],
            [
                'id'          => 'internal_settings',
                'title'       => _('Internal'),
                'icon'        => 'fa fa-cube',
                'link'        => url('backend_settings_internal'),
            ],
            [
                'id'          => 'services_settings',
                'title'       => _('External services'),
                'icon'        => 'fa fa-cloud',
                'link'        => url('backend_settings_external'),
            ],
            [
                'id'          => 'sitemap_settings',
                'title'       => _('Sitemap'),
                'icon'        => 'fa fa-sitemap',
                'link'        => url('backend_settings_sitemap'),
                'privilege'   => 'MASTER'
            ],
            [
                'id'          => 'masters_settings',
                'title'       => _('Only masters'),
                'icon'        => 'fa fa-rebel',
                'link'        => url('backend_settings_master'),
                'privilege'   => 'MASTER'
            ],
            [
                'id'          => 'theme_settings',
                'title'       => _('Theme settings'),
                'icon'        => 'fa fa-eye',
                'theme'       => [ 'apolo' ],
                'link'        => url('backend_settings_theme'),
                'privilege'   => 'MASTER'
            ]
        ]
    ]
];

return $menuXml;
