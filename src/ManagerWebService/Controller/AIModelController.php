<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AIModelController extends Controller
{
    protected $helper = 'core.helper.openai';

    /**
     * Returns the list of prompts as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_LIST')")
     */
    public function listAction()
    {
        $serviceManager = getService('orm.manager')->getDataSet('Settings', 'manager');
        $models = $this->get($this->helper)->getModelsFromApi();
        return new JsonResponse([
            'items'           => $models,
            'openai_models'   => $serviceManager->get('openai_models') ?? [],
            'openai_settings' => $serviceManager->get('openai_settings') ?? [],
            'total'           => count($models)
        ]);
    }

    /**
     * Save prompt settings
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     */
    public function saveAction(Request $request)
    {
        $request = $request->request->all();
        $msg     = $this->get('core.messenger');

        $this->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->set($request);

        $msg->add(_('Prompt saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
