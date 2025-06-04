<?php

namespace Common\Core\Component\Helper;

use Symfony\Component\DependencyInjection\Container;

/**
* Perform searches in Database Settings data
*/
class ThemeSettingsHelper extends SettingHelper
{
    /**
     * The services container.
     *
     * @var Container
     */
    protected $container;

    protected $aditionalThemeSettings = [
        'custom_css' => ''
    ];

    protected $toBool = [
        'general_topbar',
        'show_category',
        'show_subtitle',
        'show_summary',
        'show_summary_forced',
        'show_author',
        'show_date',
        'show_time',
        'show_readtime',
        'show_author_photo',
        'show_author_bio',
        'show_tiny_category',
        'show_tiny_subtitle',
        'show_tiny_summary',
        'show_tiny_author',
        'show_tiny_author_photo',
        'show_tiny_author_bio',
        'show_tiny_date',
        'show_tiny_time',
        'show_tiny_readtime',
        'show_over_category',
        'show_over_subtitle',
        'show_over_summary',
        'show_over_author',
        'show_over_author_photo',
        'show_over_author_bio',
        'show_over_date',
        'show_over_time',
        'show_over_readtime',
        'show_media',
        'related_contents_auto',
        'sidebar_widget_today_news',
        'sidebar_widget_most_viewed',
        'sidebar_widget_most_seeing_recent',
        'widget_more_in_section',
        'widget_more_in_frontpage',
        'archive_cover',
        'mobile_top_menu',
        'mobile_main_menu',
        'archive_secondary_menu',
        'show_opinion_summary',
        'show_opinion_media',
        'show_opinion_author',
        'show_opinion_author_photo',
        'show_opinion_author_bio',
    ];

    protected $generalSettings = [
        'breadcrumb',
        'progressbar',
        'main_logo_size',
        'general_topbar',
        'general_header_date',
        'general_page_width',
        'general_header_right_widget',
        'general_main_widget',
        'general_footer_widget',
        'hamburger_position',
        'sidebar_widget_custom',
        'widget_header_type',
        'widget_header_font',
        'widget_header_font_color',
        'widget_header_font_size',
        'widget_header_border_position',
        'widget_header_font_weight',
        'widget_header_border_color',
        'main_font_size',
        'main_font_weight',
        'second_font_size',
        'second_font_weight',
        'header_align',
        'header_color',
        'header_border_color',
        'menu_color',
        'menu_link_color',
        'menu_border',
        'menu_border_color',
        'archive_secondary_menu',
        'mobile_logo_size',
        'mobile_top_menu',
        'mobile_main_menu',
        'mobile_inner_aperture',
        'content_author_photo_crop',
        'aspect_ratio' => 'content_imageratio_normal',
        'aspect_ratio_tiny' => 'content_imageratio_tiny',
        'aspect_ratio_list' => 'content_imageratio_list',
    ];

