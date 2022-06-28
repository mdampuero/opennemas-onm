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
        'category-{{categories}}',
        'content-author-{{fk_author}}-frontpage',
        '{{content_type_name}}-frontpage$',
        '{{content_type_name}}-frontpage,category-{{content_type_name}}-{{categories}}',
        'content_type_name-widget-{{content_type_name}}' .
        '.*category-widget-({{categories}}|all)' .
        '.*tag-widget-({{tags}}|all)' .
        '.*author-widget-({{fk_author}}|all)',
        'last-suggested-{{categories}}',
        'tag-{{tags}}',
        'header-date',
    ];
}
