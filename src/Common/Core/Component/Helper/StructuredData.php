<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

/**
 * Generates json-ld code for different type of Objects
 * See more: https://schema.org/
 * Google ref: https://developers.google.com/search/docs/guides/intro-structured-data
 */
class StructuredData
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes StructuredData
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->instance  = $this->container->get('core.instance');
        $this->tpl       = $this->container->get('core.template.admin');
        $this->ts        = $this->container->get('api.service.tag');
        $this->ds        = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance');
    }

    /**
     * Extract parameters from data.
     *
     * @param array $data The array of data.
     *
     * @return array The array with processed data.
     */
    public function extractParamsFromData($data)
    {
        // Site information
        $data['logo']            = $this->getLogoData();
        $data['siteName']        = $this->ds->get('site_name');
        $data['siteUrl']         = $this->instance->getBaseUrl();
        $data['siteDescription'] = $this->ds->get('site_description');

        if (!array_key_exists('content', $data)) {
            return $data;
        }

        $data['title']       = $data['content']->title;
        $data['description'] = $this->getDescription($data['content']);

        // Count description data words
        $data['wordCount'] = str_word_count($data['description']);

        // Author and media information
        $data['author'] = $this->getAuthorData($data['content']);

        // Content keywords
        $data['keywords'] = empty($data['content']->tags) ? ''
            : $this->getTags($data['content']->tags);

        if (!empty($data['video'])) {
            $data['videoKeywords'] = empty($data['video']->tags) ? ''
                : $this->getTags($data['video']->tags);
        }

        return $data;
    }

    /**
     * Generate specific JSON code based on the type of content: album, video or article.
     *
     * @param array $data The array of data.
     *
     * @return string $output The JSON code specific for the data.
     */
    public function generateJsonLDCode($data)
    {
        $params = $this->extractParamsFromData($data);

        $template = 'common/helpers/structured_frontpage_data.tpl';
        if (array_key_exists('content', $params)) {
            $template = 'common/helpers/structured_content_data.tpl';
            if (in_array($params['content']->content_type_name, ['album', 'video'])) {
                $template = 'common/helpers/structured_'
                    . $params['content']->content_type_name . '_data.tpl';
            }

            if (array_key_exists('live_blog_posting', $params['content']->params) &&
                $params['content']->params['live_blog_posting'] == '1') {
                $template = 'common/helpers/structured_live_blog_data.tpl';
            }
        }

        return $this->tpl->fetch($template, $params);
    }

    /**
     * Method to retrieve the author information.
     *
     * @param \Content $content The content object.
     *
     * @return string The author information.
     */
    protected function getAuthorData($content)
    {
        // Get author if exists or agency. Otherwise get site name.
        $author = '';
        try {
            $user   = $this->container->get('core.helper.author')->getAuthor($content->fk_author);
            $author = $user->name;
        } catch (\Exception $e) {
            $author = $content->agency;
        }

        if (empty($author)) {
            $author = $this->ds->get('site_name');
        }

        return $author;
    }

    /**
     * Method to retrieve the description for a content.
     *
     * @param \Content $content The content object.
     *
     * @return string The description.
     */
    protected function getDescription($content)
    {
        // Get content summary, description or body.
        $description = trim(preg_replace('/\s+/', ' ', (strip_tags(
            current(array_filter([
                $content->seo_description,
                $content->summary,
                $content->description,
                mb_substr($content->body, 0, 250)
            ]))
        ))));

        return !empty($description)
            ? $description
            : $this->ds->get('site_description');
    }

    /**
     * Method to retrieve the logo information.
     *
     * @return array Array with logo properties.
     */
    protected function getLogoData()
    {
        $sh = $this->container->get('core.helper.setting');

        // Default logo information
        $logo = $this->instance->getBaseUrl()
            . '/assets/images/logos/opennemas-powered-horizontal.png';

        // Get instance logo size
        if ($sh->hasLogo('default')) {
            $logo = $this->container->get('core.helper.photo')->getPhotoPath(
                $sh->getLogo('default'),
                null,
                [],
                true
            );
        }

        return $logo;
    }

    /**
     * Method to retrieve the tags for a list of tag ids.
     *
     * @param array $ids List of ids we want to retrieve.
     *
     * @return string    List of tags fo this tags.
     */
    protected function getTags($ids)
    {
        $tags = $this->ts->getListByIds($ids);

        $names = array_map(function ($a) {
            return $a->name;
        }, $tags['items']);

        return implode(',', $names);
    }
}
