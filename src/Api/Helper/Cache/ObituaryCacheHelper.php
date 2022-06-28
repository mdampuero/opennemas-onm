<?php

namespace Api\Helper\Cache;

class ObituaryCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $defaultVarnishKeys = [
        'archive-page-{{starttime}}',
        'authors-frontpage',
        'content-author-{{fk_author}}-frontpage',
        '{{content_type_name}}-frontpage$',
        '{{content_type_name}}-{{pk_content}}',
        'content_type_name-widget-{{content_type_name}}' .
        '.*tag-widget-({{tags}}|all)' .
        '.*author-widget-({{fk_author}}|all)',
        'rss-author-{{fk_author}}',
        'tag-{{tags}}',
        'header-date',
    ];
}
