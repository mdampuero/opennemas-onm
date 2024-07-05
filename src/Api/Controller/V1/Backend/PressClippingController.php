<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PressClippingController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.pressclipping';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        // TODO: Falta especificar
    ];

    /**
     * {@inheritdoc}
     */
    protected $module = 'pressclipping';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.pressclipping';

    /**
     * {@inheritdoc}
     */
    protected $helper = 'core.helper.pressclipping';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData()
    {
        // TODO: Desarrollo futuro
    }

    /**
     * Tries to connect to the server.
     *
     * @return JsonResponse The response object.
     */
    public function checkServerAction()
    {
        $msg = $this->get('core.messenger');
        try {
            $service       = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')->get('pressclipping_service');
            $pressclipping = $this->get(sprintf('external.pressclipping.factory.%s', $service));
            $endpoint      = $pressclipping->getEndpoint('test_connection');

            $endpoint->testConnection();
            $msg->add(_('Server connection success!'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to connect to the server'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
