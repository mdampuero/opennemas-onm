<?php

namespace Api\Helper\Cache;

class AlbumCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'archive-page-{{starttime}}',
        'authors-frontpage',
        'category-{{categories}}',
        'content-author-{{fk_author}}-frontpage',
        '{{content_type_name}}-frontpage$',
        '{{content_type_name}}-frontpage,category-{{content_type_name}}-{{categories}}',
        '{{content_type_name}}-{{pk_content}}',
        'content_type_name-widget-{{content_type_name}}' .
        '.*category-widget-({{categories}}|all)' .
        '.*tag-widget-({{tags}}|all)' .
        '.*author-widget-({{fk_author}}|all)',
        'last-suggested-{{categories}}',
        'rss-author-{{fk_author}}',
        'rss-{{content_type_name}}$',
        'sitemap',
        'tag-{{tags}}',
        'header-date',
    ];
}
