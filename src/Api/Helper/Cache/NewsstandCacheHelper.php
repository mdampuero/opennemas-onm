<?php

namespace Api\Helper\Cache;

class NewsstandCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        '{{content_type_name}}-{{pk_content}}-inner',
        '{{content_type_name}}-frontpage$',
        '{{content_type_name}}-frontpage-{{starttime}}',
        'sitemap',
        'tag-{{tag_id}}',
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

                if ($match === 'tag_id') {
                    $key = implode('|', array_map(function ($tag) use ($match, $key) {
                        return '(' . preg_replace(sprintf('@{{%s}}@', $match), $tag, $key) . ')';
                    }, $item->tags));
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
