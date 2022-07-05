<?php

namespace Api\Helper\Cache;

class StaticPageCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        '{{content_type_name}}-{{pk_content}}',
        'header-date',
    ];
}
