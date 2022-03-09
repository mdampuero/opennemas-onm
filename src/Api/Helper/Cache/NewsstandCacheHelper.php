<?php

namespace Api\Helper\Cache;

class NewsstandCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'content_type_name-widget-{{content_type_name}}' .
        '.*category-widget-(({{category_id}})|(all))' .
        '.*tag-widget-(({{tag_id}})|(all))',
        '{{content_type_name}}-frontpage-{{starttime}}'
    ];

    /**
     * {@inheritdoc}
     */
    protected function replaceWildcards($item, $keys)
    {
        foreach ($keys as &$key) {
            preg_match_all('@{{([A-Za-z0-9_-]+)}}@', $key, $matches);

            foreach ($matches[1] as $match) {
                if (in_array($match, [ 'starttime', 'created', 'endtime' ])) {
                    $key = !empty($item->{$match}) ?
                        preg_replace(sprintf('@{{%s}}@', $match), $item->{$match}->format('Y-m'), $key) :
                        null;
                    continue;
                }

                if ($match === 'category_id') {
                    $key = preg_replace(sprintf('@{{%s}}@', $match), $item->categories[0] ?? 0, $key);

                    continue;
                }

                if ($match === 'tag_id') {
                    $tagIds = implode('|', array_map(function ($tag) {
                        return sprintf('(' . $tag . ')');
                    }, $item->tags));

                    $key = preg_replace(sprintf('@{{%s}}@', $match), $tagIds, $key);
                    continue;
                }

                $key = !empty($item->{$match}) ?
                    preg_replace(sprintf('@{{%s}}@', $match), $item->{$match}, $key) :
                    null;
            }
        }

        return array_filter($keys);
    }
}
