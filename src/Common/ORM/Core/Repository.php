<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

/**
 * The Repository class defines methods that every repository has to implement.
 */
abstract class Repository
{
    /**
     * The repository name.
     *
     * @var string
     */
    protected $name;

    /**
     * Counts the number of entities that match the criteria.
     *
     * @param strign $oql The criteria.
     *
     * @return integer The number of entities.
     */
    abstract public function countBy($oql = '');

    /**
     * Finds an entity by id.
     *
     * @param mixed $id The entity id.
     *
     * @return Entity The entity.
     *
     * @throws EntityNotFoundException  If the entity is not found.
     * @throws InvalidArgumentException If the given id is invalid.
     */
    abstract public function find($id);

    /**
     * Finds entities that match a criteria.
     *
     * @param string $oql The criteria.
     *
     * @return array The list of entities.
     */
    abstract public function findBy($oql = '');

    /**
     * Returns the first entity that matches a criteria.
     *
     * @param string $oql The criteria.
     *
     * @return Entity The entity.
     *
     * @throws EntityNotFoundException If the entity is not found.
     */
    abstract public function findOneBy($oql = '');
}
