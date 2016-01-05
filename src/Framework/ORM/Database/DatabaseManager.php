<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Database;

use Framework\ORM\Core\Entity;
use Framework\ORM\Core\Exception\InvalidPersisterException;
use Framework\ORM\Core\Exception\InvalidRepositoryException;
use Onm\Cache\CacheInterface;
use Onm\Database\DbalWrapper;

class DatabaseManager
{
    /**
     * The cache service for instance.
     *
     * @var CacheInterface
     */
    protected $icache;

    /**
     * The database connection for instance.
     *
     * @var DbalWrapper
     */
    protected $iconn;

    /**
     * The cache service for manager.
     *
     * @var CacheInterface
     */
    protected $mcache;

    /**
     * The database connection for manager.
     *
     * @var DbalWrapper
     */
    protected $mconn;

    /**
     * The source name.
     *
     * @var string
     */
    protected $source = 'Database';

    /**
     * Initializes the Braintree factory.
     *
     * @param BraintreeFactory $factory The Braintree factory.
     */
    public function __construct(CacheInterface $icache, DbalWrapper $iconn, CacheInterface $mcache, DbalWrapper $mconn)
    {
        $this->icache = $icache;
        $this->iconn  = $iconn;
        $this->mcache = $mcache;
        $this->mconn  = $mconn;
    }

    /**
     * Returns the database connection.
     *
     * @return DbalWrapper The database connection.
     */
    public function getConnection()
    {
        return $this->iconn;
    }

    /**
     * Returns a new persister to persit an entity.
     *
     * @param string $name The entity to persist.
     *
     * @return Persister The persister.
     *
     * @throws InvalidPersisterException If the persister does not exist.
     */
    public function getPersister(Entity $entity)
    {
        $class = get_class($entity);
        $class = substr($class, strrpos($class, '\\') + 1);

        $persister = __NAMESPACE__ . '\\Persister\\' . $class . 'Persister';

        if (class_exists($persister)) {
            return new $persister($this->icache, $this->iconn, $this->mcache, $this->mconn, $this->source);
        } else {
            throw new InvalidPersisterException($persister, $this->source);
        }
    }

    /**
     * Returns a new repository by name.
     *
     * @param string $name The repository name.
     *
     * @return Repository The repository.
     *
     * @throws InvalidRepositoryException If the repository does not exist.
     */
    public function getRepository($name)
    {
        $cache = $this->icache;
        $conn  = $this->iconn;

        $name   = explode('.', $name);
        $entity = $name[count($name) - 1];
        $entity = str_replace('_', '', $entity);

        if (count($name) == 2 && $name[0] === 'manager') {
            $cache  = $this->mcache;
            $conn   = $this->mconn;
            $entity = $name[1];
        }
        $repository = __NAMESPACE__ . '\\Repository\\'
            . ucfirst($entity) . 'Repository';

        if (class_exists($repository)) {
            return new $repository($cache, $conn, $this->source);
        } else {
            throw new InvalidRepositoryException($repository, $this->source);
        }
    }

    /**
     * Persists an entity in Braintree.
     *
     * @param Entity $entity The entity to remove.
     */
    public function persist(Entity $entity)
    {
        $persister = $this->getPersister($entity);

        if ($entity->exists()) {
            $persister->update($entity);
        } else {
            $persister->create($entity);
        }
    }

    /**
     * Removes an entity from Braintree.
     *
     * @param Entity $entity The entity to remove.
     *
     * @throws EntityNotFoundException If entity does not exist.
     */
    public function remove(Entity $entity)
    {
        $persister = $this->getPersister($entity);

        $persister->remove($entity);
    }
}
