<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Model\Database\Persister;

use Common\Model\Entity\Instance;
use Opennemas\Cache\Core\Cache;
use Opennemas\Orm\Core\Connection;
use Opennemas\Orm\Core\Entity;
use Opennemas\Orm\Core\Metadata;
use Opennemas\Orm\Database\Persister\BasePersister;

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

            $this->cache->remove($cacheId);
        }
    }

    public function update(Entity $entity)
    {
        if ($entity->private === 1) {
            $this->removeMenuItems($entity->slug, 'tags');
        }


        if ($this->hasCache()) {
            $this->cache->remove($this->metadata->getPrefixedId($entity));
        }
    }

    protected function removeMenuItems($slug, $type)
    {
        $sql = 'delete from menu_items'
            . ' where link_name = ? and type = ?';

        $params = [
            $slug,
            $type,
        ];
        $types  = [
            \PDO::PARAM_STR,
            \PDO::PARAM_STR,
        ];

        $this->conn->executeQuery($sql, $params, $types);
    }
}
