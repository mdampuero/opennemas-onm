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
        'sitemap',
        'tag-{{tag_id}}',
    ];
}
