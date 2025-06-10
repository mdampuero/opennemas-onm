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
use Api\Exception\GetListException;
use Api\Exception\InvalidArgumentException;
use Api\Exception\UpdateItemException;
use Api\Exception\ApiException;
use Opennemas\Orm\Core\Exception\EntityNotFoundException;

class UserService extends OrmService
{
    /**
     * {@inheritdoc}
     */
    protected $defaults = [
        'type' => 0
    ];

    /**
     * The default type value for users.
     */
    protected $type = 0;

    /**
     * The type for subscriber + user when converted to subscriber.
     */
    protected $ctype = 1;

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        try {
            if (!array_key_exists('email', $data)) {
                throw new InvalidArgumentException('The email is required', 400);
            }

            $item = $this->checkItem($data['email']);

            // Convert item to subscriber + user
            if (!empty($item)) {
                return $this->convert($item, 2);
            }
        } catch (CreateExistingItemException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }

        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        if (array_key_exists('password', $data)) {
            $data['password'] = $this->container
                ->get('core.security.encoder.password')
                ->encodePassword($data['password'], null);
        }

        return parent::createItem($data);
    }

    /**
     * Creates a new subscriber or updates an existing one by converting it and
     * assigning user groups.
     *
     * If a subscriber with the given email already exists,
     * it is converted to type 2 and assigned to the specified user groups.
     * Otherwise, a new subscriber is created using the parent class logic.
     *
     * @param array $data The subscriber data.
     * @return Entity The created or updated subscriber entity.
     *
     * @throws CreateExistingItemException If a known item-related conflict occurs.
     * @throws CreateItemException For any other unexpected creation errors.
     */
    public function createSubscriber($data)
    {
        try {
            $item = $this->checkSubscriber($data['email']);

            if (!empty($item)) {
                // Convert the subscriber to type 2 if not already.
                if ($item->type !== 1) {
                    $item = $this->convert($item, 2);
                }

                // Check if the provided user groups differ from the existing ones.
                if (!empty($data['user_groups']) && $this->areUserGroupsDifferent($item, $data['user_groups'])) {
                    // Assign the new user groups if they are different.
                    return $this->assignLists($item, $data);
                }

                // Return the existing item if no changes are needed.
                return $item;
            }
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
        if ($id == $this->container->get('core.user')->id) {
            throw new DeleteItemException('You cannot delete this item', 403);
        }

        try {
            $item = $this->getItem($id);

            // Convert if subscriber + user
            if ($item->type === 2) {
                $this->convert($item);
                return;
            }

            $this->em->remove($item, $item->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('deleteItem'), [
                'action' => __METHOD__,
                'id'   => $id,
                'item' => $item
            ]);
        } catch (\Exception $e) {
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
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $deleted = 0;
        foreach ($response['items'] as $item) {
            // Ignore current user
            if ($item === $this->container->get('core.user')) {
                continue;
            }

            try {
                // Convert if subscriber + user
                if ($item->type === 2) {
                    $this->convert($item);
                    $deleted++;
                    continue;
                }

                $this->em->remove($item, $item->getOrigin());
                $deleted++;

                $this->dispatcher->dispatch($this->getEventName('deleteList'), [
                    'action' => __METHOD__,
                    'id'   => $item->id,
                    'item' => $item
                ]);
            } catch (\Exception $e) {
                throw new DeleteListException($e->getMessage(), $e->getCode());
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
            if (empty($id)) {
                throw new \InvalidArgumentException();
            }

            $oql = sprintf('id = %s and type != 1', $id);
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy($oql);
        } catch (\Exception $e) {
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
            $item->eraseCredentials();
            return parent::responsify($item);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        try {
            if (!array_key_exists('email', $data)) {
                throw new \Exception('The email is required', 400);
            }

            $oql   = sprintf('id != "%s" and email = "%s"', $id, $data['email']);
            $items = $this->container->get('api.service.user')->getList($oql);

            if (!empty($items['items'])) {
                throw new \Exception(
                    'The email address is already in use',
                    409
                );
            }
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage(), $e->getCode());
        }

        if (array_key_exists('password', $data) && empty($data['password'])) {
            unset($data['password']);
        }

        if (array_key_exists('password', $data)) {
            $data['password'] = $this->container
                ->get('core.security.encoder.password')
                ->encodePassword($data['password'], null);
        }

        return parent::updateItem($id, $data);
    }

    /**
     * Checks if there is an item with the given email and it can be used to
     * create/update an item basing on it.
     *
     * @param string $email The email address.
     *
     * @return Entity The item with the given email.
     *
     * @throws Exception If there is an item but it can not be used to
     *                   create/update an item basing on it.
     */
    protected function checkItem($email)
    {
        try {
            $item = $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy(sprintf('email = "%s"', $email));
        } catch (EntityNotFoundException $e) {
            return null;
        }

        if ($item->type === $this->type || $item->type === 2) {
            throw new CreateExistingItemException(
                'The email address is already in use',
                409
            );
        }

        return $item;
    }

    /**
     * Checks if a subscriber with the given email exists.
     *
     * @param string $email The email address.
     *
     * @return Entity|null The subscriber entity with the given email, or null if not found.
     */
    protected function checkSubscriber($email)
    {
        try {
            $item = $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy(sprintf('email = "%s"', $email));
            return $item;
        } catch (EntityNotFoundException $e) {
            return null;
        }
    }

    /**
     * Checks if the provided user groups differ from the existing ones.
     *
     * @param Entity $item The subscriber entity.
     * @param array $newUserGroups The new user groups to compare.
     * @return bool True if the user groups differ, false otherwise.
     */
    protected function areUserGroupsDifferent($item, array $newUserGroups)
    {
        // Get the current user groups from the item, defaulting to an empty array if none.
        $currentUserGroups = $item->user_groups ?? [];

        // Create copies to avoid modifying the original arrays.
        $currentToCompare = $currentUserGroups;
        $newToCompare     = $newUserGroups;

        // Sort the arrays for consistent comparison.
        if (is_array($currentToCompare)) {
            sort($currentToCompare);
        }
        if (is_array($newToCompare)) {
            sort($newToCompare);
        }

        // Compare the sorted arrays to check for differences.
        return $currentToCompare !== $newToCompare;
    }

    /**
     * Converts an subscriber + user when deleting.
     *
     * @param Entity $item The item to convert.
     *
     * @return Entity
     */
    protected function convert($item, $type = null)
    {
        $item->type = $type;

        if (empty($type)) {
            $item->type = $this->ctype;
        }

        $this->em->persist($item, $item->getOrigin());

        return $item;
    }

    /**
     * Assigns user groups (lists) to the subscriber.
     *
     * @param Entity $item The item to assign lists to.
     * @param array $data The data containing user_groups.
     *
     * @return Entity
     */
    protected function assignLists($item, array $data)
    {
        if (!empty($data['user_groups'])) {
            $item->user_groups = $data['user_groups'];
        }

        $this->em->persist($item, $item->getOrigin());

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOqlForList($oql)
    {
         // Force OQL to include type
        return $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('type != 1')
            ->getOql();
    }

      /**
     * Moves all contents assigned to a user to another user.
     *
     * @param integer $id The user id of the source user.
     * @param integer $to The user id of the target user.
     */
    public function moveItem($id, $to)
    {
        try {
            $source = $this->getItem($id);
            if ($this->isItemEmpty($source)) {
                throw new ApiException('The item is empty', 400);
            }

            $target = $this->getItem($to);
            $moved  = $this->em->getRepository($this->entity, $this->origin)
                ->moveContents((int) $id, (int) $to);
            $this->dispatcher->dispatch($this->getEventName('moveItem'), [
                'id'       => $id,
                'item'     => $source,
                'target'   => $target,
                'contents' => $moved
            ]);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }
    /**
     * Checks if the user is empty of contents.
     *
     * @param Category $item The user.
     *
     * @return boolean True if the user is empty of contents. False otherwise.
     */
    protected function isItemEmpty($item)
    {
        try {
            $contents = $this->em->getRepository($this->entity, $this->origin)
                ->countContents($item->id);
            if (!empty($contents)
                && array_key_exists((int) $item->id, $contents)
                && !empty($contents[$item->id])
            ) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Retrieves the statistics for a given list of items.
     *
     * @param array|object $items The items to retrieve statistics for.
     * If a single object is provided, it will be wrapped in an array.
     * @return array An associative array containing the statistics for the given items.
     * @throws ApiException If an error occurs while fetching the statistics.
     */
    public function getStats($items)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = array_map(function ($a) {
            return $a->id;
        }, $items);

        try {
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->countContentsAuthors($ids);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get all the users for report.
     *
     * @return array The list of items.
     */
    public function getReport()
    {
        try {
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findUsers();
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }
}
