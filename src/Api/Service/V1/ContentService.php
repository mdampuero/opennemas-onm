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

use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Api\Exception\GetListException;

class ContentService extends OrmService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        $data['changed'] = new \DateTime();
        $data['created'] = new \DateTime();

        $data = $this->assignUser($data, [ 'fk_user_last_editor', 'fk_publisher' ]);

        return parent::createItem($data);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($id)
    {
        $related = $this->getRelatedContents($id);

        try {
            $item = $this->getItem($id);

            $this->em->remove($item, $item->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('deleteItem'), [
                'action'  => __METHOD__,
                'id'      => $id,
                'item'    => $item,
                'related' => $related
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
        if (!is_array($ids)) {
            throw new DeleteListException('Invalid ids', 400);
        }

        try {
            $response = $this->getListByIds($ids);
        } catch (\Exception $e) {
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $items   = [];
        $deleted = array_map(function ($a) {
                return $a->pk_content;
        }, $response['items']);

        $related = $this->getRelatedContents(implode(',', $deleted));

        foreach ($response['items'] as $item) {
            try {
                $this->em->remove($item, $item->getOrigin());

                $items[] = $item;
            } catch (\Exception $e) {
                throw new DeleteListException($e->getMessage(), $e->getCode());
            }
        }

        $this->dispatcher->dispatch($this->getEventName('deleteList'), [
            'action'  => __METHOD__,
            'ids'     => $deleted,
            'item'    => $items,
            'related' => $related
        ]);

        return count($deleted);
    }

    /**
     * Returns a content basing on a slug and content type.
     *
     * @param string $slug        The category slug.
     * @param string $contentType The id of the content type.
     *
     * @return Content The content.
     */
    public function getItemBySlugAndContentType($slug, $contentType)
    {
        $part = 'slug = "%s"';
        if ($this->container->get('core.instance')->hasMultilanguage()) {
            $part = 'slug regexp "(.+\"|^)%s(\".+|$)"';
        }

        $oql = sprintf(
            $part . ' and content_type_name="%s" and in_litter=0 and content_status=1',
            $slug,
            $contentType
        );

        return $this->getItemBy($oql);
    }

    /**
     * {@inheritdoc}
     */
    protected function getOqlForList($oql)
    {
        $cleanOql = preg_replace('/and tag\\s*=\\s*\"?([0-9]*)\"?\\s*/', '', $oql);
        preg_match('/tag\\s*=\\s*\"?([0-9]*)\"?\\s*/', $oql, $matches);

        $fixer = $this->container->get('orm.oql.fixer')->fix($cleanOql);

        if (!empty($matches)) {
            $fixer->addCondition(sprintf('tag_id in [%s]', $matches[1]));
        }

        return $fixer->getOql();
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        if (preg_match('/order by.*event_(start|end)_date/i', $oql)) {
            list($criteria, $order, $epp, $page) = $this->container
                ->get('core.helper.oql')
                ->getFiltersFromOql($oql);

            $cleanCriteria = preg_replace('/and tag\s*=\s*\"?([0-9]*)\"?\s*/', '', $criteria);
            preg_match('/tag\s*=\s*\"?([0-9]*)\"?\s*/', $criteria, $matches);

            $tagJoin = '';
            if (!empty($matches)) {
                $tagJoin = sprintf(
                    ' inner join content_tag on contents.pk_content = content_tag.content_id and content_tag.tag_id in (%s) ',
                    $matches[1]
                );

                $countOql = $this->container->get('orm.oql.fixer')->fix($cleanCriteria)
                    ->addCondition(sprintf('tag_id in [%s]', $matches[1]))
                    ->getOql();

                $total = $this->countBy($countOql);
            } else {
                $countOql = $this->container->get('orm.oql.fixer')->fix($cleanCriteria)
                    ->getOql();

                $total = $this->countBy($countOql);
            }

            $criteria = $cleanCriteria;
            $order = str_replace(
                [ 'event_start_date', 'event_end_date' ],
                [ 'start_date_meta.meta_value', 'end_date_meta.meta_value' ],
                $order
            );

            $limit = '';
            if ($epp > 0) {
                $offset = ($page - 1) * $epp;
                $limit = sprintf(' limit %d offset %d', $epp, $offset);
            }

            $sql = 'SELECT contents.* FROM contents '
                . $tagJoin
                . 'left join contentmeta as start_date_meta on contents.pk_content = start_date_meta.fk_content '
                . 'and start_date_meta.meta_name = "event_start_date" '
                . 'left join contentmeta as end_date_meta on contents.pk_content = end_date_meta.fk_content '
                . 'and end_date_meta.meta_name = "event_end_date" ';

            if (!empty($criteria)) {
                $sql .= 'WHERE ' . $criteria . ' ';
            }

            if (!empty($order)) {
                $sql .= 'ORDER BY ' . $order . ' ';
            }

            $sql .= $limit;

            $repository = $this->em->getRepository($this->entity, $this->origin);
            $items = $repository->findBySql($sql);

            $this->localizeList($items);

            return [ 'items' => $items, 'total' => $total ];
        }

        return parent::getList($oql);
    }

    /**
     * Returns the diferent related contents for ids passeds
     *
     * @param string $ids The list of ids for search related contents comma separated
     *
     * @return array The list of related contents.
     */
    protected function getRelatedContents($ids)
    {
        $sql = 'SELECT contents.* FROM contents'
            . ' INNER JOIN content_content ON contents.pk_content = content_content.source_id'
            . ' WHERE content_content.target_id in (' . $ids . ')';

        return $this->getListBySql($sql)['items'];
    }

    /**
     * {@inheritdoc}
     */
    public function patchItem($id, $data)
    {
        $data['changed'] = new \DateTime();

        $data = $this->assignUser($data, [ 'fk_user_last_editor' ]);

        parent::patchItem($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function patchList($ids, $data)
    {
        $data['changed'] = new \DateTime();

        $data = $this->assignUser($data, [ 'fk_user_last_editor' ]);

        return parent::patchList($ids, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        $data['changed'] = new \DateTime();

        $data = $this->assignUser($data, [ 'fk_user_last_editor' ]);
        parent::updateItem($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function responsify($item)
    {
        $item = \Onm\StringUtils::convertToUtf8($item);

        return parent::responsify($item);
    }

    /**
     * Assign the user data for content.
     *
     * @param array $data       The content data.
     * @param array $userFields The user data fields to update.
     *
     * @return array The data with the current user on user fields.
     */
    protected function assignUser($data, $userFields = [])
    {
        if (!$this->isEditable($data)) {
            return $data;
        }

        $currentUserId = $this->container->get('core.user')->id ?? null;

        return array_merge($data, array_fill_keys($userFields, $currentUserId));
    }

    /**
     * {@inheritdoc}
     */
    protected function localizeItem($item)
    {
        $keys = [
            'related_contents' => [ 'caption' ]
        ];

        $item = parent::localizeItem($item);

        foreach ($keys as $key => $value) {
            if (!empty($item->{$key})) {
                $item->{$key} = $this->container->get('data.manager.filter')
                    ->set($item->{$key})
                    ->filter('localize', [ 'keys' => $value ])
                    ->get();
            }
        }

        return $item;
    }

    /**
     * Checks if the last editor needs to be changed.
     *
     * @param array $data The array of data to update.
     *
     * @return boolean True if the last editor needs to be changed, false otherwise.
     */
    protected function isEditable($data = [])
    {
        if ($data === [ 'frontpage' => 0 ]) {
            return false;
        }

        if ($this->container->get('core.security')->hasPermission('MASTER')) {
            return false;
        }

        return true;
    }

      /**
     * {@inheritdoc}
     */
    public function getPendingNotifications()
    {
        $sql = 'SELECT pk_content FROM content_notifications'
        . ' INNER JOIN contents ON fk_content = pk_content'
        . ' WHERE status = 0';

        return $this->getListBySql($sql)['items'];
    }

    /**
     * {@inheritdoc}
     */
    protected function validate($item)
    {
        if (!empty($item->related_contents)) {
            $item->related_contents = array_filter($item->related_contents, function ($related) {
                try {
                    return !empty($this->getItem($related['target_id'])) ? true : false;
                } catch (\Exception $e) {
                    return false;
                }
            });
        }

        parent::validate($item);
    }

    public function getListWithoutLocalizer($oql = '')
    {
        try {
            $oql = $this->getOqlForList($oql);

            $repository = $this->em->getRepository($this->entity, $this->origin);

            $response = [ 'items' => $repository->findBy($oql) ];

            if ($this->count) {
                $response['total'] = $repository->countBy($oql);
            }

            $this->dispatcher->dispatch($this->getEventName('getList'), [
                'items' => $response['items'],
                'oql'   => $oql
            ]);

            return $response;
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }
}
