<?php

namespace Api\Helper\Cache;

class OpinionCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'opinion-author-{{fk_author}}-frontpage',
        'archive-page-{{starttime}}',
        'authors-frontpage',
        'content-author-{{fk_author}}-frontpage',
        '{{content_type_name}}-frontpage$',
        '{{content_type_name}}-{{pk_content}}',
        'content_type_name-widget-{{content_type_name}}' .
        '.*tag-widget-({{tags}}|all)' .
        '.*author-widget-({{fk_author}}|all)',
        'rss-author-{{fk_author}}',
        'rss-{{content_type_name}}$',
        'sitemap',
        'tag-{{tags}}',
        'header-date',
    ];
}
