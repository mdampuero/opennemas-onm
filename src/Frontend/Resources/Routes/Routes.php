<?php
/**
 * Defines all the routes for frontend interface
 *
 * @package  Frontend
 */
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

$frontendRoutes = new RouteCollection();

$frontendRoutes->add(
    'frontend_paywall_showcase',
    new Route(
        '/paywall',
        array(
            '_controller' => 'Frontend:Controllers:PaywallController:showcase',
        )
    )
);

$frontendRoutes->add(
    'frontend_paywall_prepare_payment',
    new Route(
        '/paywall/prepare_payment',
        array(
            '_controller' => 'Frontend:Controllers:PaywallController:preparePayment',
        )
    )
);

$frontendRoutes->add(
    'frontend_paywall_success_payment',
    new Route(
        '/paywall/success_payment',
        array(
            '_controller' => 'Frontend:Controllers:PaywallController:returnSuccessPayment',
        )
    )
);

$frontendRoutes->add(
    'frontend_paywall_cancel_payment',
    new Route(
        '/paywall/cancel_payment',
        array(
            '_controller' => 'Frontend:Controllers:PaywallController:returnCancelPayment',
        )
    )
);



$frontendRoutes->add(
    'frontend_newsletter_subscribe_show',
    new Route(
        '/newsletter',
        array(
            '_controller' => 'Frontend:Controllers:SubscriptionsController:show',
        )
    )
);

$frontendRoutes->add(
    'frontend_newsletter_subscribe_es',
    new Route(
        '/boletin',
        array(
            '_controller' => 'Frontend:Controllers:SubscriptionsController:show',
        )
    )
);

$frontendRoutes->add(
    'frontend_newsletter_subscribe_create',
    new Route(
        '/newsletter/subscription/create',
        array(
            '_controller' => 'Frontend:Controllers:SubscriptionsController:create',
        )
    )
);

$frontendRoutes->add(
    'frontend_auth_login',
    new Route(
        '/login',
        array(
            '_controller' => 'Frontend:Controllers:AuthenticationController:login',
        )
    )
);

$frontendRoutes->add(
    'frontend_auth_logout',
    new Route(
        '/logout',
        array(
            '_controller' => 'Frontend:Controllers:AuthenticationController:logout',
        )
    )
);

$frontendRoutes->add(
    'frontend_user_register',
    new Route(
        '/user/register',
        array(
            '_controller' => 'Frontend:Controllers:UserController:register',
        )
    )
);

$frontendRoutes->add(
    'frontend_user_show',
    new Route(
        '/user/me',
        array(
            '_controller' => 'Frontend:Controllers:UserController:show',
        )
    )
);

$frontendRoutes->add(
    'frontend_user_update',
    new Route(
        '/user/update',
        array(
            '_controller' => 'Frontend:Controllers:UserController:update',
        )
    )
);

$frontendRoutes->add(
    'frontend_user_recoverpass',
    new Route(
        '/user/recover-pass',
        array(
            '_controller' => 'Frontend:Controllers:UserController:recoverPassword',
        )
    )
);

$frontendRoutes->add(
    'frontend_user_recoverusername',
    new Route(
        '/user/recover-user',
        array(
            '_controller' => 'Frontend:Controllers:UserController:recoverUsername',
        )
    )
);

$frontendRoutes->add(
    'frontend_user_resetpass',
    new Route(
        '/user/reset-pass/{token}',
        array(
            '_controller' => 'Frontend:Controllers:UserController:regeneratePassword',
        )
    )
);

$frontendRoutes->add(
    'frontend_user_activate',
    new Route(
        '/user/activate/{token}',
        array(
            '_controller' => 'Frontend:Controllers:UserController:activate',
        )
    )
);

$frontendRoutes->add(
    'frontend_user_menu',
    new Route(
        '/user/get/menu',
        array(
            '_controller' => 'Frontend:Controllers:UserController:getUserMenu',
        )
    )
);

