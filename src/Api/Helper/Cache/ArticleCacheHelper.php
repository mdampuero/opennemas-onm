<?php

namespace Api\Helper\Cache;

class ArticleCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'archive-page-{{starttime}}',
        'authors-frontpage',
        'category-{{category_id}}',
        'content-author-{{fk_author}}',
        '{{content_type_name}}-{{pk_content}}-inner',
        'frontpage-page',
        'rss-{{content_type_name}}$',
        'rss-{{content_type_name}},{{category_id}}',
        'rss-frontpage$',
        'rss-author-{{fk_author}}',
        'sitemap',
        'tag-{{tag_id}}',
    ];

    /**
     * {@inheritdoc}
     */
    protected $redisKeys = [
        '*suggested_contents_{{content_type_name}}_{{category_id}}',
        '*WidgetInfiniteScroll-*-*-*-{{category_id}}',
        '*WidgetLastInSectionWithPhoto-*-{{category_id}}',
        '*WidgetNextPrevious-*-article-*-{{category_id}}',
    ];
}
