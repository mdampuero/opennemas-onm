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
    protected $varnishKeys = [];

    /**
     * {@inheritdoc}
     */
    protected $varnishModuleKeys = [
        'es.openhost.module.google_news_showcase' => ['rss-google-news-showcase'],
        'FIA_MODULE' => ['rss-instant-articles']
    ];
}
