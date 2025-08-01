<?php

namespace Api\Helper\Cache;

class WidgetCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        '{{content_type_name}}-{{pk_content}}',
        'widget-not-found-{{pk_content}}',
        'widget-not-found-{{class}}',
        'last-suggested-{{categories}}',
        'header-date',
    ];
}
