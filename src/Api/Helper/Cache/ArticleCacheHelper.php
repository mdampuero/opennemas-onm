<?php

namespace Api\Helper\Cache;

class ArticleCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'archive-content',
        'article-{{pk_content}}-inner',
        'authors-frontpage',
        'category-{{category_id}}',
        'content-author-{{fk_author}}',
        'frontpage-page',
        'rss-article,{{category_id}}',
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
