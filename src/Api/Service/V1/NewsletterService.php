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
        try {
            if (!array_key_exists('type', $data)) {
                $data['type'] = 0;
            }

            if (!array_key_exists('enabled', $data)) {
                $data['enabled'] = 1;
            }

            $data['created'] = new \Datetime();
            $data['updated'] = new \Datetime();
        } catch (CreateExistingItemException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }

        return parent::createItem($data);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($id)
    {
        try {
            $item = $this->getItem($id);

            $this->em->remove($item, $item->getOrigin());
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteList($ids)
    {
        if (!is_array($ids) || empty($ids)) {
            throw new DeleteListException('Invalid ids', 400);
        }

        $oql = $this->getOqlForIds($ids);

        try {
            $response = parent::getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $deleted = 0;
        foreach ($response['items'] as $item) {
            // Ignore current user
            if ($item === $this->container->get('core.user')) {
                continue;
            }

            try {
                $this->em->remove($item, $item->getOrigin());
                $deleted++;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
            }
        }

        return $deleted;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            $oql = sprintf('id = %s', $id);

            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function responsify($item)
    {
        if (is_array($item)) {
            foreach ($item as &$i) {
                $i = $this->responsify($i);
            }

            return $item;
        }

        if ($item instanceof $this->class) {
            return parent::responsify($item);
        }

        return $item;
    }

    /**
     * Count total mailing sends in current month
     *
     * @param DateTime $lastInvoiceDate the DateTime object of the last invoice
     *
     * @return int Total number of mail sent in current mount
     */
    public function getSentNewslettersSinceLastInvoice($lastInvoiceDate)
    {
        // Get today DateTime
        $today = new \DateTime();

        // Get all newsletters updated between today and last invoice
        $oql = sprintf(
            'updated >= "%s" and updated <= "%s" and sent > 0 order by created desc',
            $lastInvoiceDate->format('Y-m-d H:i:s'),
            $today->format('Y-m-d H:i:s')
        );

        $newsletters = $this->getList($oql);
        $newsletters = $newsletters['items'];

        // Check if user has reached the limit of sent newsletters
        $total = 0;
        if (count($newsletters) > 0) {
            foreach ($newsletters as $newsletter) {
                $total += (int) $newsletter->sent;
            }
        }

        return $total;
    }
}
