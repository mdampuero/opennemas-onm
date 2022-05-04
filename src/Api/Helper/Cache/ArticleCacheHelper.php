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
    protected $varnishKeys = [ 'rss-instant-articles' ];
}
