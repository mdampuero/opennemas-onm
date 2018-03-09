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

use Api\Exception\CreateItemException;
use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Api\Exception\GetItemException;
use Api\Exception\GetListException;
use Api\Exception\PatchItemException;
use Api\Exception\PatchListException;
use Api\Exception\UpdateItemException;
use Api\Service\Service;
use Common\ORM\Entity\UserGroup;

class UserGroupService extends Service
{
    /**
     * Creates a new user group.
     *
     * @param array $data The user group data.
     *
     * @return UserGroup The new user group.
     */
    public function createItem($data)
    {
        try {
            $em   = $this->container->get('orm.manager');
            $data = $em->getConverter('UserGroup')
                ->objectify($data);

            $userGroup = new UserGroup($data);

            $em->persist($userGroup, $this->origin);

            return $userGroup;
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new CreateItemException();
        }
    }

    /**
     * Deletes an user group.
     *
     * @param integer $id The user group id.
     *
     * @throws DeleteItemException If the user group could not be deleted.
     */
    public function deleteItem($id)
    {
        try {
            $item = $this->getItem($id);
            $em   = $this->container->get('orm.manager');

            $em->remove($item, $item->getOrigin());
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteItemException();
        }
    }

    /**
     * Deletes a list of user groups.
     *
     * @param array $ids The list of ids.
     *
     * @return integer The number of successfully deleted user groups.
     */
    public function deleteList($ids)
    {
        if (!is_array($ids) || empty($ids)) {
            throw new DeleteListException('Invalid ids', 400);
        }

        $oql = sprintf('pk_user_group in [%s]', implode(',', $ids));

        try {
            $response = $this->getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteListException();
        }

        $em      = $this->container->get('orm.manager');
        $deleted = 0;
        foreach ($response['results'] as $item) {
            try {
                $em->remove($item, $item->getOrigin());
                $deleted++;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
            }
        }

        return $deleted;
    }

    /**
     * Returns an user group.
     *
     * @param integer $id The user group id.
     *
     * @return UserGroup The user group.
     *
     * @throws GetItemException If the user group was not found.
     */
    public function getItem($id)
    {
        try {
            return $this->container->get('orm.manager')
                ->getRepository('UserGroup', $this->origin)->find($id);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetItemException();
        }
    }

    /**
     * Returns a list of user groups basing on a criteria.
     *
     * @param string $oql The criteria.
     *
     * @return array The list of user groups.
     *
     * @throws GetListException If there was a problem to find user groups.
     */
    public function getList($oql)
    {
        try {
            $repository = $this->container->get('orm.manager')
                ->getRepository('UserGroup', $this->origin);

            $total      = $repository->countBy($oql);
            $userGroups = $repository->findBy($oql);

            return [
                'results' => $userGroups,
                'total'   => $total,
            ];
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetListException();
        }
    }

    /**
     * Updates some user group properties.
     *
     * @param integer $id   The user group id.
     * @param array   $data The new user group information.
     *
     * @throws PatchItemException If the user group could not be patched.
     */
    public function patchItem($id, $data)
    {
        try {
            $em   = $this->container->get('orm.manager');
            $data = $em->getConverter('UserGroup')
                ->objectify($data);

            $userGroup = $this->getItem($id);

            $userGroup->merge($data);

            $em->persist($userGroup);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new PatchItemException();
        }
    }

    /**
     * Updates some properties for a list of user groups.
     *
     * @param array $ids  The list of ids.
     * @param array $data The properties to update.
     *
     * @return integer The number of successfully updated user groups.
     */
    public function patchList($ids, $data)
    {
        if (!is_array($ids) || empty($ids)) {
            throw new PatchListException('Invalid ids', 400);
        }

        $em   = $this->container->get('orm.manager');
        $data = $em->getConverter('UserGroup')->objectify($data);
        $oql  = sprintf('pk_user_group in [%s]', implode(',', $ids));

        try {
            $response = $this->getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new PatchListException();
        }

        $updated = 0;
        foreach ($response['results'] as $userGroup) {
            try {
                $userGroup->merge($data);
                $em->persist($userGroup);

                $updated++;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
            }
        }

        return $updated;
    }

    /**
     * Updates an user group.
     *
     * @param integer $id   The user group id.
     * @param array   $data The user group information.
     *
     * @throws UpdateItemException If the user group could not be updated.
     */
    public function updateItem($id, $data)
    {
        try {
            $em   = $this->container->get('orm.manager');
            $data = $em->getConverter('UserGroup')
                ->objectify($data);

            $userGroup = $this->getItem($id);
            $userGroup->setData($data);

            $em->persist($userGroup, $userGroup->getOrigin());
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new UpdateItemException();
        }
    }
}
