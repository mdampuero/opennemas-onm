<?php

namespace Api\Helper\Cache;

class ArticleCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $redisKeys = [
        'suggested_contents_{{content_type_name}}_{{categories}}',
        'suggested_contents_{{content_type_name}}_{{categories}}_{{pk_content}}'
    ];

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

    /**
     * {@inheritdoc}
     */
    protected $varnishModuleKeys = [
        'es.openhost.module.google_news_showcase' => ['rss-google-news-showcase'],
        'FIA_MODULE' => ['rss-instant-articles']
    ];
}
