<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Content;

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
        '.*category-widget-({{categories}}|all)' .
        'last-suggested-{{categories}}',
        'sitemap',
        'header-date',
    ];

    /**
     * {@inheritdoc}
     */
    protected function replaceDate(Content $item)
    {
        return substr($item->date, 0, strrpos($item->date, '-'));
    }
}
