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
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Handles the actions for assets.
 */
class AssetController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'image' => 'asset_image'
    ];

    /**
     * Displays an image after applying a transformation.
     *
     * @return Response The response object.
     */
    public function imageAction(Request $request, $params, $path)
    {
        $action = $this->get('core.globals')->getAction();
        $params = $this->container->get('data.manager.filter')
            ->set($params)
            ->filter('url_decode')
            ->get();

        $expectedUri = $this->getExpectedUri($action, [
            'params' => $params,
            'path'   => $path
        ]);

        if ($request->getRequestUri() !== $expectedUri) {
            return new RedirectResponse($expectedUri, 301);
        }

        $path      = $this->getParameter('core.paths.public') . '/' . $path;
        $params    = explode(',', $params);
        $transform = array_shift($params);

        try {
            $content = $this->get('core.image.processor')
                ->open($path)
                ->strip()
                ->apply($transform, $params)
                ->getContent();
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }

        $mimeType = $this->get('core.image.processor')->getMimeType();

        return new Response($content, 200, [ 'Content-Type' => $mimeType ]);
    }

    /**
     * Generates custom css for frontpages elements
     *
     * @param Request $request The request object
     *
     * @return Response the response object
     */
    public function customCssFrontpageAction(Request $request)
    {
        $categoryName      = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $currentCategoryId = 0;

        try {
            $category = $this->get('api.service.category')
                ->getItemBySlug($categoryName);

            $currentCategoryId = $category->pk_content_category;
        } catch (\Exception $e) {
        }

        list(, , $contentsInHomepage) = $this->get('api.service.frontpage_version')
            ->getContentsInCurrentVersionforCategory($currentCategoryId);

        if (is_array($contentsInHomepage)) {
            $bgColor    = 'bgcolor_' . $currentCategoryId;
            $titleColor = "title_" . $currentCategoryId;

            $properties = [];
            foreach ($contentsInHomepage as &$content) {
                $properties[] = [$content->id, $bgColor];
                $properties[] = [$content->id, $titleColor];
            }

            $properties = \ContentManager::getMultipleProperties($properties);

            foreach ($contentsInHomepage as &$content) {
                foreach ($properties as $property) {
                    if ($property['fk_content'] != $content->id) {
                        continue;
                    }

                    if ($property['meta_name'] == $bgColor) {
                        $content->bgcolor = $property['meta_value'];
                    }

                    if ($property['meta_name'] == $titleColor) {
                        $content->title_props = $property['meta_value'];
                        if (!empty($content->title_props)) {
                            $content->title_props = json_decode($content->title_props);
                        }
                    }
                }
            }
        }

        $response = '';

        // render
        if (!empty($contentsInHomepage)) {
            $response .= "/**********************************************************\n"
                      . "   CSS for contents in frontpage of category $categoryName\n"
                      . " **********************************************************/\n";

            foreach ($contentsInHomepage as $item) {
                // Background color
                if (!empty($item->bgcolor)) {
                    $response .= "#content-{$item->pk_content}.onm-new { "
                            . "background-color:{$item->bgcolor} !important; }\n";

                    $response .= "#content-{$item->pk_content}.colorize { "
                            . "padding:10px !important; border-radius:5px; }\n";
                }

                if (!empty($item->title_props)) {
                    $response .= "#content-{$item->pk_content} .custom-text, "
                                . "#content-{$item->pk_content} .title a, "
                                . "#content-{$item->pk_content} .nw-title a { ";

                    foreach ($item->title_props as $property => $value) {
                        if (!empty($value)) {
                            $response .= "{$property}:{$value}!important;";
                        }
                    }

                    $response .= "}\n";
                }
            }
        }

        return new Response($response, 200, [
            'Content-Type' => 'text/css',
            'x-tags'       => 'frontpagecss',
            'x-cacheable'  => true,
        ]);
    }

    /**
     * Retrieves the styleSheet rules for the frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function globalCssAction()
    {
        // Setup templating cache layer
        $this->view->setConfig('frontpages');
        $cacheID = $this->view->getCacheId('css', 'global');

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('base/custom_css.tpl', $cacheID)
        ) {
            // RenderColorMenu
            $siteColor   = '#005689';
            $configColor = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('site_color');

            if (!empty($configColor)) {
                if (!preg_match('@^#@', $configColor)) {
                    $configColor = '#' . $configColor;
                }

                $siteColor = $configColor;
            }

            $categories = $this->get('api.service.category')
                ->getList('color !is null and color != ""');

            $this->view->assign([
                'categories' => $categories['items'],
                'site_color' => $siteColor,
            ]);
        }

        $coreCss   = $this->get('core.template.admin')->render('css/global.tpl');
        $customCss = $this->get('core.template.frontend')->render(
            'base/custom_css.tpl',
            [ 'cache_id' => $cacheID ]
        );

        $contents = $coreCss . PHP_EOL . $customCss;

        return new Response($contents, 200, [
            'Content-Type' => 'text/css',
            'x-tags'       => 'customcss',
            'x-cacheable'  => true,
        ]);
    }

    /**
     * Redirect requests to apple-touch-icon.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function favicoAction()
    {
        $path = '/assets/images/favicon.png';

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get(['favico', 'logo_enabled']);

        if ($settings['logo_enabled'] && !empty($settings['favico'])) {
            $path = $this->get('core.instance')->getMediaShortPath()
                . '/sections/' . $settings['favico'];
        }

        $path = $this->getParameter('core.paths.public') . $path;

        $content = $this->get('core.image.processor')
            ->open($path)
            ->getContent();

        $mimeType = $this->get('core.image.processor')->getMimeType();

        return new Response($content, 200, [ 'Content-Type' => $mimeType ]);
    }

    /**
     * Returns the expected URI for the provided action basing on a list of
     * parameters.
     *
     * @param string $action The action name.
     * @param array  $params The list of parameters to generate the route with.
     *
     * @return string The expected URI.
     */
    protected function getExpectedUri($action, $params)
    {
        return $this->get('router')->generate($this->getRoute($action), $params);
    }
}
