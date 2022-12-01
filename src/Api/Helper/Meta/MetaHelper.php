<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Helper\Meta;

use Api\Exception\GetListException;

class MetaHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Content tpl
     *
     * @var String
     */
    protected $template        = 'metas/content/content.tpl';
    protected $contentTemplate = 'metas/content/content.tpl';


    /**
     * Initializes the AdvertisementHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container       = $container;
        $this->tpl             = $this->container->get('view')->get('frontend');
        $this->globals         = $container->get('core.globals');
        $this->settings        = $container->get('orm.manager')->getDataSet('Settings');
        $this->categoryHelper  = $container->get('core.helper.category');
        $this->authorHelper    = $container->get('core.helper.author');
        $this->siteTitle       = $this->settings->get('site_title');
        $this->siteKeywords    = $this->settings->get('site_keywords');
        $this->siteDescription = $this->settings->get('site_description');
    }

    /**
     * Return meta tags
     *
     * @param String $action The action value.
     * @param Mixed  $content The Content object.
     * @param Mixed  $page the page number.
     *
     * @return Mixed The tpl meta content.
     */
    public function generateMetas($action, $content, $page)
    {
        $data   = $this->generateData($content, $action, $page);
        $result = $this->render($content, $data);
        return $result;
    }

    /**
     * Return meta tpl content
     *
     * @param Mixed  $content The Content object.
     * @param Mixed  $params The params object.
     *
     * @return Mixed The tpl meta content.
     */
    public function render($content, $params)
    {
        $tpl = $content instanceof \Common\Model\Entity\Content ? $this->contentTemplate : $this->template;

        return $this->tpl->fetch($tpl, $params);
    }

    /**
     * Return extracted data
     *
     * @param Mixed  $content The Content object.
     * @param String $action The action value.
     * @param Mixed  $page the page number.
     *
     * @return Mixed The extracted data.
     */
    public function generateData($content, $action, $page)
    {
        $data = [];

        $data['action']               = $action ?? '';
        $data['page']                 = is_int($page) && $page > 1 ? $page : '';
        $data['category_name']        = $this->categoryHelper->getCategoryName($content) ?? '';
        $data['category_description'] = $this->categoryHelper->getCategoryDescription($content) ??
            $this->categoryHelper->getCategoryName($content) ??
            '';
        $data['tag_name']             = $content->name ?? '';
        $data['author_name']          = $this->authorHelper->getAuthorName($content) ?? '';
        $data['author_description']   = $this->authorHelper->getAuthorBioSummary($content) ??
            $this->authorHelper->getAuthorBioBody($content) ??
            $this->authorHelper->getAuthorName($content) ??
            '';

        $data = array_filter($data, function ($element) {
            return !empty($element);
        });

        $data = array_map(function ($element) {
            if (!is_string($element)) {
                return $element;
            }
            return strip_tags(trim($element));
        }, $data);

        return [
            'data' => $data
        ];
    }
}
