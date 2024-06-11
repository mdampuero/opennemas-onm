<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    protected $service = 'api.service.openai';

            /**
     * {@inheritdoc}
     */
    protected $helper = 'core.helper.openai';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        // $response = [
        //     'years' => $this->getItemYears(),
        // ];

        // if (empty($items)) {
        //     return $response;
        // }

        // if (!is_array($items)) {
        //     $items = [ $items ];
        // }

        // $photos = [];

        // $ids = array_filter(array_map(function ($notification) {
        //     return $notification->image;
        // }, $items), function ($photo) {
        //         return !empty($photo);
        // });

        // try {
        //     $photos = $this->get('api.service.content')->getListByIds($ids)['items'];
        //     $photos = $this->get('data.manager.filter')
        //         ->set($photos)
        //         ->filter('mapify', [ 'key' => 'pk_content' ])
        //         ->get();

        //     $photos = [ 'photos' => $this->get('api.service.content')->responsify($photos) ];
        //     return array_merge($response, $photos);
        // } catch (GetItemException $e) {
        // }
        // $photos = [ 'photos' => $photos, ];

        // return array_merge($response, $photos);
    }

        /**
     * Returns a list of items.
     *
     * @param Request $request The request object.
     *
     * @return array The list of items and all extra information.
     */
    // public function getListAction(Request $request)
    // {
    //     // Checks if it is a demo listing or a real one
    //     if (!$this->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')) {
    //         $demo_response = [
    //             'items' => [
    //                 [
    //                     'impressions' => 5476,
    //                     'clicks' => 1327,
    //                     'closed' => 3864,
    //                     'send_date' => '2001-12-03 21:30:12',
    //                     'title' => 'Lorem Ipsum Dolor',
    //                     'status' => 1,
    //                     'image' => null,
    //                     'transaction_id' => '23854aer',
    //                     'send_count' => 6438
    //                 ],
    //                 [
    //                     'impressions' => 0,
    //                     'clicks' => 0,
    //                     'closed' => 0,
    //                     'send_date' => '2024-01-01 08:00:00',
    //                     'title' => 'Sit Amet Consectetur',
    //                     'status' => 0,
    //                     'image' => null,
    //                     'transaction_id' => '23872ser',
    //                     'send_count' => 0
    //                 ],
    //                 [
    //                     'impressions' => 6000,
    //                     'clicks' => 3000,
    //                     'closed' => 2500,
    //                     'send_date' => '2020-01-01 08:00:00',
    //                     'title' => 'Adipiscing Elit',
    //                     'status' => 1,
    //                     'image' => null,
    //                     'transaction_id' => '25879adr',
    //                     'send_count' => 6235
    //                 ],
    //                 [
    //                     'impressions' => 0,
    //                     'clicks' => 0,
    //                     'closed' => 0,
    //                     'send_date' => '2024-01-01 08:00:00',
    //                     'title' => 'Sed Do Eiusmod',
    //                     'status' => 0,
    //                     'image' => null,
    //                     'transaction_id' => '6879der',
    //                     'send_count' => 0
    //                 ],
    //                 [
    //                     'impressions' => 6000,
    //                     'clicks' => 3000,
    //                     'closed' => 2500,
    //                     'send_date' => '2020-01-010 08:00:00',
    //                     'title' => 'Tempor Incididunt Ut Labore',
    //                     'status' => 1,
    //                     'image' => null,
    //                     'transaction_id' => '23779aew',
    //                     'send_count' => 7422
    //                 ],
    //                 [
    //                     'impressions' => 0,
    //                     'clicks' => 0,
    //                     'closed' => 0,
    //                     'send_date' => '2024-01-01 08:00:00',
    //                     'title' => 'Ut Enim Ad Minim Veniam',
    //                     'status' => 0,
    //                     'image' => null,
    //                     'transaction_id' => '63879asr',
    //                     'send_count' => 0
    //                 ],
    //                 [
    //                     'impressions' => 0,
    //                     'clicks' => 0,
    //                     'closed' => 0,
    //                     'send_date' => '2024-01-01 08:00:00',
    //                     'title' => 'Laboris Nisi Ut Aliquip',
    //                     'status' => 0,
    //                     'image' => null,
    //                     'transaction_id' => '43879ser',
    //                     'send_count' => 0
    //                 ],
    //                 [
    //                     'impressions' => 6000,
    //                     'clicks' => 3000,
    //                     'closed' => 2500,
    //                     'send_date' => '2020-01-01 08:00:00',
    //                     'title' => 'Titulo 1',
    //                     'status' => 1,
    //                     'image' => null,
    //                     'transaction_id' => '63879aes',
    //                     'send_count' => 6235
    //                 ],
    //                 [
    //                     'impressions' => 6000,
    //                     'clicks' => 3000,
    //                     'closed' => 2500,
    //                     'send_date' => '2020-01-01 08:00:00',
    //                     'title' => 'Ex Ea Commodo Consequat',
    //                     'status' => 1,
    //                     'image' => null,
    //                     'transaction_id' => '53879adr'
    //                 ],
    //                 [
    //                     'impressions' => 6000,
    //                     'clicks' => 3000,
    //                     'closed' => 2500,
    //                     'send_date' => '2020-01-01 08:00:00',
    //                     'title' => 'Duis Aute Irure Dolor In Reprehenderit',
    //                     'status' => 1,
    //                     'image' => null,
    //                     'transaction_id' => '76879wes',
    //                     'send_count' => 6235
    //                 ],
    //             ]
    //         ];

    //         return [
    //             'items'      => $demo_response['items'],
    //             'total'      => 10,
    //         ];
    //     } else {
    //         $this->checkSecurity($this->extension, $this->getActionPermission('list'));

    //         $us  = $this->get($this->service);
    //         $oql = $request->query->get('oql', '');

    //         $response = $us->getList($oql);
    //     }

    //     return [
    //         'items'      => $us->responsify($response['items']),
    //         'total'      => $response['total'],
    //         'extra'      => $this->getExtraData($response['items']),
    //         'o-filename' => $this->filename,
    //     ];
    // }

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
        // dump($settings);
        // dump($credentials);
        // dump($serviceName);
        // die();
        return new JsonResponse([
            'openai_service'     => $serviceName,
            'openai_credentials' => $credentials,
            'openai_config'      => $settings,
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
        $message = [];

        $message['system'] = $request->request->get('system_prompt', '');
        $message['user']   = $request->request->get('user_prompt', '');

        $response = $this->get($this->helper)->sendMessage($message);

        if (array_key_exists('tokens', $response) && !empty($response['tokens'])) {
            $this->get($this->helper)->saveTokens($response['tokens']);
        }

        return new JsonResponse($response);
    }

    public function getPricingAction()
    {
        $tokens  = $this->get($this->helper)->getTokens();
        $pricing = $this->get($this->helper)->getPricing();
        $money   = $this->get($this->helper)->getSpentMoney();

        $data = [
            'tokens' => $tokens,
            'prices' => $pricing,
            'total' => $money
        ];

        return new JsonResponse($data);
    }
}
