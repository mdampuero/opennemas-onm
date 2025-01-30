<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Common\Core\Annotation\Security;
use Exception;

class OnmAIController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.openai';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'list'   => 'ADMIN',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.ai';

    /**
     * {@inheritdoc}
     */
    protected $helper = 'core.helper.ai';

    /**
     * Get the Web Push notifications configuration
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        // Checks if it is a demo listing or a real one
        if (!$this->get('core.security')->hasExtension($this->extension)) {
            return;
        }

        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        return new JsonResponse([
            'onmai_config' => $this->get($this->helper)->getInstanceSettings(),
            'models' => $this->get($this->helper)->getModels(),
            'model'  => $this->get($this->helper)->getManagerSettings()['model'],
            'engines' => $this->get($this->helper)->getEngines(),
        ]);
    }

    public function saveConfigAction(Request $request)
    {
        $this->checkSecurity($this->extension, 'ADMIN');

        $msg    = $this->get('core.messenger');
        $config = $request->request->all();

        try {
            $this->get($this->helper)->setInstanceSettings($config['onmai_config']);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (AccessDeniedException $e) {
            $msg->add(_('Webpush Module is not activated'), 'info');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    public function generateAction(Request $request)
    {
        if (!$this->get('core.security')->hasExtension($this->extension)) {
            return new JsonResponse(['error' => 'Access denied.'], JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            $messages                   = [];
            $messages["input"]          = $request->request->get('input');
            $messages["roleSelected"]   = $request->request->get('roleSelected');
            $messages["toneSelected"]   = $request->request->get('toneSelected');
            $messages["promptSelected"] = $request->request->get('promptSelected');
            $messages["promptInput"]    = $request->request->get('promptInput');
            $messages["locale"]         = $request->request->get('locale');

            $response = $this->get($this->helper)->sendMessage($messages);

            if (isset($response['error'])) {
                return new JsonResponse(['error' => $response['error']], JsonResponse::HTTP_REQUEST_TIMEOUT);
            }

            return new JsonResponse($response);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred: ' .
                $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUsageAction(Request $request)
    {
        $year  = $request->query->get('year', date('Y'));
        $month = $request->query->get('month', date('m'));
        return new JsonResponse($this->get($this->helper)->getStats($month, $year));
    }
}
