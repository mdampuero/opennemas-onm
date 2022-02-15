<?php

namespace Api\Helper\Cache;

class OpinionCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'authors-frontpage',
        'content-author-{{fk_author}}',
        'frontpage-page',
        '{{content_type_name}}-{{pk_content}}-inner',
        '{{content_type_name}}-author-{{fk_author}}',
        '{{content_type_name}}-frontpage',
        'rss-author-{{fk_author}}',
        'rss-frontpage$',
        'rss-{{content_type_name}}',
        'sitemap',
        'tag-{{tag_id}}',
    ];
}