$frontendRoutes->add(
    'frontend_user_user_box',
    new Route(
        '/user/user_box',
        array(
            '_controller' => 'Frontend:Controllers:UserController:userBox',
        )
    )
);

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
    'frontend_comments_ajax',
    new Route(
        '/comments/ajax',
        array(
            '_controller' => 'Frontend:Controllers:CommentsController:ajax',
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
            '_controller' => 'Frontend:Controllers:PlaygroundController:default',
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
            'page'        => 1
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
            'page'        => 1
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
            '_format'     => 'html',
            'page'        => 1
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
            '_format'     => 'html',
            'page'        => 1
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
        '/{component}/{page}',
        array(
            '_controller' => 'Frontend:Controllers:PollsController:frontpage',
            'page'        => 1
        ),
        array(
            'page'      => '([0-9]+)?',
            'component' => 'encuesta|poll|enquerito|enquisa',
        )
    )
);



$frontendRoutes->add(
    'frontend_poll_vote',
    new Route(
        '/poll/addvote/{id}',
        array(
            '_controller' => 'Frontend:Controllers:PollsController:addVote',
        ),
        array(
            'id'            => '([0-9]+)',
        )
    )
);

$frontendRoutes->add(
    'frontend_poll_frontpage_category',
    new Route(
        '/{component}/{category_name}/{page}',
        array(
            '_controller' => 'Frontend:Controllers:PollsController:frontpage',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'page'          => '([0-9]+)?',
            'component' => 'encuesta|poll|enquerito|enquisa',
        )
    )
);

$frontendRoutes->add(
    'frontend_poll_show',
    new Route(
        '/{component}/{category_name}/{slug}/{id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:PollsController:show',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'id'            => '([0-9]+)',
            'component' => 'encuesta|poll|enquerito|enquisa',
        )
    )
);

$frontendRoutes->add(
    'frontend_archive_content',
    new Route(
        '/archive/content/{year}/{month}/{day}',
        array(
            '_controller' => 'Frontend:Controllers:ArchiveController:archive',
            'page'        => 1
        ),
        array(
            'year'       => '[0-9]+',
            'month'      => '[0-9]+',
            'day'        => '[0-9]+',
        )
    )
);

$frontendRoutes->add(
    'frontend_archive_category',
    new Route(
        '/archive/content/{year}/{month}/{day}/{category_name}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:ArchiveController:archiveCategory',
            'page'        => 1
        ),
        array(
            'category_name'  => '[a-z0-9\-]+',
            'year'       => '([0-9]+)',
            'month'      => '([0-9]+)',
            'day'        => '([0-9]+)',
        )
    )
);


$frontendRoutes->add(
    'frontend_digital_frontpage',
    new Route(
        '/archive/digital/{year}/{month}/{day}/{category_name}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:ArchiveController:digitalFrontpage',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'year'       => '([0-9]+)',
            'month'      => '([0-9]+)',
            'day'        => '([0-9]+)',
        )
    )
);

$frontendRoutes->add(
    'frontend_letter_frontpage',
    new Route(
        '/cartas-al-director',
        array(
            '_controller' => 'Frontend:Controllers:LetterController:frontpage',
        )
    )
);

$frontendRoutes->add(
    'frontend_letter_show',
    new Route(
        '/cartas-al-director/{author}/{slug}/{id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:LetterController:show',
            '_format' => 'html',
        ),
        array(
            'author'   => '([a-z0-9\-]+)',
            'slug'     => '[a-z0-9\-]+',
            'video_id' => '([0-9]+)'
        )
    )
);


$frontendRoutes->add(
    'frontend_letter_save',
    new Route(
        '/cartas-al-director/save',
        array(
            '_controller' => 'Frontend:Controllers:LetterController:save',

        )
    )
);


