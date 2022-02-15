<?php

namespace Api\Helper\Cache;

class AlbumCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'archive-content',
        'authors-frontpage',
        'content-author-{{fk_author}}',
        'frontpage-page',
        '{{content_type_name}}-*-inner',
        '{{content_type_name}}-frontpage$',
        '{{content_type_name}}-frontpage,category-{{category_id}}',
        'rss-author-{{fk_author}}',
        'rss-frontpage$',
        'rss-{{content_type_name}}$',
        'rss-{{content_type_name}},category-{{category_id}}',
        'sitemap',
        'tag-{{tag_id}}',
    ];

    /**
     * {@inheritdoc}
     */
    protected $redisKeys = [
        '*WidgetAlbumLatest*'
    ];
}
