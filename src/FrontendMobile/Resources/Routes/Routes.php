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

$mobileRoutes = new RouteCollection();

$mobileRoutes->add(
    'frontendmobile_redirect_web',
    new Route(
        '/redirect_web',
        array(
            '_controller' => 'FrontendMobile:Controllers:FrontpagesController:redirectCompleteWeb',
        )
    )
);

$mobileRoutes->add(
    'frontendmobile_opinion_frontpage',
    new Route(
        '/opinion',
        array(
            '_controller' => 'FrontendMobile:Controllers:OpinionsController:frontpage',
        )
    )
);

$mobileRoutes->add(
    'frontendmobile_opinion_frontpage2',
    new Route(
        '/seccion/opinion',
        array(
            '_controller' => 'FrontendMobile:Controllers:OpinionsController:frontpage',
        )
    )
);

$mobileRoutes->add(
    'frontendmobile_frontpages_category',
    new Route(
        '/seccion/{category}',
        array(
            '_controller' => 'FrontendMobile:Controllers:FrontpagesController:show',
            'category'    => null,
        ),
        array(
            'category'    => '[a-z0-9\-\._]+'
        )
    )
);

$mobileRoutes->add(
    'frontendmobile_frontpages_category',
    new Route(
        '/seccion/{category}/{subcategory}',
        array(
            '_controller' => 'FrontendMobile:Controllers:FrontpagesController:show',
            'category'    => null,
            'subcategory' => null,
        ),
        array(
            'category'    => '[a-z0-9\-\._]+',
            'subcategory' => '[a-z0-9\-\._]+'
        )
    )
);

$mobileRoutes->add(
    'frontendmobile_latestnews',
    new Route(
        '/ultimas-noticias',
        array(
            '_controller' => 'FrontendMobile:Controllers:FrontpagesController:latestNews',
        )
    )
);

$mobileRoutes->add(
    'frontendmobile_latestnews2',
    new Route(
        '/ultimas',
        array(
            '_controller' => 'FrontendMobile:Controllers:FrontpagesController:latestNews',
        )
    )
);

$mobileRoutes->add(
    'frontendmobile_opinion_show',
    new Route(
        '/opinion/{author_name}/{opinion_title}/{opinion_id}.{_format}',
        array(
            '_controller' => 'FrontendMobile:Controllers:OpinionsController:show',
            '_format'     => 'html'
        ),
        array(
            'author_name'    => '[a-zA-Z0-9\-]+',
            'opinion_title'  => '[a-z0-9\-]+',
            'opinion_id'     => '[a-z0-9\-]+',
        )
    )
);

$mobileRoutes->add(
    'frontendmobile_article_show',
    new Route(
        '/{component}/{category_name}/{slug}/{article_id}.{_format}',
        array(
            '_controller' => 'FrontendMobile:Controllers:ArticlesController:show',
            'component'   => 'articulo',
            '_format'     => 'html',
            'page'        => 1
        ),
        array(
            'component'     => 'articulo|artigo|article',
            'category_name' => '[a-z0-9\-]+',
            'slug'          => '[a-z0-9\-]+',
            'article_id'    => '([0-9]+)',
            '_format'       => 'html|htm'
        )
    )
);

$mobileRoutes->addPrefix('/mobile');

$routes->add(
    'frontendmobile_root',
    new Route(
        '/mobile',
        array('_controller' => 'FrontendMobile:Controllers:FrontpagesController:show')
    )
);

$routes->addCollection($mobileRoutes);
