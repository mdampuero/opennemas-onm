<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Persister;

use Common\Cache\Core\Cache;
use Common\ORM\Core\Connection;
use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Instance;

/**
 * The TagPersister class defines actions to persist Tags.
 */
class TagPersister extends BasePersister
{
    /**
     * Initializes a new TagPersister.
     *
     * @param Connection $conn     The database connection.
     * @param Metadata   $metadata The entity metadata.
     * @param Cache      $cache    The cache service.
     * @param Instance   $instance The current instance.
     */
    public function __construct(Connection $conn, Metadata $metadata, Cache $cache = null, Instance $instance)
    {
        parent::__construct($conn, $metadata, $cache);

        $this->instance = $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        if ($this->hasCache()) {
            $this->removeEntitiesFromCache($entity);
        }

        parent::remove($entity);
    }

    /**
     * Removes all entities linked to the current entity from cache.
     *
     * @param Entity $entity The current entity.
     */
    protected function removeEntitiesFromCache($entity)
    {
        $sql = 'select content_id, content_type_name'
            . ' from contents_tags'
            . ' inner join contents on content_id = pk_content'
            . ' where tag_id = ?';

        $ids = $this->conn->fetchAll($sql, [ $entity->id ], [ \PDO::PARAM_INT ]);

        foreach ($ids as $id) {
            $cacheId = $id['content_type_name'] . '-' . $id['content_id'];

            $this->cache->removeByPattern(
                "*{$this->instance->internal_name}_{$cacheId}"
            );
        }
    }
}