    protected $settingsMap = [
        'general' => [
            'list' => [
                'archive_appearance',
                'archive_cover',
                'show_category' => 'archive_category_name',
                'show_subtitle' => 'archive_subtitle',
                'show_summary' => 'archive_summary',
                'show_summary_forced' => 'content_summary_forced',
                'show_author' => 'archive_author',
                'show_date' => 'archive_date',
                'show_time' => 'archive_time',
                'show_readtime' => 'archive_readtime',
                'show_author_photo' => 'archive_author_photo',
                'show_author_bio' => 'archive_author_bio',
                'show_opinion_media' => 'archive_opinion_media',
                'show_opinion_summary' => 'archive_opinion_summary',
                'show_opinion_author' => 'archive_opinion_author',
                'show_opinion_author_photo' => 'archive_opinion_author_photo',
                'show_opinion_author_bio' => 'archive_opinion_author_bio',
                'sidebar_widget_today_news' => 'sidebar_widget_today_news_list',
                'sidebar_widget_most_viewed' => 'sidebar_widget_most_viewed_list',
                'sidebar_widget_most_seeing_recent' => 'sidebar_widget_most_seeing_recent_list',
            ],
            'show' => [
                'article_header',
                'article_layout',
                'article_header_media',
                'article_header_order',
                'article_header_align',
                'event_info_display',
                'share_tools',
                'tags_display',
                'related_contents',
                'related_contents_auto',
                'related_contents_auto_position',
                'sidebar_widget_today_news' => 'sidebar_widget_today_news_inner',
                'sidebar_widget_most_viewed' => 'sidebar_widget_most_viewed_inner',
                'sidebar_widget_most_seeing_recent' => 'sidebar_widget_most_seeing_recent_inner',
                'widget_more_in_section',
                'widget_more_in_frontpage',
                'widget_more_in_section_layout',
                'widget_more_in_frontpage_layout',
                'show_author' => 'inner_content_author',
                'show_date' => 'inner_content_date',
                'show_time' => 'inner_content_time',
                'show_readtime' => 'inner_content_readtime',
                'show_author_photo' => 'inner_content_author_photo',
                'show_author_bio' => 'inner_content_author_bio',
                'suggested_max_items' => 'inner_content_suggested_items'
            ]
        ],
        'frontpages' => [
            'list' => [
                'show_category' => 'content_category_name',
                'show_subtitle' => 'content_subtitle',
                'show_summary' => 'content_summary',
                'show_summary_forced' => 'content_summary_forced',
                'show_author' => 'content_author',
                'show_date' => 'content_date',
                'show_time' => 'content_time',
                'show_readtime' => 'content_readtime',
                'show_author_photo' => 'content_author_photo',
                'show_author_bio' => 'content_author_bio',
                'show_tiny_category' => 'content_tiny_category_name',
                'show_tiny_subtitle' => 'content_tiny_subtitle',
                'show_tiny_summary' => 'content_tiny_summary',
                'show_tiny_author' => 'content_tiny_author',
                'show_tiny_author_photo' => 'content_tiny_author_photo',
                'show_tiny_author_bio' => 'content_tiny_author_bio',
                'show_tiny_date' => 'content_tiny_date',
                'show_tiny_time' => 'content_tiny_time',
                'show_tiny_readtime' => 'content_tiny_readtime',
                'show_over_category' => 'content_over_category_name',
                'show_over_subtitle' => 'content_over_subtitle',
                'show_over_summary' => 'content_over_summary',
                'show_over_author' => 'content_over_author',
                'show_over_author_photo' => 'content_over_author_photo',
                'show_over_author_bio' => 'content_over_author_bio',
                'show_over_date' => 'content_over_date',
                'show_over_time' => 'content_over_time',
                'show_over_readtime' => 'content_over_readtime',
                'show_opinion_summary' => 'content_opinion_summary',
                'show_opinion_media' => 'content_opinion_media',
                'show_opinion_author' => 'content_opinion_author',
                'show_opinion_author_photo' => 'content_opinion_author_photo',
                'show_opinion_author_bio' => 'content_opinion_author_bio',
            ],
            'show' => [
                'show_category' => 'content_category_name',
                'show_subtitle' => 'content_subtitle',
                'show_summary' => 'content_summary',
                'show_summary_forced' => 'content_summary_forced',
                'show_author' => 'content_author',
                'show_date' => 'content_date',
                'show_time' => 'content_time',
                'show_readtime' => 'content_readtime',
                'show_author_photo' => 'content_author_photo',
                'show_author_bio' => 'content_author_bio',
                'show_tiny_category' => 'content_tiny_category_name',
                'show_tiny_subtitle' => 'content_tiny_subtitle',
                'show_tiny_summary' => 'content_tiny_summary',
                'show_tiny_author' => 'content_tiny_author',
                'show_tiny_author_photo' => 'content_tiny_author_photo',
                'show_tiny_author_bio' => 'content_tiny_author_bio',
                'show_tiny_date' => 'content_tiny_date',
                'show_tiny_time' => 'content_tiny_time',
                'show_tiny_readtime' => 'content_tiny_readtime',
                'show_over_category' => 'content_over_category_name',
                'show_over_subtitle' => 'content_over_subtitle',
                'show_over_summary' => 'content_over_summary',
                'show_over_author' => 'content_over_author',
                'show_over_author_photo' => 'content_over_author_photo',
                'show_over_author_bio' => 'content_over_author_bio',
                'show_over_date' => 'content_over_date',
                'show_over_time' => 'content_over_time',
                'show_over_readtime' => 'content_over_readtime',
                'show_opinion_summary' => 'content_opinion_summary',
                'show_opinion_media' => 'content_opinion_media',
                'show_opinion_author' => 'content_opinion_author',
                'show_opinion_author_photo' => 'content_opinion_author_photo',
                'show_opinion_author_bio' => 'content_opinion_author_bio',
            ]
        ],
        'opinion' => [
            'show' => [
            ],
            'list' => [
                'show_summary' => 'archive_opinion_summary',
                'show_summary_forced' => 'content_summary_forced',
                'show_media' => 'archive_opinion_media',
                'show_author' => 'archive_opinion_author',
                'show_author_photo' => 'archive_opinion_author_photo',
                'show_author_bio' => 'archive_opinion_author_bio',
            ]
        ],
    ];

