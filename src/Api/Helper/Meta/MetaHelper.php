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

class MetaHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Default meta template
     *
     * @var String
     */
    protected $template = 'metas/default/default.tpl';

    /**
     * Content meta templates
     *
     * @var Array
     */
    protected $contentTemplate = [
        'default' => 'metas/content/content.tpl',
        'event'   => 'metas/content/event/event_content.tpl',
        'company' => 'metas/content/company/company_content.tpl'
    ];

    /**
     * The global variables service.
     *
     * @var GlobalVariables
     */
    protected $globals;

    /**
     * The instance settings array.
     *
     * @var Settings
     */
    protected $settings;

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
        $this->tpl       = $container->get('view')->get('frontend');
        $this->settings  = $container->get('orm.manager')->getDataSet('Settings');
        $this->globals   = $container->get('core.globals');
    }

    /**
     * Return meta tags
     *
     * @param String $action    The action value.
     * @param Mixed  $content   The Content object.
     * @param Mixed  $page      The page number.
     * @param Mixed  $exception The exception if exists.
     *
     * @return Mixed The tpl meta content.
     */
    public function generateMetas($action, $content, $page, $exception)
    {
        $data = $this->generateData($content, $action, $page, $exception);
        $tpl  = $this->template;

        if ($content instanceof \Common\Model\Entity\Content) {
            $tpl = array_key_exists($content->content_type_name, $this->contentTemplate) ?
                $this->contentTemplate[$content->content_type_name] :
                $this->contentTemplate['default'];
        }

        return $this->tpl->fetch($tpl, [
            'data'            => $data,
            'siteName'        => $this->settings->get('site_name'),
            'siteTitle'       => $this->settings->get('site_title'),
            'siteDescription' => $this->settings->get('site_description'),
            'siteKeywords'    => $this->settings->get('site_keywords'),
        ]);
    }

    /**
     * Return extracted data
     *
     * @param Content $content   The Content object.
     * @param String  $action    The action value.
     * @param Mixed   $page      The page number.
     * @param Mixed   $exception The exception if exists.
     *
     * @return Array  $data      The extracted data.
     */
    protected function generateData($content, $action, $page, $exception)
    {
        $cah = $this->container->get('core.helper.category');
        $ah  = $this->container->get('core.helper.author');

        $category = $cah->getCategory($content);

        $data = [
            'action'               => $action,
            'exception_code'       => !empty($exception) && $exception->getcode() ? $exception->getcode() : '',
            'category_name'        => $category->seo_title ?? $category->title,
            'category_description' => $category->description,
            'tag_name'             => $content->seo_title ?? $content->name ?? '',
            'tag_description'      => $content->description ?? '',
            'author_name'          => $ah->getAuthorName($content),
            'author_description'   => $ah->getAuthorBioSummary($content) ?? $ah->getAuthorBioBody($content),
        ];

        // On static page routes, $page is Content entity
        if ($page && !$page instanceof \Common\Model\Entity\Content) {
            $data['page'] = (int) $page > 1 ? $page : '';
        }

        if ($content && $content instanceof \Common\Model\Entity\Content) {
            $data = array_merge($data, $this->getContentData($content));
        }

        return $data;
    }

    /**
     * Return parsed metadata for a content
     *
     * @param Content  $content The Content object.
     *
     * @return Array   $data    The parsed metadata for a content.
     */
    protected function getContentData($content)
    {
        $title = htmlspecialchars(trim(strip_tags(
            $content->seo_title ??
            $content->title_int ??
            $content->title ??
            $this->settings->get('site_title')
        )));

        $description = htmlspecialchars(trim(preg_replace('/\s+/', ' ', (strip_tags(
            current(array_filter([
                $content->seo_description,
                $content->summary,
                $content->description,
                mb_substr($content->body, 0, 160),
                $this->settings->get('site_description')
            ]))
        )))));

        if (is_array($content->tags) && !empty($content->tags)) {
            $tags = $this->container->get('api.service.tag')->getListByIds($content->tags);

            $tagsName = array_map(function ($tag) {
                return strip_tags($tag->name);
            }, $tags['items']);

            $keywords = implode(',', $tagsName);
        }

        $data = [
            'content_title'       => $title,
            'content_description' => strlen($description) > 160 ? substr($description, 0, 157) . '...' : $description,
            'content_starttime'   => $content->starttime,
            'content_modified'    => $content->changed,
            'content_keywords'    => $keywords ?? $this->settings->get('site_keywords')
        ];

        return $data;
    }
}
