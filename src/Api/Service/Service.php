<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service;

abstract class Service
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the Service.
     *
     * @param \Api\Exception\ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Creates a new item.
     *
     * @param array $data The item data.
     *
     * @return mixed The new item.
     */
    abstract public function createItem($data);

    /**
     * Deletes an item.
     *
     * @param integer $id The item id.
     *
     * @throws \Api\Exception\DeleteItemException If the item could not be deleted.
     */
    abstract public function deleteItem($ids);

    /**
     * Deletes a list of item.
     *
     * @param array $ids The list of ids.
     *
     * @return integer The number of successfully deleted item.
     */
    abstract public function deleteList($item);

    /**
     * Returns an item.
     *
     * @param integer $id The item id.
     *
     * @return mixed The item.
     *
     * @throws \Api\Exception\GetItemException If the item was not found.
     */
    abstract public function getItem($id);

    /**
     * Returns a list of items basing on a criteria.
     *
     * @param string $oql The criteria.
     *
     * @return array The list of items.
     *
     * @throws \Api\Exception\GetListException If there was a problem to find items.
     */
    abstract public function getList($oql = '');

    /**
     * Updates some item properties.
     *
     * @param integer $id   The item id.
     * @param array   $data The new item information.
     *
     * @throws \Api\Exception\PatchItemException If the item could not be patched.
     */
    abstract public function patchItem($id, $data);

    /**
     * Updates some properties for a list of items.
     *
     * @param array $ids  The list of ids.
     * @param array $data The properties to update.
     *
     * @return integer The number of successfully updated items.
     */
    abstract public function patchList($ids, $data);

    /**
     * Updates an item.
     *
     * @param integer $id   The item id.
     * @param array   $data The item information.
     *
     * @throws \Api\Exception\UpdateItemException If the item could not be updated.
     */
    abstract public function updateItem($id, $data);
}
