<?php

namespace Api\Helper\Cache;

class ArticleCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'article-{{pk_content}}-inner',
        'category-{{category_id}}',
        'frontpage-page',
        'sitemap',
        'rss-article,{{category_id}}',
        'rss-frontpage$',
        'rss-author-{{fk_author}}',
        'authors-frontpage',
        'content-author-{{fk_author}}',
        'tag-{{tag_id}}',
        'archive-content',
    ];

    /**
     * {@inheritdoc}
     */
    protected $redisKeys = [
        '*WidgetLastInSectionWithPhoto-*-{{category_id}}',
        '*WidgetInfiniteScroll-*-*-*-{{category_id}}',
        '*WidgetNextPrevious-*-article-*-{{category_id}}',
        '*suggested_contents_{{content_type_name}}_{{category_id}}'
    ];
}
