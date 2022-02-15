<?php

namespace Api\Helper\Cache;

class EventCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'frontpage-page',
        '{{content_type_name}}-{{pk_content}}-inner',
        '{{content_type_name}}-frontpage$',
        'rss-frontpage$',
        'sitemap',
        'tag-{{tag_id}}',
    ];
}