$frontendRoutes->add(
    'frontend_video_ajax_more',
    new Route(
        '/video/more/{category}',
        array(
            '_controller' => 'Frontend:Controllers:VideosController:ajaxMore',
            'page' => 1
        ),
        array(
            'category' => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_video_ajax_incategory',
    new Route(
        '/video/incategory/{category}',
        array(
            '_controller' => 'Frontend:Controllers:VideosController:ajaxInCategory',
            'page' => 1
        ),
        array(
            'category' => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_video_frontpage',
    new Route(
        '/video/{page}',
        array(
            '_controller' => 'Frontend:Controllers:VideosController:frontpage',
            'page' => 1
        ),
        array(
            'page' => '([0-9]+)?'
        )
    )
);

$frontendRoutes->add(
    'frontend_video_frontpage_category',
    new Route(
        '/video/{category_name}/{page}',
        array(
            '_controller' => 'Frontend:Controllers:VideosController:frontpage',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'page'          => '([0-9]+)?'
        )
    )
);

$frontendRoutes->add(
    'frontend_video_show_with_slug',
    new Route(
        '/video/{category_name}/{slug}/{video_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:VideosController:show',
            '_format' => 'html',
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'video_id'      => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_video_show_with_date_slug',
    new Route(
        '/video/{category_name}/{date}/{slug}/{video_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:VideosController:show',
            '_format' => 'html',
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'date'          => '([0-9]{4})-([0-1][0-9])-([0-3][0-9])',
            'slug'          => '[a-z0-9\-]+',
            'video_id'      => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_article_show',
    new Route(
        '/articulo/{category_name}/{slug}/{article_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:ArticlesController:show',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'article_id'    => '([0-9]+)',
            '_format'       => 'html|htm'
        )
    )
);

$frontendRoutes->add(
    'frontend_external_article_show',
    new Route(
        '/extarticulo/{category_name}/{slug}/{article_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:ArticlesController:extShow',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'article_id'    => '([0-9]+)',
            '_format'       => 'html|htm'
        )
    )
);

$frontendRoutes->add(
    'frontend_article_show_gl',
    new Route(
        '/artigo/{category_name}/{slug}/{article_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:ArticlesController:show',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'article_id'    => '([0-9]+)',
            '_format'       => 'html|htm'
        )
    )
);

$frontendRoutes->add(
    'frontend_article_show_old_with_date',
    new Route(
        '/articulo/{category_name}/{date}/{slug}/{article_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:ArticlesController:show',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'article_id'    => '([0-9]+)',
            '_format'       => 'html|htm',
            'date'          => '([0-9]{4})-([0-1][0-9])-([0-3][0-9])',
        )
    )
);

$frontendRoutes->add(
    'frontend_content_print',
    new Route(
        '/content/print/{slug}/{content_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:ContentsController:print',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'slug'          => '[a-z0-9\-]+',
            'content_id'    => '([0-9]+)',
        )
    )
);

$frontendRoutes->add(
    'frontend_external_content_print',
    new Route(
        '/content/extprint/{category_name}/{slug}/{content_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:ContentsController:extPrint',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'content_id'    => '([0-9]+)',
        )
    )
);

$frontendRoutes->add(
    'frontend_content_share_by_mail',
    new Route(
        '/content/share-by-email/{content_id}',
        array(
            '_controller' => 'Frontend:Controllers:ContentsController:shareByEmail',
        )
    )
);
$frontendRoutes->add(
    'frontend_content_vote',
    new Route(
        '/content/rate',
        array(
            '_controller' => 'Frontend:Controllers:ContentsController:rateContent',
        )
    )
);

$frontendRoutes->add(
    'frontend_content_stats',
    new Route(
        '/content/stats',
        array(
            '_controller' => 'Frontend:Controllers:ContentsController:stats',
        )
    )
);

$frontendRoutes->add(
    'frontend_newstand_frontpage',
    new Route(
        '/portadas-papel',
        array(
           '_controller' => 'Frontend:Controllers:NewStandController:frontpage',
       )
    )
);

