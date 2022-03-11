<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Content;

class NewsstandCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'content_type_name-widget-{{content_type_name}}' .
        '.*category-widget-(({{category_id}})|(all))' .
        '.*tag-widget-(({{tag_id}})|(all))',
        '{{content_type_name}}-frontpage-{{starttime}}'
    ];

    /**
     * {@inheritdoc}
     */
    protected function replaceStarttime(Content $item)
    {
        return $item->starttime->format('Y-m');
    }
}
