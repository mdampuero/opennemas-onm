<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

class L10nRouteHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The list of routes that can be localized.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Initializes the L10nRouteHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns the list of localizable routes.
     *
     * @return array The list of localizable routes.
     */
    public function getLocalizableRoutes()
    {
        // Get the list of routes that could be localized
        if (empty($this->routes)) {
            $routes = array_filter(
                $this->container->get('router')->getRouteCollection()->all(),
                function ($route) {
                    return true === $route->getOption('l10n')
                        || 'true' === $route->getOption('l10n');
                }
            );

            $this->routes = array_keys($routes);
        }

        return $this->routes;
    }

    /**
     * Returns if a route is localizable
     *
     * @return bool True if the route is localizable.
     */
    public function isRouteLocalizable($routeName)
    {
        $localizableRoutes = [
            'frontend_album_frontpage',
            'frontend_album_frontpage_category',
            'frontend_album_show',
            'frontend_album_show_amp',
            'frontend_archive_content',
            'frontend_digital_frontpage',
            'frontend_archive',
            'frontend_external_article_show',
            'frontend_article_show',
            'frontend_article_show_amp',
            'frontend_author_frontpage',
            'frontend_frontpage_authors',
            'frontend_blog_frontpage',
            'frontend_blog_author_frontpage',
            'frontend_blog_show',
            'frontend_blog_show_amp',
            'category_frontpage',
            'frontend_company_show',
            'frontend_companies',
            'frontend_content_print',
            'frontend_events',
            'frontend_event_show',
            'frontend_participa_frontpage',
            'frontend_participa_form',
            'frontend_frontpage',
            'frontend_frontpage_category',
            'frontend_letter_frontpage',
            'frontend_letter_form',
            'frontend_letter_show',
            'frontend_newsletter_subscribe_show',
            'frontend_newsletter_subscribe_es',
            'frontend_newsletter_subscribe_create',
            'frontend_newsstand_frontpage',
            'frontend_newsstand_frontpage_date',
            'frontend_newsstand_show',
            'frontend_opinion_show',
            'frontend_opinion_show_amp',
            'frontend_opinion_author_frontpage',
            'frontend_opinion_frontpage',
            'frontend_obituaries',
            'frontend_obituary_show',
            'frontend_obituary_show_amp',
            'frontend_poll_frontpage',
            'frontend_poll_frontpage_category',
            'frontend_poll_show',
            'frontend_poll_show_amp',
            'frontend_rss_facebook_instant_articles',
            'frontend_rss_showcase',
            'frontend_rss_listing',
            'frontend_rss_author',
            'frontend_rss_frontpage',
            'frontend_rss_frontpage_category',
            'frontend_rss',
            'frontend_rss_most_viewed',
            'frontend_rss_type',
            'frontend_rss_type_category',
            'frontend_rss_category',
            'frontend_search_internal',
            'frontend_static_page',
            'frontend_suggested_show',
            'frontend_tag_index',
            'frontend_tag_frontpage',
            'frontend_video_frontpage',
            'frontend_video_frontpage_category',
            'frontend_video_show',
            'frontend_video_show_amp',
            'frontend_widget_render',
        ];

        return in_array($routeName, $localizableRoutes);
    }
}
