<?php

namespace Api\Helper\Cache;

class AttachmentCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $varnishKeys = [
        'archive-page-{{starttime}}',
        'category-{{categories}}',
        'content_type_name-widget-{{content_type_name}}' .
        '.*category-widget-({{categories}}|all)' .
        'last-suggested-{{categories}}',
    ];
}