$frontendRoutes->add(
    'frontend_newstand_frontpage_date',
    new Route(
        '/portadas-papel/{year}/{month}/{day}',
        array(
           '_controller' => 'Frontend:Controllers:NewStandController:frontpage',
           'month'       => 0,
           'day'         => 1,
        ),
        array(
           'year'   => '[0-9]{4}',
           'month'  => '[0-9]{1,2}',
           'day'    => '[0-3][0-9]'
       )
    )
);

$frontendRoutes->add(
    'frontend_newstand_frontpage_category',
    new Route(
        '/portadas-papel/{category_name}/{year}/{month}',
        array(
           '_controller' => 'Frontend:Controllers:NewStandController:frontpage',
        ),
        array(
           'category_name'  => '[a-z0-9\-]+',
           'year'           => '([0-9]{4})',
           'month'          => '([0-9]{1,2})'
       )
    )
);

$frontendRoutes->add(
    'frontend_newstand_show',
    new Route(
        '/portadas-papel/{category_name}/{id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:NewStandController:show',
            '_format' => 'html',
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'id'            => '([0-9]+)'
       )
    )
);

$frontendRoutes->add(
    'frontend_newstandPaypal_frontpage',
    new Route(
        '/kiosko',
        array(
           '_controller' => 'Frontend:Controllers:NewStandPaypalController:frontpage',
        )
    )
);

$frontendRoutes->add(
    'frontend_newstandPaypal_frontpage_date',
    new Route(
        '/kiosko/{year}/{month}/{day}',
        array(
           '_controller' => 'Frontend:Controllers:NewStandPaypalController:frontpage',
           'month'       => date('n'),
           'day'         => 1,
        ),
        array(
           'year'   => '[0-9]{4}',
           'month'  => '[0-9]{1,2}',
           'day'    => '[0-3][0-9]'
       )
    )
);

$frontendRoutes->add(
    'frontend_newstandPaypal_frontpage_category',
    new Route(
        '/kiosko/{category_name}/{year}/{month}',
        array(
           '_controller' => 'Frontend:Controllers:NewStandPaypalController:frontpage',
       ),
        array(
           'category_name'  => '[a-z0-9\-]+',
           'year'           => '([0-9]{4})',
           'month'          => '([0-9]{1,2})'
       )
    )
);

