<?php
/**
 * Handles the actions for sitemaps
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for sitemaps
 *
 * @package Frontend_Controllers
 **/
class SitemapController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('sitemap');

        $this->cm  = new \ContentManager();
        $this->ccm = \ContentCategoryManager::get_instance();

        list($this->availableCategories, $this->subcats, $this->other) =
            $this->ccm->getArraysMenu(0, 1);
    }

    /**
     * Renders the index sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function indexAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);
        $cacheID = $this->view->generateCacheId('sitemap', '', '');

        return $this->buildResponse($format, $cacheID, null);
    }

    /**
     * Renders the web sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function webAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);
        $cacheID = $this->view->generateCacheId('sitemap', '', 'web');

        if (($this->view->caching == 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            //TODO: add this value in a config file for easy editing
            $maxArticlesByCategory = 250;

            // Foreach available category retrieve last $maxArticlesByCategory articles in there
            $articlesByCategory = array();
            foreach ($this->availableCategories as $category) {
                if ($category->inmenu == 1
                    && $category->internal_category == 1
                ) {
                    $articlesByCategory[$category->name] = $this->cm->getArrayOfArticlesInCategory(
                        $category->pk_content_category,
                        'content_status=1 AND fk_content_type=1',
                        ' ORDER BY created DESC',
                        $maxArticlesByCategory
                    );
                    $articlesByCategory[$category->name] = $this->cm->getInTime(
                        $articlesByCategory[$category->name]
                    );

                    // Exclude articles with external link from RSS
                    foreach ($articlesByCategory[$category->name] as $key => $article) {
                        if (!is_null($article['params'])) {
                            $article['params'] = unserialize($article['params']);
                            if (isset($article['params']['bodyLink']) && !empty($article['params']['bodyLink'])) {
                                unset($articlesByCategory[$category->name][$key]);
                            }
                        }
                    }

                }
            }

            $opinions = $this->cm->getOpinionAuthorsPermalinks(
                'contents.content_status=1',
                'ORDER BY in_home DESC, position ASC, changed DESC LIMIT 100'
            );
            foreach ($opinions as &$opinion) {
                $opinion['author_name_slug'] = \Onm\StringUtils::get_title($opinion['name']);
            }

            $this->view->assign('articlesByCategory', $articlesByCategory);
            $this->view->assign('opinions', $opinions);
        }

        return $this->buildResponse($format, $cacheID, 'web');
    }

    /**
     * Renders the news sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function newsAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);
        $cacheID = $this->view->generateCacheId('sitemap', '', 'news');

        if (($this->view->caching == 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            $articlesByCategory = array();
            $maxArticlesByCategory = floor(900 / count($this->availableCategories));

            $er = getService('entity_repository');
            // Foreach available category and retrieve articles from 700 days ago
            foreach ($this->availableCategories as $category) {
                if ($category->inmenu == 1
                    && $category->internal_category == 1
                ) {
                    $articlesByCategory[$category->name] = $this->cm->getArrayOfArticlesInCategory(
                        $category->pk_content_category,
                        'content_status=1 AND fk_content_type=1 ',
                        'ORDER BY changed DESC',
                        $maxArticlesByCategory
                    );
                    $articlesByCategory[$category->name] =
                        $this->cm->getInTime($articlesByCategory[$category->name]);

                    foreach ($articlesByCategory[$category->name] as &$value) {
                        $aux = $er->find('Article', $value['pk_content']);
                        if (!empty($aux->img1)) {
                            $value['image_path'] = \Photo::getPhotoPath($aux->img1);
                        } elseif (!empty($aux->img2)) {
                            $value['image_path'] = \Photo::getPhotoPath($aux->img2);
                        }
                    }
                }
            }

            // Get latest opinions
            $opinions = $this->cm->getOpinionAuthorsPermalinks(
                'contents.content_status=1',
                'ORDER BY position ASC, changed DESC LIMIT 100'
            );

            foreach ($opinions as &$opinion) {
                $opinion['author_name_slug'] = \Onm\StringUtils::get_title($opinion['name']);
            }

            $this->view->assign('articlesByCategory', $articlesByCategory);
            $this->view->assign('opinions', $opinions);
        }

        return $this->buildResponse($format, $cacheID, 'news');
    }

    /**
     * Formats the response
     *
     * @param string format whether compress the sitemap or not
     * @param string $cacheID the identificator for this cache
     * @param string $action the type of sitemap: news or web
     *
     * @return Response the response object
     **/
    public function buildResponse($format, $cacheID, $action)
    {
        $this->view->assign('action', $action);
        $contents = $this->renderView('sitemap/sitemap.tpl', array('cache_id' => $cacheID));

        if ($format == 'xml.gz') {
            // disable ZLIB ouput compression
            ini_set('zlib.output_compression', 'Off');
            // compress data
            $contents = gzencode($contents);

            $headers = array(
                'Content-Type'     => 'application/x-download',
                'Content-Encoding' => 'gzip',
                'Content-Length' => strlen($contents)
            );
        } else {
            // Return the output as xml
            $headers = array('Content-Type' => 'application/xml charset=utf-8');
        }

        $headers['x-tags'] = 'sitemap';

        return new Response($contents, 200, $headers);
    }
}
