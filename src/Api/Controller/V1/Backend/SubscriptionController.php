<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays, saves, modifies and removes subscriptions.
 */
class SubscriptionController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'CONTENT_SUBSCRIPTIONS';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_subscription_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'SUBSCRIPTION_CREATE',
        'delete' => 'SUBSCRIPTION_DELETE',
        'list'   => 'SUBSCRIPTION_ADMIN',
        'patch'  => 'SUBSCRIPTION_UPDATE',
        'save'   => 'SUBSCRIPTION_CREATE',
        'show'   => 'SUBSCRIPTION_UPDATE',
        'update' => 'SUBSCRIPTION_UPDATE',
    ];

    protected $module = 'subscription';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.subscription';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        return [
            'modules' => $this->get('core.helper.permission')->getByModule(),
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function importAction(Request $request)
    {
        // TODO: Import one action
        $msg        = $this->get('core.messenger');
        $service    = $this->get($this->service);
        $content    = $request->request->get('csv_file', null);
        $newsletter = $request->request->get('newsletter', null);

        if (empty($content)) {
            return new JsonResponse(
                [ _('No file provided') ],
                400
            );
        }

        $lines = explode("\n", $content);
        array_shift($lines); // Remove Header

        foreach ($lines as $line) {
            $line = trim($line);

            if (!$line) {
                continue;
            }

            $columns    = explode(',', $line);
            $email      = trim($columns[0]);
            $name       = isset($columns[1]) ? trim($columns[1]) : $email;
            $signupDate = isset($columns[2]) ? trim($columns[2]) : date('Y-m-d');

            $newsletter = $service->getItem(7);

            $data = [
                'email'         => $email,
                'name'          => $name,
                'register_date' => $signupDate,
                'activated'     => 1,
                'type'          => 1,
               // 'user_groups'   => $userGroups
            ];

            try {
                // TODO: Service
            } catch (\Exception $e) {
                continue;
            }
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function importListAction()
    {
        // TODO: Import List Action
    }



    /**
     * {@inheritdoc}
     */
    protected function getItemId($item)
    {
        return $item->pk_user_group;
    }
}
