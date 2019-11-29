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

use Common\ORM\Core\EntityManager;
use Common\ORM\Entity\Instance;
use Api\Service\V1\TagService;

/**
 * Generates json-ld code for different type of Objects
 * See more: https://schema.org/
 * Google ref: https://developers.google.com/search/docs/guides/intro-structured-data
 */
class StructuredData
{
    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The dataset service.
     *
     * @var DataSet
     */
    protected $ds;

    /**
     * The tag service.
     *
     * @var TagService
     */
    protected $ts;

    /**
     * The template service
     *
     * @var Template
     */
    protected $tpl;

    /**
     * Initializes StructuredData
     *
     * @param Instance      $instance The current instance.
     * @param EntityManager $em       The entity manager.
     * @param TagService    $ts       The tag service.
     * @param Template      $tpl       The template service.
     */
    public function __construct(Instance $instance, EntityManager $em, TagService $ts, $tpl)
    {
        $this->instance = $instance;
        $this->ds       = $em->getDataSet('Settings', 'instance');
        $this->ts       = $ts;
        $this->tpl      = $tpl;
    }


    /**
     * Transform tags from array of ids to String and add some data to the array
     *
     * @param array $data The array of data
     *
     * @return array The array with Strings of tags and more data
     */
    public function extractParamsFromData($data)
    {
        $data['keywords'] = empty($data['content']->tags) ? '' : $this->getTags($data['content']->tags);

        if (!empty($data['video'])) {
            $data['videokeywords'] = empty($data['video']->tags) ? '' : $this->getTags($data['video']->tags);
        }

        $data['wordCount'] = str_word_count($data['content']->body);
        $data['sitename']  = $this->ds->get("site_name");
        $data['siteurl']   = SITE_URL;

        return $data;
    }

    /**
     * Generate specific JSON code based on the type of content: album, video or article.
     *
     * @param array $data The array of data
     *
     * @return string $output The JSON code specific for the data
     */
    public function generateJsonLDCode($data)
    {
        $params = $this->extractParamsFromData($data);

        if ($data['content']->content_type_name == 'album') {
            $output = $this->tpl->fetch('common/helpers/structured_gallery_data.tpl', $params);
        } elseif (!empty($data['video'])) {
            $output = $this->tpl->fetch('common/helpers/structured_video_data.tpl', $params);
        } else {
            $output = $this->tpl->fetch('common/helpers/structured_article_data.tpl', $params);
        }

        return $output;
    }
    /**
     *  Method to retrieve the tags for a list of tag ids
     *
     * @param array $ids List of ids we want to retrieve
     *
     * @return string List of tags fo this tags.
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
