<?php

namespace Api\Helper\Cache;

class PhotoCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'authors-frontpage',
        'content-author-{{fk_author}}-frontpage',
        'content_type_name-widget-{{content_type_name}}' .
        '.*tag-widget-({{tags}}|all)' .
        '.*author-widget-({{fk_author}}|all)',
        'rss-author-{{fk_author}}',
        'tag-{{tags}}',
    ];
}
