<?php

namespace Common\Core\Component\Helper;

use Symfony\Component\DependencyInjection\Container;
use Api\Service\ContentService;

/**
* Perform searches in Database Poll data
*/
class PollHelper
{
    /**
     * Returns the total votes of poll
     *
     * @param mixed $item The item to get poll.
     *
     * @return array The total votes of poll items.
     */
    public function getTotalVotes($items = []) : ?array
    {
        if (is_object($items)) {
            $items = [ $items ];
        }

        if (empty($items)) {
            return null;
        }

        $total_votes = [];

        foreach ($items as $item) {
            if ($item instanceof \Common\Model\Entity\Content
            && !empty($item->items)
            ) {
                try {
                    $total_votes[$item->pk_content] = array_reduce($item->items, function ($total, $value) {
                        return $total += $value['votes'];
                    });
                } catch (\Exception $e) {
                    return null;
                }
            }
        }

        return $total_votes;
    }

    /**
     * Returns whether a survey is closed or not
     *
     * @param mixed $item The item to get poll.
     *
     * @return bool True or False
     */
    public function isClosed($item)
    {
        return !empty($item->closetime)
            && $item->closetime < date('Y-m-d H:i:s');
    }
}
