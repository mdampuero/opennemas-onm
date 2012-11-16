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

$frontendRoutes->add(
    'frontend_search_internal',
    new Route(
        '/search/',
        array(
            '_controller' => 'Frontend:Controllers:SearchController:internal',
        )
    )
);

$frontendRoutes->add(
    'frontend_search_google',
    new Route(
        '/search/google',
        array(
            '_controller' => 'Frontend:Controllers:SearchController:google',
        )
    )
);

$frontendRoutes->add(
    'frontend_rating_vote',
    new Route(
        '/ratings/vote',
        array(
            '_controller' => 'Frontend:Controllers:RatingsController:vote',
        )
    )
);

$frontendRoutes->add(
    'frontend_comments_get',
    new Route(
        '/comments/get',
        array(
            '_controller' => 'Frontend:Controllers:CommentsController:get',
        )
    )
);

$frontendRoutes->add(
    'frontend_comments_vote',
    new Route(
        '/comments/vote',
        array(
            '_controller' => 'Frontend:Controllers:CommentsController:vote',
        )
    )
);

$frontendRoutes->add(
    'frontend_comments_paginate',
    new Route(
        '/comments/paginate',
        array(
            '_controller' => 'Frontend:Controllers:CommentsController:paginateComments',
        )
    )
);

$frontendRoutes->add(
    'frontend_comments_save',
    new Route(
        '/comments/save',
        array(
            '_controller' => 'Frontend:Controllers:CommentsController:save',
        )
    )
);

$frontendRoutes->add(
    'frontend_redirect_content',
    new Route(
        '/redirect/content',
        array(
            '_controller' => 'Frontend:Controllers:RedirectorsController:content',
        )
    )
);

$frontendRoutes->add(
    'frontend_redirect_category',
    new Route(
        '/redirect/category',
        array(
            '_controller' => 'Frontend:Controllers:RedirectorsController:category',
        )
    )
);

$frontendRoutes->add(
    'frontend_playground',
    new Route(
        '/playground/{action}',
        array(
            '_controller' => 'Frontend:Controllers:PlaygroundController:frontpage',
        )
    )
);


$frontendRoutes->add(
    'frontend_books',
    new Route(
        '/libros',
        array(
            '_controller' => 'Frontend:Controllers:BooksController:frontpage',
        )
    )
);

$frontendRoutes->add(
    'frontend_book_show',
    new Route(
        '/libro/{id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:BooksController:show',
            '_format'     => 'html',
            'id'          => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_book_show_with_slug',
    new Route(
        '/libro/{category_name}/{slug}/{id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:BooksController:show',
            '_format' => 'html',
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '([a-z0-9\-]+)?',
            'id'            => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_books_widget',
    new Route(
        '/libros/widget',
        array(
            '_controller' => 'Frontend:Controllers:BooksController:ajaxPaginationList',
        )
    )
);

$frontendRoutes->add(
    'frontend_album_ajax',
    new Route(
        '/album/ajax',
        array(
            '_controller' => 'Frontend:Controllers:AlbumsController:ajaxPaginated',
        )
    )
);

$frontendRoutes->add(
    'frontend_album_frontpage',
    new Route(
        '/album/{page}',
        array(
            '_controller' => 'Frontend:Controllers:AlbumsController:frontpage',
            'page' => 1
        ),
        array(
            'page' => '([0-9]+)?'
        )
    )
);

$frontendRoutes->add(
    'frontend_album_frontpage_category',
    new Route(
        '/album/{category_name}/{page}',
        array(
            '_controller' => 'Frontend:Controllers:AlbumsController:frontpage',
            'page' => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'page'          => '([0-9]+)?'
        )
    )
);

$frontendRoutes->add(
    'frontend_album_show_with_date_slug',
    new Route(
        '/album/{category_name}/{date}/{slug}/{album_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:AlbumsController:show',
            '_format' => 'html',
            'page' => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'date'          => '([0-9]{4})-([0-1][0-9])-([0-3][0-9])',
            'slug'          => '[a-z0-9\-]+',
            'album_id'      => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_album_show_with_date_slug',
    new Route(
        '/album/{category_name}/{slug}/{album_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:AlbumsController:show',
            '_format' => 'html',
            'page' => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'album_id'      => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_monograph_frontpage',
    new Route(
        '/especiales/{page}',
        array(
            '_controller' => 'Frontend:Controllers:MonographsController:frontpage',
            'page' => 1
        ),
        array(
            'page' => '([0-9]+)?'
        )
    )
);

$frontendRoutes->add(
    'frontend_monograph_frontpage_category',
    new Route(
        '/especiales/{category_name}/{page}',
        array(
            '_controller' => 'Frontend:Controllers:MonographsController:frontpage',
            'page' => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'page'          => '([0-9]+)?'
        )
    )
);

$frontendRoutes->add(
    'frontend_monograph_show',
    new Route(
        '/especiales/{category_name}/{slug}/{special_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:MonographsController:show',
            '_format' => 'html',
            'page' => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'special_id'    => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_poll_frontpage',
    new Route(
        '/encuesta/{page}',
        array(
            '_controller' => 'Frontend:Controllers:PollsController:frontpage',
            'page'        => 1
        ),
        array(
            'page' => '([0-9]+)?'
        )
    )
);

$frontendRoutes->add(
    'frontend_poll_frontpage_category',
    new Route(
        '/encuesta/{category_name}/{page}',
        array(
            '_controller' => 'Frontend:Controllers:PollsController:frontpage',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'page'          => '([0-9]+)?'
        )
    )
);

$frontendRoutes->add(
    'frontend_poll_show',
    new Route(
        '/encuesta/{category_name}/{slug}/{id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:PollsController:show',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'id'            => '([0-9]+)'
        )
    )
);

$routes->addCollection($frontendRoutes);

