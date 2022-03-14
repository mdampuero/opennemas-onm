<?php

namespace Api\Helper\Cache;

class ArticleCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $redisKeys = [ 'suggested_contents_{{content_type_name}}_{{categories}}*' ];

    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [ 'rss-instant-articles' ];
}
