<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Core\Annotation\Security;

class WebPushNotificationsController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.webpush_notifications';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'list'   => 'WEBPUSH_ADMIN',
    ];

    protected $module = 'webpush_notifications';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.webpush_notifications';

            /**
     * {@inheritdoc}
     */
    protected $helper = 'core.helper.webpush_notifications';

        /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     *
     * @Security("hasExtension('es.openhost.module.webpush_notifications')
     *     and hasPermission('WEBPUSH_ADMIN')")
     */
    // public function getListAction(Request $request)
    // {
    //     $wps = $this->get('api.service.webpush_notifications');
    //     $oql = $request->query->get('oql', '');

    //     $response = $wps->getList($oql);

    //     return new JsonResponse([
    //         'items' => $wps->responsify($response['items']),
    //         'total' => $response['total'],
    //     ]);
    // }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        return [
            'years'            => $this->getItemYears()
        ];
    }

    /**
     * Returns the list of settings for CONTENT_SUBSCRIPTIONS extension.
     *
     * @return JsonResponse The response object.
     */
    public function listSettingsAction()
    {
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([
                'newsletter_manual',
                'newsletter_maillist',
                'newsletter_subscriptionType',
                'actOn.marketingLists',
                'actOn.formPage',
                'actOn.headerId',
                'actOn.footerId',
            ]);

        return new JsonResponse([ 'settings' => $settings ]);
    }

    /**
     * Saves settings for CONTENT_SUBSCRIPTIONS extension.
     *
     * @param Request $request The request object.
     *
     * @return JsonResposne The response object.
     */
    public function saveSettingsAction(Request $request)
    {
        $msg      = $this->get('core.messenger');
        $settings = $request->request->all();

        // Damned PHP with its weird behaviour
        // http://php.net/manual/en/language.variables.external.php
        // Dots and spaces in variable names are converted to underscores.
        // For example <input name="a.b" /> becomes $_REQUEST["a_b"].
        $settings['actOn.marketingLists'] = $settings['actOn_marketingLists'];
        unset($settings['actOn_marketingLists']);
        $settings['actOn.headerId'] = $settings['actOn_headerId'];
        unset($settings['actOn_headerId']);
        $settings['actOn.footerId'] = $settings['actOn_footerId'];
        unset($settings['actOn_footerId']);
        $settings['actOn.formPage'] = $settings['actOn_formPage'];
        unset($settings['actOn_formPage']);

        try {
            $this->get('orm.manager')->getDataSet('Settings', 'instance')
                ->set($settings);

            $msg->add(_('Settings saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
