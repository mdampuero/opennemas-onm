<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

use Api\Exception\CreateExistingItemException;
use Api\Exception\CreateItemException;
use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Api\Exception\GetItemException;
use Api\Exception\UpdateItemException;
use Common\ORM\Core\Exception\EntityNotFoundException;

class NewsletterService extends OrmService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        if (!array_key_exists('type', $data)) {
            $data['type'] = 0;
        }

        if (!array_key_exists('enabled', $data)) {
            $data['enabled'] = 1;
        }

        if (!array_key_exists('sent', $data)) {
            $data['sent'] = null;
        }

        $data['created'] = new \Datetime(null);
        $data['updated'] = new \Datetime();

        $data['created']->setTimeZone(new \DateTimeZone('UTC'));
        $data['updated']->setTimeZone(new \DateTimeZone('UTC'));

        return parent::createItem($data);
    }

    /**
     * Count total mailing sends in current month.
     *
     * @param DateTime $lastInvoiceDate the DateTime object of the last invoice.
     *
     * @return int Total number of mail sent in current mount.
     */
    public function getSentNewslettersSinceLastInvoice($lastInvoiceDate)
    {
        // Get today DateTime
        $today = new \DateTime();

        // Get all newsletters updated between today and last invoice
        $oql = sprintf(
            'updated >= "%s" and updated <= "%s" and sent_items > 0 order by created desc',
            $lastInvoiceDate->format('Y-m-d H:i:s'),
            $today->format('Y-m-d H:i:s')
        );

        $newsletters = $this->getList($oql);
        $newsletters = $newsletters['items'];

        // Check if user has reached the limit of sent newsletters
        $total = 0;
        if (count($newsletters) > 0) {
            foreach ($newsletters as $newsletter) {
                $total += (int) $newsletter->sent_items;
            }
        }

        return $total;
    }
}