$frontendRoutes->add(
    'frontend_newstandPaypal_show',
    new Route(
        '/kiosko/{category_name}/{id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:NewStandPaypalController:show',
            '_format' => 'html',
        ),
        array(
            'category_name' => '[a-z0-9\-]+',
            'id'            => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_opinion_author_frontpage',
    new Route(
        '/opinion/autor/{author_id}/{author_slug}',
        array(
           '_controller' => 'Frontend:Controllers:OpinionsController:frontpageAuthor',
        ),
        array(
           'author_slug'    => '(.*)?',
           'author_id'      => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_opinion_external_author_frontpage',
    new Route(
        '/extopinion/autor/{author_id}/{author_slug}',
        array(
           '_controller' => 'Frontend:Controllers:OpinionsController:extFrontpageAuthor',
        ),
        array(
           'author_slug'    => '(.*)?',
           'author_id'      => '([0-9]+)'
        )
    )
);

$frontendRoutes->add(
    'frontend_opinion_show_with_author_slug',
    new Route(
        '/opinion/{author_name}/{opinion_title}/{opinion_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:OpinionsController:show',
            'author_name' => 'author',
            '_format'     => 'html'
        ),
        array(
            'author_name'    => '[a-z0-9\-]+',
            'opinion_title'  => '[a-z0-9\-]+',
            'opinion_id'     => '[a-z0-9\-]+',
        )
    )
);

$frontendRoutes->add(
    'frontend_opinion_show_with_author_slug_and_date',
    new Route(
        '/opinion/{author_name}/{date}/{opinion_title}/{opinion_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:OpinionsController:show',
            'author_name' => 'author',
            '_format'     => 'html'
        ),
        array(
            'author_name'   => '[a-z0-9\-]+',
            'opinion_title' => '[a-z0-9\-]+',
            'opinion_id'    => '[a-z0-9\-]+',
            'date'          => '([0-9]{4})-([0-1][0-9])-([0-3][0-9])',
        )
    )
);

$frontendRoutes->add(
    'frontend_opinion_show',
    new Route(
        '/opinion/{opinion_title}/{opinion_id}.{_format}',
        array(
           '_controller' => 'Frontend:Controllers:OpinionsController:show',
           '_format'     => 'html'
        ),
        array(
            'opinion_title' => '[a-z0-9\-]+',
            'opinion_id'    => '[a-z0-9\-]+',
       )
    )
);

$frontendRoutes->add(
    'frontend_opinion_external_show_with_author_slug',
    new Route(
        '/extopinion/{author_name}/{opinion_title}/{opinion_id}.{_format}',
        array(
            '_controller' => 'Frontend:Controllers:OpinionsController:extShow',
            'author_name' => 'author',
            '_format'     => 'html'
        ),
        array(
            'author_name'    => '[a-z0-9\-]+',
            'opinion_title'  => '[a-z0-9\-]+',
            'opinion_id'     => '[a-z0-9\-]+',
        )
    )
);

$frontendRoutes->add(
    'frontend_opinion_external_show',
    new Route(
        '/extopinion/{opinion_title}/{opinion_id}.{_format}',
        array(
           '_controller' => 'Frontend:Controllers:OpinionsController:extShow',
           '_format'     => 'html'
        ),
        array(
            'opinion_title' => '[a-z0-9\-]+',
            'opinion_id'    => '[a-z0-9\-]+',
       )
    )
);

$frontendRoutes->add(
    'frontend_opinion_frontpage',
    new Route(
        '/opinion',
        array(
           '_controller' => 'Frontend:Controllers:OpinionsController:frontpage',
        )
    )
);

$frontendRoutes->add(
    'frontend_opinion_external_frontpage',
    new Route(
        '/extseccion/opinion',
        array(
           '_controller' => 'Frontend:Controllers:OpinionsController:extFrontpage',
        )
    )
);

$frontendRoutes->add(
    'frontend_frontpage_home',
    new Route(
        '/home',
        array(
            '_controller' => 'Frontend:Controllers:FrontpagesController:show',
            'category'    => 'home'
        )
    )
);

$frontendRoutes->add(
    'frontend_frontpage',
    new Route(
        '/{category}',
        array(
            '_controller' => 'Frontend:Controllers:FrontpagesController:show',
            'category'    => 'home'
        ),
        array(
            'category'  => '(^[mobile])', // [a-z0-9\-]+
        )
    )
);

$frontendRoutes->add(
    'frontend_frontpage_category',
    new Route(
        '/seccion/{category}',
        array(
            '_controller' => 'Frontend:Controllers:FrontpagesController:show',
            'category'    => 'home'
        ),
        array(
            'category'          => '[a-z0-9\-]+',
        )
    )
);


$frontendRoutes->add(
    'frontend_frontpage_category_css',
    new Route(
        '/css/{cb}/{category}.css',
        array(
            '_controller' => 'Frontend:Controllers:FrontpagesController:css',
            'category'    => 'home',
            'cb'          => '12345'
        ),
        array(
            'category'          => '[a-z0-9\-]+',
        )
    )
);

$frontendRoutes->add(
    'frontend_externalfrontpage_category',
    new Route(
        '/extseccion/{category}',
        array(
            '_controller' => 'Frontend:Controllers:FrontpagesController:extShow',
            'category'    => 'home'
        ),
        array(
            'category'    => '[a-z0-9\-]+',
        )
    )
);

$frontendRoutes->add(
    'frontend_externalfrontpage_category_page',
    new Route(
        '/extseccion/{category}/{page}',
        array(
            '_controller' => 'Frontend:Controllers:FrontpagesController:extShow',
            'category'    => 'home'
        ),
        array(
            'category'    => '[a-z0-9\-]+',
            'page'        => '[0-9]+',
        )
    )
);

$routes->addCollection($frontendRoutes);
