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
    protected $helper = 'core.helper.ai';

    /**
     * Returns the list of prompts as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_LIST')")
     */
    public function configAction()
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
     * Returns the list of prompts as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_LIST')")
     */
    public function listAction(Request $request)
    {
        $oql     = $request->query->get('oql', '');
        $helpeAI = $this->get('core.helper.ai');

        // Fix OQL for Non-MASTER users
        if (!$this->get('core.security')->hasPermission('MASTER')) {
            $condition = sprintf('owner_id = %s ', $this->get('core.user')->id);

            $oql = $this->get('orm.oql.fixer')->fix($oql)
                ->addCondition($condition)->getOql();
        }
        ///$oql = 'ai_config != "" and' . $oql;

        $repository = $this->get('orm.manager')->getRepository('Instance');
        $converter  = $this->get('orm.manager')->getConverter('Instance');

        $instances = $repository->findBy($oql);
        $total     = $repository->countBy($oql);

        $instances = array_map(function ($a) use ($converter) {
            return $converter->responsify($a->getData());
        }, $instances);

        $serviceManager = getService('orm.manager')->getDataSet('Settings', 'manager');

        $extra          = $serviceManager->get('openai_settings') ?? [];
        $extra['model'] = $helpeAI->getModelIdDefault();

        return new JsonResponse([
            'total'   => $total,
            'results' => $instances,
            'extra'  => $extra,
            'oql' => $oql
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
