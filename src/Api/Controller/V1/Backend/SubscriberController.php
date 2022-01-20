<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Displays, saves, modifies and removes subscribers.
 */
class SubscriberController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'CONTENT_SUBSCRIPTIONS';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_subscriber_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'SUBSCRIBER_CREATE',
        'delete' => 'SUBSCRIBER_DELETE',
        'list'   => 'SUBSCRIBER_ADMIN',
        'patch'  => 'SUBSCRIBER_UPDATE',
        'save'   => 'SUBSCRIBER_CREATE',
        'show'   => 'SUBSCRIBER_UPDATE',
        'update' => 'SUBSCRIBER_UPDATE',
    ];

    protected $propertyName = 'article';

    protected $translations = [
        [
            'name' => 'subscriptions',
            'title' => 'Lists'
        ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.subscriber';

    /**
     * Returns the list of settings for CONTENT_SUBSCRIPTIONS extension.
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('user_settings', []);

        return new JsonResponse([ 'settings' => $settings ]);
    }

    /**
     * Downloads the list of subscribers with metas.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function getReportAction()
    {
        // Get information
        $items = $this->get('api.service.subscriber')->getReport();

        $settings      = $this->getSettings();
        $extraFields   = $settings['fields'];
        $subscriptions = $this->getSubscriptions();

        // Prepare contents for CSV
        $headers = [
            _('Email'),
            _('Name'),
            _('Activated'),
            _('Registration date'),
            _('Subscriptions')
        ];

        foreach ($extraFields as $extraField) {
            $headers[] = $extraField['title'];
        }

        $data = [];
        foreach ($items as $user) {
            $userGroups = [];
            foreach ($user['user_groups'] as $value) {
                if (array_key_exists($value, $subscriptions)) {
                    $userGroups[] = $subscriptions[$value]['name'];
                }
            }

            $userInfo = [
                $user['email'],
                $user['name'],
                ($user['activated']) ? _('Yes') : _('No'),
                $user['register_date'] ?? '',
                implode(',', $userGroups),
            ];

            foreach ($extraFields as $extraField) {
                if (array_key_exists($extraField['name'], $user)) {
                    $userInfo[] = $user[$extraField['name']];
                }
            }

            $data[] = $userInfo;
        }

        // Prepare the CSV content
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->setDelimiter(';');
        $writer->setInputEncoding('utf-8');
        $writer->insertOne($headers);
        $writer->insertAll($data);

        $response = new Response($writer, 200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Subscribers list Export');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename=subscribers-' . date('Y-m-d') . '.csv'
        );
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Saves settings for CONTENT_SUBSCRIPTIONS extension.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $msg      = $this->get('core.messenger');
        $settings = $request->request->all();

        if (!is_array($settings) ||
            !array_key_exists('fields', $settings) ||
            !is_array($settings['fields'])
        ) {
            $settings = ['fields' => []];
        }

        try {
            $this->get('orm.manager')->getDataSet('Settings', 'instance')
                ->set('user_settings', $settings);

            $msg->add(_('Settings saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        return [
            'client'        => $this->getClient(),
            'countries'     => $this->get('core.geo')->getCountries(),
            'photos'        => $this->getPhotos($items),
            'settings'      => $this->getSettings(),
            'subscriptions' => $this->getSubscriptions(),
            'formSettings'  => [
                'name'             => $this->propertyName,
                'expansibleFields' => $this->translateFields($this->translations)
            ]
        ];
    }

    /**
     * Returns the list of photos for all items in the list.
     *
     * @param mixed $items The item or the list of items.
     *
     * @return array The list of photos.
     */
    protected function getPhotos($items = null) : array
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = array_filter(array_map(function ($a) {
            return [ 'photo', $a->avatar_img_id ];
        }, $items), function ($a) {
            return !empty($a[1]);
        });

        $photos = $this->get('entity_repository')->findMulti($ids);

        return $this->get('data.manager.filter')
            ->set($photos)
            ->filter('mapify', [ 'key' => 'pk_content' ])
            ->get();
    }

    /**
     * Returns the list of extra fields for subscribers.
     *
     * @return array The list of extra fields.
     */
    protected function getSettings() : array
    {
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('user_settings', []);

        if (!is_array($settings)) {
            $settings = [ 'fields' => [] ];
        }

        if (!array_key_exists('fields', $settings)
            || !is_array($settings['fields'])
        ) {
            $settings['fields'] = [];
        }

        return $settings;
    }

    /**
     * Returns the list of subscriptions.
     *
     * @return array The list of subscriptions.
     */
    protected function getSubscriptions() : array
    {
        $ss = $this->get('api.service.subscription');

        $subscriptions = $ss->getList()['items'];

        return $ss->responsify($this->get('data.manager.filter')
            ->set($subscriptions)
            ->filter('mapify', [ 'key' => 'pk_user_group'])
            ->get());
    }
}
