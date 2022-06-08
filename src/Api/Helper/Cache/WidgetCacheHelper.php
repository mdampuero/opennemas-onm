<?php

namespace Api\Helper\Cache;

class WidgetCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $defaultVarnishKeys = [
        '{{content_type_name}}-{{pk_content}}'
    ];
}
