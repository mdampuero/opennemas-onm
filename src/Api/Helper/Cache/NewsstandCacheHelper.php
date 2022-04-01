<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Content;

class NewsstandCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $defaultVarnishKeys = [
        '{{content_type_name}}-frontpage',
        '{{content_type_name}}-frontpage-{{date}}',
        '{{content_type_name}}-{{pk_content}}',
        'content_type_name-widget-{{content_type_name}}' .
        '.*category-widget-(({{categories}})|(all))' .
        '.*tag-widget-(({{tags}})|(all))' .
        '.*author-widget-(({{fk_author}})|(all))',
    ];

    /**
     * {@inheritdoc}
     */
    protected function replaceDate(Content $item)
    {
        return substr($item->date, 0, strrpos($item->date, '-'));
    }
}
