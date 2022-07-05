<?php

namespace Api\Helper\Cache;

class LetterCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'archive-page-{{starttime}}',
        '{{content_type_name}}-frontpage$',
        '{{content_type_name}}-{{pk_content}}',
        'content_type_name-widget-{{content_type_name}}' .
        '.*tag-widget-({{tags}}|all)' .
        'sitemap',
        'tag-{{tags}}',
        'header-date',
    ];
}
