<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent;

use Common\NewsAgency\Component\Parser\NewsML\NewsML;

/**
 * Parses NewsComponent container from NewsML files.
 */
class NewsMLComponentList extends NewsML
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }

        $node = $data->xpath('/NewsComponent/NewsComponent');

        if (!is_array($node) || empty($node)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $this->bag['title']   = $this->getTitle($data);
        $this->bag['created'] = $this->getCreatedTime($data);

        $list = $data->xpath('/NewsComponent/NewsComponent');

        $contents = [];
        foreach ($list as $item) {
            $item = simplexml_load_string($item->asXML());

            $content = $this->parseComponent($item);

            if (is_array($content)) {
                $contents = array_merge($contents, $content);
            }

            if (is_object($content)) {
                $contents[] = $content;
            }
        }

        // Get related resources
        $related      = [];
        $childRelated = [];
        foreach ($contents as $content) {
            if ($content->type === 'photo' && !$content->isChild) {
                $related[] = $content->id;
            }

            if ($content->type === 'text' && $content->isChild) {
                $related[] = $content->id;
            }

            if ($content->type === 'photo' && $content->isChild && !empty($content->uid)) {
                $childRelated[$content->uid] = $content->id;
            }
        }

        // Add related to texts resources
        foreach ($contents as $content) {
            $content->merge($this->bag);

            if ($content->type === 'text' && !$content->isChild) {
                $content->related = $related;
            }

            if ($content->type === 'text'
                && $content->isChild
                && !empty($content->uid)
                && array_key_exists($content->uid, $childRelated)
            ) {
                $content->related = [$childRelated[$content->uid]];
            }
        }

        return $contents;
    }
}
