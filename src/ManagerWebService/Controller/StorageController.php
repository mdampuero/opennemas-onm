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

class StorageController extends Controller
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
    public function configAction()
    {
        $storage_settings = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->get('storage_settings', []);
        return new JsonResponse([
            'storage_settings'  => $storage_settings
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
            'storage_settings'  => $this->get($this->helper)->getManagerSettings()
        ]);

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', 'attachment; filename=storage_settings_manager.json');
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
            $configCurrent = ['storage_settings' => $this->get($this->helper)->getManagerSettings()];

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
}
