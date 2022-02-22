<?php

namespace Api\Helper\Cache;

class AlbumCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'authors-frontpage',
        'category-{{category_id}}',
        'content-author-{{fk_author}}',
        'frontpage-page',
        '{{content_type_name}}-*-inner',
        '{{content_type_name}}-frontpage$',
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
