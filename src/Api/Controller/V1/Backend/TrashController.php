<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Symfony\Component\HttpFoundation\JsonResponse;

class TrashController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'TRASH_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.trash';

    /**
     * Deletes all items in the trash.
     */
    public function emptyListAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('empty'));

        $msg = $this->get('core.messenger');

        $this->get($this->service)->emptyTrash();

        $msg->add(_('Items deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCategories($items = null)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = [];
        foreach ($items as $item) {
            $ids = array_merge($ids, $item->categories);
        }

        $ids = array_unique(array_values(array_filter($ids, function ($a) {
            return !empty($a);
        })));

        if (empty($ids)) {
            return [];
        }

        return $this->get('api.service.category')->responsify(
            $this->get('api.service.category')
                ->getListByIds($ids)['items']
        );
    }
}
