<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace FrontendMobile\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Handles the actions for the mobile opinion section
 *
 * @package FrontendMobile_Controllers
 **/
class OpinionsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        define('BASE_PATH', '/mobile');
    }

    /**
     * Renders the opinion frontpage for mobile devices
     *
     * @return Response the response object
     **/
    public function frontpageAction()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('frontpage-mobile');

        $cacheID = $this->view->generateCacheId('opinion-mobile', '', 0);
        if (($this->view->caching == 0)
            || !$this->view->isCached('mobile/opinion-index.tpl', $cacheID)
        ) {
            $this->view->assign('menuMobile', $this->getMobileMenu());

            $cm  = new \ContentManager();

            //Fetch opinions
            $director  = $cm->find(
                'Opinion',
                'type_opinion=2 AND in_home=1 AND content_status=1',
                'ORDER BY created DESC  LIMIT 0,1'
            );
            $editorial = $cm->find(
                'Opinion',
                'type_opinion=1 AND in_home=1 AND content_status=1',
                'ORDER BY position ASC, created DESC LIMIT 0,2'
            );
            $opinions = $cm->getOpinionArticlesWithAuthorInfo(
                'type_opinion=0 AND content_status=1',
                'ORDER BY in_home DESC, position ASC, created DESC LIMIT 0,10'
            );

            if (isset ($director[0])) {
                $director[0]->name = 'Director';
                $this->view->assign('director', $director[0]);
            }

            foreach ($opinions as &$opinion) {
                $opinion['author_name_slug'] = \Onm\StringUtils::getTitle($opinion['name']);
            }

            $this->view->assign([
                'editorial' => $editorial,
                'opinions'  => $opinions,
            ]);
        }
        return $this->render(
            'mobile/opinion-index.tpl',
            [
                'cache_id'           => $cacheID,
                'category_name'      => 'opinion',
                'category_real_name' => 'Opinion',
                'section'            => 'opinion'
            ]
        );
    }

    /**
     * Displays the mobile version of an opinion
     *
     * @return Respone the response object
     **/
    public function showAction(Request $request)
    {
        $dirtyID = $request->query->getDigits('opinion_id');

        // Resolve opinion ID, search in repository or redirect to 404
        $opinionID = \ContentManager::resolveID($dirtyID);
        $er        = getService('entity_repository');
        $opinion   = $er->find('Opinion', $opinionID);
        if (is_null($opinion)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        // Setup view
        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('frontpage-mobile');

        $cacheID = $this->view->generateCacheId('opinion-mobile', '', $opinionID);
        if (($this->view->caching == 0)
            || !$this->view->isCached('mobile/opinion-inner.tpl', $cacheID)
        ) {
            // Get author photo
            $photo = $er->find('Photo', $opinion->fk_author_img);

            $this->view->assign(
                array(
                    'opinion'            => $opinion,
                    'author_name'        => $opinion->name,
                    'condition'          => $opinion->bio,
                    'menuMobile'         => $this->getMobileMenu(),
                    'photo'              => $photo
                )
            );
        }

        return $this->render(
            'mobile/opinion-inner.tpl',
            [
                'cache_id'           => $cacheID,
                'section'            => 'opinion',
                'category_name'      => 'opinion',
                'category_real_name' => 'Opinion'
            ]
        );
    }

    /**
     * Get mobile menu
     *
     * @return Response the response object
     **/
    public function getMobileMenu()
    {
        $cache = getService('cache');

        $menuMobile = $cache->fetch(CACHE_PREFIX.'_mobileMenu');

        if (empty($menuMobile)) {
            $menu = new \Menu();
            $menuMobile = $menu->getMenu('mobile');

            if (!empty($menuMobile->items)) {
                $cache->save(CACHE_PREFIX.'_mobileMenu', $menuMobile, 300);
            }
        }
        return $menuMobile->items;
    }
}
