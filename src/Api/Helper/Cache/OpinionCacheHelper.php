<?php

namespace Api\Helper\Cache;

class OpinionCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'opinion-author-{{fk_author}}-frontpage'
    ];
}
