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
        $data['title'] = $data['content']->title;
        // Get content summary, body or description. Otherwise use content title.
        $data['description'] = trim(preg_replace('/\s+/', ' ', (strip_tags(
            current(array_filter([
                $data['content']->seo_description,
                $data['content']->summary,
                $data['content']->description,
                mb_substr($data['content']->body, 0, 250),
                $data['content']->title
            ]))
        ))));

        // Count description data words
        $data['wordCount'] = str_word_count($data['description']);

        // Logo, author and media information
        $data['logo']   = $this->getLogoData();
        $data['author'] = $this->getAuthorData($data['content']);
        $media          = $this->getMediaData($data['content']);
        $data['image']  = $media['image'];
        $data['video']  = $media['video'];

        // Content keywords
        $data['keywords'] = empty($data['content']->tags) ? ''
            : $this->getTags($data['content']->tags);

        if (!empty($data['video'])) {
            $data['videokeywords'] = empty($data['video']->tags) ? ''
                : $this->getTags($data['video']->tags);
        }

        // Site information
        $data['sitename'] = $this->ds->get('site_name');
        $data['siteurl']  = SITE_URL;

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

        $output = $this->tpl->fetch('common/helpers/structured_article_data.tpl', $params);
        if ($params['content']->content_type_name == 'album') {
            $output = $this->tpl->fetch('common/helpers/structured_gallery_data.tpl', $params);
        } elseif (!empty($params['video'])) {
            $output = $this->tpl->fetch('common/helpers/structured_video_data.tpl', $params);
        }

        return $output;
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
            $user   = $this->container->get('api.service.author')->getItem($content->fk_author);
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
     * Method to retrieve the logo information.
     *
     * @return array Array with logo properties.
     */
    protected function getLogoData()
    {
        // Default logo information
        $logo = [
            'url'    => SITE_URL . '/assets/images/logos/opennemas-powered-horizontal.png',
            'width'  => '350',
            'height' => '60'
        ];

        $siteLogo = $this->ds->get('site_logo');
        if (!empty($siteLogo)) {
            $logo = [
                'url'    => SITE_URL
                    . '/asset/thumbnail%252C260%252C60%252Ccenter%252Ccenter'
                    . $this->container->get('core.instance')->getMediaShortPath()
                    . '/sections/' . $siteLogo,
                'width'  => '260',
                'height' => '60'
            ];
        }

        return $logo;
    }

    /**
     * Method to retrieve the media information.
     *
     * @param \Content $content The content object.
     *
     * @return array Array with media information.
     */
    protected function getMediaData($content)
    {
        $mediaObject = $this->container->get('core.helper.content_media')
            ->getMedia($content);

        $media = [
            'image' => $mediaObject->content_type_name === 'photo' ? $mediaObject : null,
            'video' => $mediaObject->content_type_name === 'video' ? $mediaObject : null,
        ];

        return $media;
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
