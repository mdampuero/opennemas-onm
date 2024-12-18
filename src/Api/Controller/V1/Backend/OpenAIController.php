<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Common\Core\Annotation\Security;
use Exception;

class OpenAIController extends ApiController
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

    protected $module = 'openai';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.ai';

    /**
     * {@inheritdoc}
     */
    protected $helper = 'core.helper.openai';

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

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_config', []);

        $serviceName = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_service', 'custom');

        $credentials = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_credentials', []);

        if (empty($settings)) {
            //TODO: Get config from manager
            $settings = $this->get($this->helper)->getDafaultParams();
        }

        foreach ($settings as $key => $value) {
            if (is_numeric($value)) {
                $settings[$key] = (float) $value;
            }
        }

        return new JsonResponse([
            'openai_service'          => $serviceName,
            'openai_credentials'      => $credentials,
            'openai_config'           => $settings,
            'openai_roles'            => $this->get($this->helper)->getRoles(),
            'openai_tones'            => $this->get($this->helper)->getTones(),
            'openai_models'           => $this->get($this->helper)->getModels(),
        ]);
    }

    /**
     * Saves configuration for Web Push notifications.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $this->checkSecurity($this->extension, 'ADMIN');

        $msg    = $this->get('core.messenger');
        $config = $request->request->all();

        $config['openai_roles'] = $this->get($this->helper)->deleteFlagReadOnly($config['openai_roles']);
        $config['openai_tones'] = $this->get($this->helper)->deleteFlagReadOnly($config['openai_tones']);

        try {
            $this->get('orm.manager')->getDataSet('Settings')->set($config);
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

            if ($response['tokens'] ?? false) {
                $this->get($this->helper)->saveTokens($response['tokens']);
            }

            return new JsonResponse($response);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred: ' .
                $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkApiKeyAction(Request $request)
    {
        try {
            $this->get($this->helper)->checkApiKey($request->request->get('apiKey', null));
            return new JsonResponse(['OK']);
        } catch (\Exception $e) {
            return new JsonResponse([$e->getMessage()], JsonResponse::HTTP_FORBIDDEN);
        }
    }

    public function getPricingAction()
    {
        $tokens  = $this->get($this->helper)->getTokensMonthly();
        $pricing = $this->get($this->helper)->getPricing();
        $money   = $this->get($this->helper)->getSpentMoney();


        $agrupatedTokens = [];

        foreach ($tokens['items'] as $item) {
            $model            = $item->getData()['params']['model'];
            $promptTokens     = $item->getData()['tokens']['prompt_tokens'];
            $completionTokens = $item->getData()['tokens']['completion_tokens'];
            $totalTokens      = $item->getData()['tokens']['total_tokens'];

            if (isset($agrupatedTokens[$model])) {
                $agrupatedTokens[$model]['prompt_tokens']     += $promptTokens;
                $agrupatedTokens[$model]['completion_tokens'] += $completionTokens;
                $agrupatedTokens[$model]['total_tokens']      += $totalTokens;
            } else {
                $agrupatedTokens[$model] = [
                    'prompt_tokens' => $promptTokens,
                    'completion_tokens' => $completionTokens,
                    'total_tokens' => $totalTokens
                ];
            }
        }

        $data = [
            'tokens' => $agrupatedTokens,
            'prices' => $pricing,
            'total' => $money
        ];

        return new JsonResponse($data);
    }

    /**
     * Import valid JSON as a theme settings
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('MASTER')
     *     and hasPermission('MASTER')")
     */
    public function downloadConfigAction()
    {
        $openaiConfig = $this->container->get($this->helper)->getConfigAll();
        $response     = new JsonResponse($openaiConfig);

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', 'attachment; filename=openai_settings.json');
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
     * @Security("hasExtension('MASTER')
     *     and hasPermission('MASTER')")
     */
    public function uploadConfigAction(Request $request)
    {
        $helperInstance = $this->container->get($this->helper);
        $jsonSettings   = $request->request->get('openai_config', null);
        $msg            = $this->get('core.messenger');

        try {
            $openaiConfigNew = json_decode($jsonSettings, true);

            if (!$helperInstance->validateJsonStructure($openaiConfigNew, $helperInstance->map)) {
                throw new Exception("INVALIDO");
            }
            $config = $helperInstance->replaceConfig($openaiConfigNew);
            $this->get('orm.manager')->getDataSet('Settings')->set($config);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
