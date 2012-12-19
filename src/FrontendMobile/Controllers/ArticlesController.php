<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace FrontendMobile\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the mobile articles section
 *
 * @package FrontendMobile_Controllers
 **/
class ArticlesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Displays the mobile version of an opinion
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->view->setConfig('articles-mobile');

        $dirtyID = $request->query->getDigits('article_id');

        $articleID = \Content::resolveID($dirtyID);

        $cacheID = $this->view->generateCacheId('articles-mobile', '', $articleID);
        if ($this->view->caching == 0
            || ! $this->view->isCached('mobile/article-inner.tpl', $cacheID)
        ) {
            // Category manager to retrieve category of article
            $ccm = \ContentCategoryManager::get_instance();
            $cm = new \ContentManager();

            // require('sections.php');

            $article = new \Article($articleID);
            $article->category_name = $ccm->get_name($article->category);


            /******* RELATED  CONTENT *******/
            $rel= new \RelatedContent();

            $relationes = $rel->cache->getRelationsForInner($articleID);
            $relat = $cm->cache->getContents($relationes);
            $relat = $cm->getInTime($relat);
            //Filter availables and not inlitter.
            $relat = $cm->cache->getAvailable($relat);

            //Nombre categoria correcto.
            foreach ($relat as $ril) {
                $ril->category_name = $ccm->get_title($ril->category_name);
            }

            $this->view->assign('related_articles', $relat);
            $this->view->assign('article', $article);
            $this->view->assign('section', $article->category_name);

            // Photo interior
            if (isset($article->img2) and ($article->img2 != 0)) {
                $photo = new \Photo($article->img2);
                $this->view->assign('photo', $photo->path_file . '140-100-' . $photo->name);
            }

        }

        //TODO: define cache system
        return $this->render(
            'mobile/mobile-article-inner.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }
}
