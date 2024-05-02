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
        'show_category',
        'show_subtitle',
        'show_summary',
        'show_author',
        'show_date',
        'show_time',
        'show_readtime',
        'show_author_photo',
        'show_author_bio',
        'show_opinion_media',
        'show_opinion_summary',
        'show_opinion_author',
        'show_opinion_author_photo',
        'show_opinion_author_bio',
        'related_contents_auto',
        'sidebar_widget_today_news',
        'sidebar_widget_most_viewed',
        'sidebar_widget_most_seeing_recent',
        'widget_more_in_section',
        'widget_more_in_frontpage',
        'archive_cover',
        'mobile_top_menu',
        'mobile_main_menu',
    ];

    protected $generalSettings = [
        'breadcrumb',
        'main_logo_size',
        'general_page_width',
        'general_footer_widget',
        'hamburger_position',
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
        'mobile_logo_size',
        'mobile_top_menu',
        'mobile_main_menu',
        'mobile_inner_aperture',
        'aspect_ratio' => 'content_imageratio_normal',
        'aspect_ratio_tiny' => 'content_imageratio_tiny',
        'aspect_ratio_list' => 'content_imageratio_list',
    ];

    protected $extensionSettings = [
        'frontpages' => [
            'show_category' => 'content_category_name',
            'show_subtitle' => 'content_subtitle',
            'show_summary' => 'content_summary',
            'show_author' => 'content_author',
            'show_date' => 'content_date',
            'show_time' => 'content_time',
            'show_readtime' => 'content_readtime',
            'show_author_photo' => 'content_author_photo',
            'show_author_bio' => 'content_author_bio',
            'show_opinion_media' => 'content_opinion_media',
            'show_opinion_summary' => 'content_opinion_summary',
            'show_opinion_author' => 'content_opinion_author',
            'show_opinion_author_photo' => 'content_opinion_author_photo',
            'show_opinion_author_bio' => 'content_opinion_author_bio',
        ]
    ];

    protected $actionSettings = [
        'list' => [
            'archive_appearance',
            'archive_cover',
            'show_category' => 'archive_category_name',
            'show_subtitle' => 'archive_subtitle',
            'show_summary' => 'archive_summary',
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

            $themeOptions = array_key_exists('params', $skinParams)
                && array_key_exists('options', $skinParams['params'])
                ? $skinParams['params']['options']
                : [];

            if ($maped && !empty($themeOptions)) {
                $themeOptions = array_map(function ($option) {
                    $option = $option['default'];
                    return $option;
                }, $themeOptions);
            }
        }

        return $themeOptions;
    }

    public function getThemeVariables()
    {
        $action    = $this->container->get('core.globals')->getAction();
        $extension = $this->container->get('core.globals')->getExtension();

        $action = strpos($action, 'list') !== false ? 'list' : $action;
        $action = strpos($action, 'show') !== false ? 'show' : $action;

        //Some weird exceptions
        if ($action == 'archive'
            || $action == 'frontpageauthors'
            || $action == 'authorfrontpage'
            || $extension == 'tag') {
            $action = 'list';
        }

        $currentSettings   = $this->getThemeSettings();
        $generalVariables  = $this->parseSettings($currentSettings, $this->generalSettings);
        $specificVariables = [];

        if (array_key_exists($action, $this->actionSettings)) {
            $specificVariables = $this->parseSettings($currentSettings, $this->actionSettings[$action]);
        }

        if (array_key_exists($extension, $this->extensionSettings)) {
            $specificVariables = $this->parseSettings($currentSettings, $this->extensionSettings[$extension]);
        }

        $finalVars = array_merge($generalVariables, $specificVariables);
        return array_merge($finalVars, [ 'theme_options' => true]);
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
