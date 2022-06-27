<?php

namespace Api\Helper\Cache;

class NewsstandCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'archive-page-{{starttime}}',
        'category-{{categories}}',
        'newsstand-frontpage$',
        'newsstand-frontpage,category-newsstand-{{categories}}',
        'newsstand-{{pk_content}}',
        'content_type_name-widget-newsstand' .
        '.*category-widget-(({{categories}})|(all))' .
        'last-suggested-{{categories}}',
        'rss-newsstand$',
        'sitemap',
        'header-date',
    ];
}
