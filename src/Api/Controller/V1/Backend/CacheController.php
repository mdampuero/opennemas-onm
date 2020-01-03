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

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CacheController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'ALBUM_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_album_get_item';

    /**
     * {@inheritdoc}
     */
    protected $services = [
        'redis'   => 'api.service.redis',
        'smarty'  => 'api.service.smarty',
        'varnish' => 'api.service.varnish'
    ];

    /**
     * {@inheritdoc}
     */
    public function deleteItemAction($id, $service = null)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('delete'));

        $msg = $this->get('core.messenger');

        try {
            $this->get($this->services[$service])->deleteItem($id);

            if ($this->get($this->services[$service])->isPattern($id)) {
                $msg->add(_('Running cleanup in background'), 'info');
            } else {
                $msg->add(_('Item deleted successfully'), 'success');
            }
        } catch (\Exception $e) {
            $msg->add($e->getMessage(), 'error', $e->getCode());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteListAction(Request $request, $service = null)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('delete'));

        $msg = $this->get('core.messenger');

        try {
            $this->get($this->services[$service])->deleteList([]);
            $msg->add(_('Running cleanup in background'), 'info');
        } catch (\Exception $e) {
            $msg->add($e->getMessage(), 'error', $e->getCode());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function getItemAction($id, $service = null)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('delete'));

        $item = $this->get($this->services[$service])->getItem($id);

        return new JsonResponse([ 'item' => json_encode($item) ]);
    }
}
