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
     * The global variables service.
     *
     * @var GlobalVariables
     */
    protected $globals;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $tpl;

    /**
     * Initializes the AdvertisementHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->tpl       = $this->container->get('view')->get('frontend');
        $this->globals   = $container->get('core.globals');
    }

    /**
     * Return meta tags
     *
     * @param String $action The action value.
     * @param Mixed  $content The Content object.
     * @param Mixed  $page the page number.
     * @param Mixed  $exception the exception if exists.
     *
     * @return Mixed The tpl meta content.
     */
    public function generateMetas($action, $content, $page, $exception)
    {
        $data = $this->generateData($content, $action, $page, $exception);
        $tpl  = $content instanceof \Common\Model\Entity\Content
            ? $this->contentTemplate
            : $this->template;

        return $this->tpl->fetch($tpl, $data);
    }

    /**
     * Return extracted data
     *
     * @param Mixed  $content The Content object.
     * @param String $action The action value.
     * @param Mixed  $page the page number.
     * @param Mixed  $exception the exception if exists.
     *
     * @return Mixed The extracted data.
     */
    protected function generateData($content, $action, $page, $exception)
    {
        $data = [];

        // On static page routes, $page is Content entity
        if ($page && !$page instanceof \Common\Model\Entity\Content) {
            $data['page'] = (int) $page > 1 ? $page : '';
        }

        $ch = $this->container->get('core.helper.category');
        $ah = $this->container->get('core.helper.author');

        $data = [
            'action'               => $action ?? '',
            'exception_code'       => !empty($exception) && $exception->getcode() ? $exception->getcode() : '',
            'category_name'        => $ch->getCategoryName($content) ?? '',
            'category_description' => $ch->getCategoryDescription($content) ?? $ch->getCategoryName($content) ?? '',
            'tag_name'             => $content->name ?? '',
            'author_name'          => $ah->getAuthorName($content) ?? '',
            'author_description'   => $ah->getAuthorBioSummary($content) ?? $ah->getAuthorBioBody($content) ??
                $ah->getAuthorName($content) ?? '',
        ];

        if ($content && $content instanceof \Common\Model\Entity\Content) {
            $data = array_merge($data, $this->getContentData($content));
        }

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

    /**
     * Return parsed metadata for a content
     *
     * @param Content  $content The Content object.
     *
     * @return Array The parsed metadata for a content.
     */
    protected function getContentData($content)
    {
        $settings        = $this->container->get('orm.manager')->getDataSet('Settings');
        $siteTitle       = $settings->get('site_title');
        $siteDescription = $settings->get('site_description');

        $title = htmlspecialchars(trim(strip_tags(
            $content->seo_title ?? $content->title_int ?? $content->title ?? $siteTitle
        )));

        $description = htmlspecialchars(trim(preg_replace('/\s+/', ' ', (strip_tags(
            current(array_filter([
                $content->seo_description,
                $content->summary,
                $content->description,
                mb_substr($content->body, 0, 160),
                $siteDescription
            ]))
        )))));

        $data = [
            'content_title'       => strlen($title) > 90 ? substr($title, 0, 87) . '...' : $title,
            'content_description' => strlen($description) > 160 ? substr($description, 0, 157) . '...' : $description,
            'content_starttime'   => $content->starttime,
        ];

        if (is_array($content->tags) && !empty($content->tags)) {
            $tags = $this->container->get('api.service.tag')->getListByIds($content->tags);

            $tagsName = array_map(function ($tag) {
                return strip_tags($tag->name);
            }, $tags['items']);

            $data['content_keywords'] = implode(',', $tagsName);
        }

        return $data;
    }
}
