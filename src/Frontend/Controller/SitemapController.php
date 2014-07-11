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
     * Renders the index sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function indexAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->generateCacheId('sitemap', '', '');

        return $this->buildResponse($format, $cacheID, null);
    }

    /**
     * Renders the sitemap web
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function webAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->generateCacheId('sitemap', '', 'web');

        if (($this->view->caching == 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            // Fetch contents
            $contents = $this->fetchContents(array());

            // Remove external articles
            foreach ($contents as $key => &$content) {
                if (!empty($content->params['bodyLink'])) {
                    unset($contents[$key]);
                }
            }

            $this->view->assign(array('contents' => $contents));
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

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->generateCacheId('sitemap', '', 'news');

        if (($this->view->caching == 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            // Fetch contents
            $contents = $this->fetchContents(array());

            // Fetch images and videos from contents
            $er = getService('entity_repository');
            foreach ($contents as $key => &$content) {
                if (!empty($content->params['bodyLink'])) {
                    // Remove external articles
                    unset($contents[$key]);
                } else {
                    // Get content image
                    if (!empty($content->img1)) {
                        $content->image = $er->find('Photo', $content->img2);
                    } elseif (!empty($content->img2)) {
                        $content->image = $er->find('Photo', $content->img2);
                    }
                    // Get content video
                    if (!empty($content->fk_video)) {
                        $content->video = $er->find('Video', $content->fk_video);
                    } elseif (!empty($content->fk_video2)) {
                        $content->video = $er->find('Video', $content->fk_video2);
                    }
                }
            }

            $this->view->assign(
                array(
                    'contents'   => $contents,
                    'googleNews' => s::get('google_news_name'),
                )
            );
        }

        return $this->buildResponse($format, $cacheID, 'news');
    }

    /**
     * Renders the image sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function imageAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->generateCacheId('sitemap', '', 'image');

        if (($this->view->caching == 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            // Fetch contents
            $contents = $this->fetchContents(
                array('content_type_name' => array(array('value' => 'photo')))
            );

            // Get contents referenced for this image
            foreach ($contents as $key => &$content) {
                $content->referenced = $this->getContentsWithReferencedImage($content->id);
                if (!$content->referenced) {
                    // Remove images without content referenced
                    unset($contents[$key]);
                } elseif (!empty($content->params['bodyLink'])) {
                    // Remove external articles
                    unset($contents[$key]);
                }
            }

            $this->view->assign(array('contents' => $contents));
        }

        return $this->buildResponse($format, $cacheID, 'image');
    }

    /**
     * Renders the video sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function videoAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->generateCacheId('sitemap', '', 'video');

        if (($this->view->caching == 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            // Fetch contents
            $contents = $this->fetchContents(
                array('content_type_name' => array(array('value' => 'video')))
            );

            $this->view->assign(array('contents' => $contents));
        }

        return $this->buildResponse($format, $cacheID, 'video');
    }

    /**
     * Formats the response
     *
     * @param string format whether compress the sitemap or not
     * @param string $cacheID the identifier for this cache
     * @param string $action the type of sitemap: news, web, image or video
     *
     * @return Response the response object
     **/
    public function buildResponse($format, $cacheID, $action)
    {
        $this->view->assign('action', $action);
        $contents = $this->renderView('sitemap/sitemap.tpl', array('cache_id' => $cacheID));

        if ($format == 'xml.gz') {
            // disable ZLIB output compression
            ini_set('zlib.output_compression', 'Off');
            // compress data
            $contents = gzencode($contents);

            $headers = array(
                'Content-Type'     => 'application/x-download',
                'Content-Encoding' => 'gzip',
                'Content-Length'   => strlen($contents)
            );
        } else {
            // Return the output as xml
            $headers = array('Content-Type' => 'application/xml charset=utf-8');
        }

        $headers['x-tags'] = 'sitemap';

        return new Response($contents, 200, $headers);
    }

    /**
     * Fetch articles and opinions contents
     *
     * @param int Max number of contents
     *
     * @return Array all contents
     **/
    public function fetchContents($criteria = array(), $limit = 100)
    {
        // Set search filters
        $filters = array(
            'content_type_name' => array(
                'union' => 'OR',
                array('value' => 'article'),
                array('value' => 'opinion')
            ),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '<>'))
        );

        if (!empty($criteria)) {
            $filters = array_merge($filters, $criteria);
        }

        // Fetch contents
        $er = getService('entity_repository');
        $contents = $er->findBy($filters, array('created' => 'desc'), $limit, 1);

        return $contents;
    }

    /**
    * Fetches articles or albums associated with the image
    *
    * @param $imageId The image id
    *
    * @return Array | Contents associated with this image
    */
    public function getContentsWithReferencedImage($imageId)
    {
        $cache = $this->get('cache');

        $content = $cache->fetch(CACHE_PREFIX."_image_".$imageId);

        if (!$content) {
            $sql = "SELECT `pk_article` as id FROM `articles`
                    WHERE `img1` = ? OR `img2` = ?";

            $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
            $rs = $GLOBALS['application']->conn->Execute($sql, array($imageId, $imageId));

            $er = getService('entity_repository');
            if (!$rs || $rs->fields === false) {
                $sql = "SELECT `pk_album` as id FROM `albums_photos`
                        WHERE `pk_photo` = ?";

                $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                $rs = $GLOBALS['application']->conn->Execute($sql, array($imageId));

                if (!$rs || $rs->fields === false) {
                    return false;
                } else {
                    $content = $er->find('Album', $rs->fields['id']);
                }
            } else {
                $content = $er->find('Article', $rs->fields['id']);
            }
            // Save the content in cache
            $cache->save(CACHE_PREFIX."_image_".$imageId, $content, 300);
        }

        return $content;
    }
}