    /**
     * Initializes the SettingLogoHelper.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->em        = $container->get('orm.manager');
    }

    public function getThemeSettings($base = false, $maped = true)
    {
        $themeOptions = $this->em
            ->getDataSet('Settings', 'instance')
            ->get('theme_options', []);

        if (empty($themeOptions) || $base) {
            $skin = $this->em
                ->getDataSet('Settings', 'instance')
                ->get('theme_skin', 'default');

            $skinParams = $this->container->get('core.theme')->getSkin($skin);

            $themeOptions = $skinParams['params']['options'] ?? [];

            if ($maped && !empty($themeOptions)) {
                $themeOptions = array_map(function ($option) {
                    $option = $option['default'];
                    return $option;
                }, $themeOptions);
            }
        }

        return $themeOptions;
    }

    public function getThemeVariables($action = null, $extension = null)
    {
        $action    = !empty($action) ? $action : $this->container->get('core.globals')->getAction();
        $extension = !empty($extension) ? $extension : $this->container->get('core.globals')->getExtension();

        $action = strpos($action, 'list') !== false ? 'list' : $action;
        $action = strpos($action, 'show') !== false ? 'show' : $action;

        //Some weird exceptions
        if ($action == 'archive'
            || $action == 'frontpageauthors'
            || $action == 'authorfrontpage'
            || $extension == 'tag') {
            $action = 'list';
        }
        //Treat archive as a fake frontpage
        if ($action == 'archive') {
            $extension = 'frontpages';
        }

        $themeSettings  = $this->getThemeSettings();
        $targetSettings = $this->getMappedSettings($extension, $action);

        return $this->parseSettings($themeSettings, $targetSettings);
    }

    protected function getMappedSettings($extension, $action)
    {
        $action = $action == 'show' ? 'show' : 'list';

        $targetSettings = array_merge($this->generalSettings, $this->settingsMap['general'][$action]);

        if (array_key_exists($extension, $this->settingsMap)) {
            $targetSettings = array_merge($targetSettings, $this->settingsMap[$extension][$action]);
        }

        return $targetSettings;
    }

    protected function parseSettings($master, $part)
    {
        $result = [];
        if (empty($master)) {
            return $result;
        }

        foreach ($part as $key => $value) {
            if (array_key_exists($value, $master)) {
                $key = is_integer($key) ? $value : $key;

                $result[$key] = in_array($key, $this->toBool)
                    ? filter_var($master[$value], FILTER_VALIDATE_BOOLEAN)
                    : $master[$value];
            }
        }

        return $result;
    }
}
