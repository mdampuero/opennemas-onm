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
use Common\Model\Entity\PromptManager;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OnmAIController extends Controller
{

    protected $helper = 'core.helper.ai';
    protected $map    = [
        "onmai_roles" => [
            [
                "name" => "string",
                "prompt" => "string",
            ],
        ],
        "onmai_tones" => [
            [
                "name" => "string",
                "description" => "string",
            ],
        ],
        "onmai_instructions" => [
            [
                "type" => "string",
                "value" => "string",
                "field" => "string"
            ],
        ]
    ];

    /**
     * Returns the list of prompts as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     */
    public function modelsSuggestedAction()
    {
        $onmai_settings = $this->get($this->helper)->getManagerSettings();
        $models         = [];
        $engines        = $this->get($this->helper)->getEngines();

        foreach (array_keys($engines) as $id) {
            $models[$id] = $this->container->get('core.helper.' . $id)->getSuggestedModels([
                'apiKey' => $onmai_settings['engines'][$id]['apiKey'] ?? ''
            ]);
        }

        return new JsonResponse($models);
    }

    /**
     * Returns the list of prompts as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     */
    public function configAction()
    {
        return new JsonResponse([
            'onmai_settings'  => $this->get($this->helper)->getManagerSettings()
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
    public function configSaveAction(Request $request)
    {
        $request = $request->request->all();
        $msg     = $this->get('core.messenger');

        $this->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->set($request);

        $msg->add(_('Prompt saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Download the current configuration as a JSON file.
     *
     * @return JsonResponse The JSON response containing the configuration.
     */
    public function configDownloadAction()
    {
        $response = new JsonResponse([
            'onmai_settings'  => $this->get($this->helper)->getManagerSettings()
        ]);

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', 'attachment; filename=onmai_settings_manager.json');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    /**
     * Upload and save a new configuration from a JSON file.
     *
     * @param Request $request The request object containing the JSON configuration.
     *
     * @return JsonResponse The response object indicating success or failure.
     */
    public function configUploadAction(Request $request)
    {
        $jsonSettings = $request->request->get('config', null);
        $msg          = $this->get('core.messenger');

        try {
            $configNew     = json_decode($jsonSettings, true);
            $configCurrent = ['onmai_settings' => $this->get($this->helper)->getManagerSettings()];

            foreach ($configNew as $key => $item) {
                if (key_exists($key, $configCurrent)) {
                    $configCurrent[$key] = $item;
                }
            }

            $this->get('orm.manager')
                ->getDataSet('Settings', 'manager')
                ->set($configCurrent);

            $msg->add(_('Prompt saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
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
    public function instancesAction(Request $request)
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

        return new JsonResponse([
            'total'   => $total,
            'results' => $instances,
            'extra'  => [
                'model'   => $helpeAI->getModelDefault(),
                'models'  => $helpeAI->getModels(),
                'service' => 'onmai'
            ],
            'oql' => $oql
        ]);
    }

    /**
     * Save instance settings.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @throws AccessDeniedException If the user does not have access to the instance.
     */
    public function instancesSaveAction(Request $request)
    {
        $request = $request->query->all();
        $msg     = $this->get('core.messenger');

        $id       = $request['id'] ?? 0;
        $model    = $request['template']['onmai_config']['model'] ?? '';
        $em       = $this->get('orm.manager');
        $instance = $em->getRepository('Instance')->find($id);

        /**
         * SAVE MANAGER
         */
        $aiConfig            = $instance->ai_config;
        $aiConfig['model']   = $model;
        $instance->ai_config = $aiConfig;
        $em->persist($instance);

        /**
         * SAVE INSTANCE SETTINGS
         */
        if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
            throw new AccessDeniedException();
        }
        $this->get('core.loader')->configureInstance($instance);
        $onmai_settings          = $em->getDataSet('Settings', 'instance')->get('onmai_settings', []);
        $onmai_settings['model'] = $model;
        $em->getDataSet('Settings', 'instance')->set('onmai_settings', $onmai_settings);

        $msg->add(_('Prompt saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
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

    /**
     * Returns the list of prompts as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_LIST')")
     */
    public function promptListAction(Request $request)
    {
        $oql          = $request->query->get('oql', '');
        $repository   = $this->get('orm.manager')->getRepository('PromptManager');
        $converter    = $this->get('orm.manager')->getConverter('PromptManager');
        $helperLocale = $this->get('core.helper.locale');

        $ids   = [];
        $total = $repository->countBy($oql);
        $items = $repository->findBy($oql);

        $items = array_map(function ($a) use ($converter, &$ids) {
            $ids[] = $a->id;
            return $converter->responsify($a);
        }, $items);

        return new JsonResponse([
            'results' => $helperLocale->translateAttributes($items, ['mode', 'field']),
            'items'   => $items,
            'total'   => $total,
        ]);
    }

    /**
     * Returns the data to create a new prompt container.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_CREATE')")
     */
    public function promptNewAction()
    {
        return new JsonResponse([
            'extra'  => $this->promptGetExtraData()
        ]);
    }

    /**
     * Creates a new prompt container from the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('PROMPT_CREATE')")
     */
    public function promptSaveAction(Request $request)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('PromptManager')->objectify($request->request->all());

        $prompt = new PromptManager($data);
        $em->persist($prompt);
        $msg->add(_('Item saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl('manager_ws_onmai_prompt_list')
        );

        return $response;
    }

    /**
     * Returns an prompt instance as JSON.
     *
     * @param integer  $id The instance id.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('PROMPT_UPDATE')")
     */
    public function promptShowAction($id)
    {
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('PromptManager');
        $item      = $em->getRepository('PromptManager')->find($id);
        $extra     = $this->promptGetExtraData();

        return new JsonResponse([
            'extra' => $extra,
            'item'  => $converter->responsify($item->getData())
        ]);
    }

    /**
     * Deletes a prompt container.
     *
     * @param integer $id The prompt id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_DELETE')")
     */
    public function promptDeleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $em->remove($em->getRepository('PromptManager')->find($id));
        $msg->add(_('Item deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected prompts.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_DELETE')")
     */
    public function promptDeleteSelectedAction(Request $request)
    {
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || empty($ids)) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em  = $this->get('orm.manager');
        $oql = sprintf('id in [%s]', implode(',', $ids));

        $prompts = $em->getRepository('PromptManager')->findBy($oql);

        $instancesToBan = [];

        $deleted = 0;
        foreach ($prompts as $container) {
            try {
                $em->remove($container);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
            foreach ($container->instances as $instanceName) {
                $instancesToBan[] = $instanceName;
            }
        }


        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s items deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates the instance information gives its id
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The instance id.
     *
     * @return Response          The response object.
     *
     * @Security("hasPermission('PROMPT_UPDATE')")
     */
    public function promptUpdateAction(Request $request, $id)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('PromptManager')->objectify($request->request->all());
        $item = $em->getRepository('PromptManager')->find($id);

        $item->merge($data);

        $em->persist($item);
        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function promptGetExtraData()
    {
        $serviceManager = getService('orm.manager')->getDataSet('Settings', 'manager');
        $settingOpenai  = [
            'onmai_roles'        => $serviceManager->get('onmai_roles') ?? [],
            'onmai_tones'        => $serviceManager->get('onmai_tones') ?? [],
            'onmai_instructions' => $serviceManager->get('onmai_instructions') ?? []
        ];
        return $settingOpenai;
    }

    /**
     * Returns a list of targets basing on the request.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_CREATE')")
     */
    public function promptAutocompleteAction(Request $request)
    {
        $target   = [];
        $query    = strtolower($request->query->get('query'));
        $security = $this->get('core.security');

        if ($security->hasPermission('MASTER')
            && (empty($query)
                || strpos(strtolower(_('All')), strtolower($query)) !== false)
        ) {
            $target[] = ['id' => 'all', 'name' => _('All')];
        }

        $oql = '';
        if (!$security->hasPermission('MASTER')
            && $security->hasPermission('PARTNER')
        ) {
            $oql = sprintf('owner_id = "%s" ', $this->get('core.user')->id);
        }

        if (!empty($query)) {
            if (!empty($oql)) {
                $oql .= 'and ';
            }

            $oql .= '(internal_name ~ "%s" or name ~ "%s" or domains ~ "%s") ';
            $oql  = sprintf($oql, $query, $query, $query);
        }

        $oql .= 'order by internal_name asc limit 10';

        $instances = $this->get('orm.manager')->getRepository('instance')
            ->findBy($oql);

        foreach ($instances as $instance) {
            $target[] = [
                'id'      => $instance->internal_name,
                'name'    => $instance->internal_name,
            ];
        }

        return new JsonResponse(['target' => $target]);
    }

    /**
     * Get prompt Setting
     *
     * @return JsonResponse The response object.
     */
    public function promptConfigAction()
    {
        $serviceManager = getService('orm.manager')->getDataSet('Settings', 'manager');
        $settingOpenai  = [
            'onmai_roles'        => $serviceManager->get('onmai_roles') ?? [],
            'onmai_tones'        => $serviceManager->get('onmai_tones') ?? [],
            'onmai_instructions' => $serviceManager->get('onmai_instructions') ?? []
        ];

        $response = new Response();
        $response->setContent(json_encode($settingOpenai));

        return $response;
    }

    /**
     * Save prompt settings
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     */
    public function promptConfigSaveAction(Request $request)
    {
        $request = $request->request->all();

        $msg = $this->get('core.messenger');

        $this->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->set($request);

        $msg->add(_('Prompt saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Import valid JSON as a theme settings
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_LIST')")
     */
    public function promptConfigDownloadAction()
    {
        $serviceManager = getService('orm.manager')->getDataSet('Settings', 'manager');
        $repository     = $this->get('orm.manager')->getRepository('PromptManager');
        $converter      = $this->get('orm.manager')->getConverter('PromptManager');

        $items = array_map(function ($a) use ($converter) {
            $item = $converter->responsify($a);
            unset($item['id']);
            return $item;
        }, $repository->findBy(null, null, 1000));

        $settingOpenai = [
            'onmai_roles'        => $serviceManager->get('onmai_roles') ?? [],
            'onmai_tones'        => $serviceManager->get('onmai_tones') ?? [],
            'onmai_instructions' => $serviceManager->get('onmai_instructions') ?? [],
            'prompts'             => $items
        ];

        $response = new JsonResponse($settingOpenai);

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', 'attachment; filename=onmai_prompt_settings_manager.json');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    /**
     * Import valid JSON as a theme settings
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('PROMPT_CREATE')")
     */
    public function promptConfigUploadAction(Request $request)
    {
        $em             = $this->get('orm.manager');
        $repository     = $em->getRepository('PromptManager');
        $serviceManager = getService('orm.manager')->getDataSet('Settings', 'manager');
        $helperAI       = $this->container->get('core.helper.ai');
        $jsonSettings   = $request->request->get('config', null);
        $msg            = $this->get('core.messenger');

        $promptConfigCurrent = [
            'onmai_roles'        => $serviceManager->get('onmai_roles') ?? [],
            'onmai_tones'        => $serviceManager->get('onmai_tones') ?? [],
            'onmai_instructions' => $serviceManager->get('onmai_instructions') ?? []
        ];

        $promptsCurrent = $repository->findBy(null, null, 1000);

        try {
            $promptConfigNew = json_decode($jsonSettings, true);
            $promptsNew      = $promptConfigNew["prompts"];
            unset($promptConfigNew["prompts"]);

            if (!$helperAI->validateJsonStructure($promptConfigNew, $this->map)) {
                throw new Exception("INVALIDO");
            }

            foreach ($promptConfigNew as $key => $item) {
                if (key_exists($key, $promptConfigCurrent)) {
                    $promptConfigCurrent[$key] = $item;
                }
            }

            $this->get('orm.manager')->getDataSet('Settings', 'manager')
                ->set($promptConfigNew);

            foreach ($promptsCurrent as $prompt) {
                $em->remove($prompt);
            }

            foreach ($promptsNew as $p) {
                $prompt = new PromptManager($em->getConverter('PromptManager')->objectify($p));
                $em->persist($prompt);
            }

            $msg->add(_('Prompt saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
