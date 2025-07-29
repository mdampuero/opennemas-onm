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
        $content    = $request->request->get('csv_file', null);
        $newsletter = $request->request->get('newsletter', null);

        if (empty($content)) {
            return new JsonResponse(
                [ _('Unable to find the file provided') ],
                400
            );
        }

        $lines = explode("\n", $content);
        array_shift($lines); // Remove Header

        // TODO: Hardcoded maxLines, maybe new setting on manager for this.
        $maxLines       = 1000;
        $processedLines = 0;

        $userGroups = is_array($newsletter)
            ? array_map(
                function ($id) {
                    return ['user_group_id' => $id, 'status' => 1];
                },
                $newsletter
            )
            : [['user_group_id' => $newsletter, 'status' => 1]];

        foreach ($lines as $line) {
            if ($processedLines >= $maxLines) {
                break;
            }

            $line = trim($line);
            if (!$line) {
                continue;
            }

            $columns    = explode(',', $line);
            $email      = trim($columns[0]);
            $name       = empty($columns[1]) ? $email : trim($columns[1]);
            $signupDate = empty($columns[2]) ? date('Y-m-d') : $columns[2];

            // Verify if the email is a valid.
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $data = [
                'email'         => $email,
                'name'          => $name,
                'register_date' => $signupDate,
                'activated'     => 1,
                'type'          => 1,
                'user_groups'   => $userGroups
            ];

            try {
                $this->get('api.service.subscriber')->createSubscriber($data);
                $processedLines++;
            } catch (\Exception $e) {
                continue;
            }
        }

        return new JsonResponse(['messages' => [[
            'id'      => '200',
            'type'    => 'success',
            'message' => sprintf(_('Import successfully (up to %d lines processed)'), $processedLines)
        ]]]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getItemId($item)
    {
        return $item->pk_user_group;
    }
}
