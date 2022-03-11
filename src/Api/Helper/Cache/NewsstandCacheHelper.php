<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Content;

class NewsstandCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected function replaceStarttime(Content $item)
    {
        return $item->starttime->format('Y-m');
    }
}
