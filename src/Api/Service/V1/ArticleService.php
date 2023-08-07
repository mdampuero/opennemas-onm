<?php

namespace Api\Service\V1;

class ArticleService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        $item                    = parent::getItem($id);
        // Aux var to set webpush notifications checkbox
        $item->is_notified_check = $item->is_notified;

        if (!empty($item->live_blog_updates)) {
            $updates = $item->live_blog_updates;
            usort($updates, function ($a, $b) {
                return (strtotime($a['created']) > strtotime($b['created'])) ? -1 : 1;
            });

            $item->live_blog_updates = $updates;
        }

        return $item;
    }
}
