<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// Load the available route collection
$routes = new RouteCollection();

$frontendRoutes = new RouteCollection();

// Common content management routes
$frontendRoutes->add(
    'frontend_ad_get',
    new Route(
        '/ads/get/{id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:AdvertisementController:get',
            '_format' => 'html',
        )
    )
);

$frontendRoutes->add(
    'frontend_ad_redirect',
    new Route(
        '/ads/{id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:AdvertisementController:redirect',
            '_format' => 'html',
        )
    )
);

$frontendRoutes->add(
    'frontend_rss_listing',
    new Route(
        '/rss/listado',
        array(
            '_controller' => 'Frontend:Controllers:RssController:index',
        )
    )
);

$frontendRoutes->add(
    'frontend_rss_opinion_author',
    new Route(
        '/rss/opinion/{author}',
        array(
            '_controller' => 'Frontend:Controllers:RssController:generalRSS',
            'category_name' => 'opinion'
        ),
        array(
            'author' => '\d+',
        )
    )
);

$frontendRoutes->add(
    'frontend_rss_subcategory',
    new Route(
        '/rss/{category_name}/{subcategory_name}',
        array(
            '_controller' => 'Frontend:Controllers:RssController:generalRSS',
        ),
        array(
            'category_name' => '[a-z\d-]+',
            'subcategory_name' => '[a-z\d-]+'
        )
    )
);

$frontendRoutes->add(
    'frontend_rss_category',
    new Route(
        '/rss/{category_name}',
        array(
            '_controller' => 'Frontend:Controllers:RssController:generalRSS',
        ),
        array(
            'category_name' => '[A-Za-z\d-]+',
        )
    )
);

$frontendRoutes->add(
    'frontend_rss',
    new Route(
        '/rss',
        array(
            '_controller' => 'Frontend:Controllers:RssController:generalRSS',
        )
    )
);

$frontendRoutes->add(
    'frontend_robots',
    new Route(
        '/robots.txt',
        array(
            '_controller' => 'Frontend:Controllers:RobotsController:index',
        )
    )
);

$frontendRoutes->add(
    'frontend_sitemapnews',
    new Route(
        '/sitemapnews.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:SitemapController:news',
            '_format' => 'xml'
        ),
        array(
            '_format' => 'xml|xml.gz'
        )
    )
);

$frontendRoutes->add(
    'frontend_sitemapweb',
    new Route(
        '/sitemapweb.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:SitemapController:web',
            '_format' => 'xml'
        ),
        array(
            '_format' => 'xml|xml.gz'
        )
    )
);

$frontendRoutes->add(
    'frontend_sitemapindex',
    new Route(
        '/sitemap.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:SitemapController:index',
            '_format' => 'xml'
        ),
        array(
            '_format' => 'xml|xml.gz'
        )
    )
);

$frontendRoutes->add(
    'frontend_staticpage',
    new Route(
        '/estaticas/{slug}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:StaticPagesController:show',
            '_format' => 'html'
        ),
        array(
            '_format' => 'html|htm',
            'slug' => '[A-Za-z\d-]+',
        )
    )
);



$routes->addCollection($frontendRoutes);
