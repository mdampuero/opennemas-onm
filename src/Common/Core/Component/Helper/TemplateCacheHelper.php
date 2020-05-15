<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Common\Core\Component\Template\Cache\CacheManager;
use Common\ORM\Core\EntityManager;

class TemplateCacheHelper
{
    /**
     * The CacheManager service.
     *
     * @var CacheManager
     */
    protected $cache;

    /**
     * Initializes the TemplateCacheHelper.
     *
     * @param CacheManager  $cache The CacheManager service.
     * @param EntityManager $em    The EntityManager service.
     */
    public function __construct(CacheManager $cache, EntityManager $em)
    {
        $this->cache = $cache;
        $this->em    = $em;
    }

    /**
     * Deletes cache files for contents created in the last 24 hours by any
     * user in the list.
     *
     * @param array $users The list of users.
     */
    public function deleteContentsByUsers(array $users) : void
    {
        if (empty($users)) {
            return;
        }

        $ids = array_map(function ($a) {
            return $a->id;
        }, array_filter($users, function ($a) {
            return $a->getOrigin() !== 'manager';
        }));

        if (empty($ids)) {
            return;
        }

        $sql = 'SELECT pk_content FROM contents WHERE fk_author IN (?)'
            . ' AND content_status = 1 AND in_litter = 0 and starttime >= ?';

        $params = [ $ids, date('Y-m-d H:i:s', strtotime('-1 day')) ];
        $types  = [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY, \PDO::PARAM_STR ];

        $contents = $this->em->getConnection('instance')
            ->fetchAll($sql, $params, $types);

        foreach ($contents as $content) {
            $this->cache->delete('content', $content['pk_content']);
        }
    }

    /**
     * Deletes cache files for a list of newsstands.
     *
     * @param array $newsstands The list of newsstands to delete cache for.
     */
    public function deleteNewsstands(array $newsstands) : void
    {
        $this->cache->delete('newsstand', 'list');

        foreach ($newsstands as $newsstand) {
            $this->cache->delete(
                'content',
                $newsstand->pk_content
            );
        }
    }

    /**
     * Deletes cache files for a list of users.
     *
     * @param array $users The list of users to delete cache for.
     */
    public function deleteUsers(?array $users) : void
    {
        $this->cache->delete('frontpage', 'authors');
        $this->cache->delete('opinion', 'list');

        if (!empty($users)) {
            foreach ($users as $user) {
                $this->cache->delete('frontpage', 'author', $user->id);
                $this->cache->delete('opinion', 'listauthor', $user->id);
            }
        }
    }
}
