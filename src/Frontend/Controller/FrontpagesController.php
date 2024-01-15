<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Displays frontpages.
 */
class FrontpagesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'show'    => 'frontpage'
    ];

    /**
     * Shows the frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @throws ResourceNotFoundException If the frontpage doesn't exist.
     */
    public function showAction(Request $request)
    {
        $categoryName = $request->query->get('category', 'home');
        $category     = null;
        $categoryId   = 0;

        if (!empty($categoryName) && $categoryName !== 'home') {
            try {
                $category = $this->get('api.service.category')
                    ->getItemBySlug($categoryName);
            } catch (\Exception $e) {
                throw new ResourceNotFoundException();
            }

            if (!$category->enabled) {
                throw new ResourceNotFoundException();
            }

            $categoryId   = $category->id;
            $categoryName = $category->name;
        }

        list($contentPositions, $contents, $invalidationDt, $lastSaved) =
            $this->get('api.service.frontpage')->getCurrentVersionForCategory($categoryId, false);

        $xtags = implode(',', array_map(function ($content) {
            return $content->content_type_name . '-' . $content->pk_content;
        }, $contents));

        $contents = $this->get('api.service.frontpage_version')->filterPublishedContents($contents);

        // Setup templating cache layer
        $this->view->setConfig('frontpages');

        $systemDate = new \DateTime();
        $lifetime   = $invalidationDt->getTimestamp() - $systemDate->getTimestamp();

        if (!empty($invalidationDt)) {
            if ($lifetime < $this->view->getCacheLifetime()) {
                $this->view->setCacheLifetime($lifetime);
            }
        }

        $cacheId = $categoryName == 'home'
            ? $this->view->getCacheId('frontpage', 'category', $categoryName, $lastSaved)
            : $this->view->getCacheId('frontpage', 'category', $categoryId, $lastSaved);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('frontpage/frontpage.tpl', $cacheId)
        ) {
            // Overloading information for contents
            $tagsIds = [];
            foreach ($contents as &$content) {
                $tagsIds = array_merge($content->tags, $tagsIds);
            }

            $layout = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('frontpage_layout_' . $categoryId, 'default');
            if (empty($layout)) {
                $layout = 'default';
            }

            $layoutFile = 'layouts/' . $layout . '.tpl';

            $this->view->assign('column', $contents);
            $this->view->assign('layoutFile', $layoutFile);
            $this->view->assign('contentPositionByPos', $contentPositions);
            $this->view->assign(
                'tags',
                $this->get('api.service.tag')
                    ->getListByIdsKeyMapped(array_unique($tagsIds))['items']
            );
        }

        $this->getAdvertisements($category);

        $invalidationDt->setTimeZone($this->get('core.locale')->getTimeZone());
        $themeVariables = $this->get('core.helper.theme_settings')->getThemeVariables(
            $this->get('core.globals')->getExtension(),
            $this->get('core.globals')->getAction()
        );
        return $this->render('frontpage/frontpage.tpl', array_merge([
            'cache_id'    => $cacheId,
            'category'    => $category,
            'o_category'  => $category,
            'time'        => $systemDate->getTimestamp(),
            'x-cache-for' => $invalidationDt->format('Y-m-d H:i:s'),
            'x-cacheable' => true,
            'x-tags'      => $xtags . ',frontpage-page-' . $categoryId
        ], $themeVariables));
    }
}
