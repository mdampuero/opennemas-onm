<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * Lists and displays newsletters.
 */
class NewsletterTemplateController extends Controller
{
    /**
     * Returns the data to create a new newsletter.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function createAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Deletes an newsletter.
     *
     * @param integer $id The newsletter id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function deleteAction($id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.newsletter')->deleteItem($id);
        $msg->add(_('Item deleted successfully'), 'success');

        // TODO: Remove when deprecated old newsletter_repository
        $this->get('core.dispatcher')->dispatch('newsletter.update', ['id' => $id]);

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected newsletters.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $deleted = $this->get('api.service.newsletter')->deleteList($ids);

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s items deleted successfully'), $deleted),
                'success'
            );
        }

        if ($deleted !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be deleted successfully'),
                count($ids) - $deleted
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of extra data.
     *
     * @param array $item The list of items.
     *
     * @return array Array of template parameters.
     */
    private function getExtraData($items = null)
    {
        $recipients = [];

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([
                'newsletter_maillist',
                'newsletter_subscriptionType',
                'actOn.marketingLists',
            ]);


        $ss       = $this->get('api.service.subscription');
        $ssb      = $this->get('api.service.subscriber');
        $response = $ss->getList('');

        $lists = array_filter($response['items'], function ($list) {
            return in_array(224, $list->privileges);
        });

        $recipients = [];
        foreach ($lists as $list) {
            $recipients[] = [
                'type' => 'list',
                'name' => $list->name,
                'id'   => $list->pk_user_group,
                'subscribers' => $ssb->getList(
                    '(user_group_id = "' . $list->pk_user_group
                    . '" and status != 0)'
                )['total']
            ];
        }

        if (!empty($settings['newsletter_maillist'])) {
            $recipients[] = [
                'type' => 'external',
                'name' => $settings['newsletter_maillist']['email'],
                'email' => $settings['newsletter_maillist']['email'],
            ];
        }

        if (empty($settings['actOn.marketingLists'])) {
            $settings['actOn.marketingLists'] = [];
        }

        foreach ($settings['actOn.marketingLists'] as $list) {
            $recipients[] = [
                'uuid' => uniqid(),
                'type' => 'acton',
                'name' => $list['name'],
                'id'   => $list['id'],
            ];
        }

        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[] = sprintf("%02d:00", $i);
        }

        $days = [
            [ "id" => 1, "name" => _("Monday") ],
            [ "id" => 2, "name" => _("Tuesday") ],
            [ "id" => 3, "name" => _("Wednesday") ],
            [ "id" => 4, "name" => _("Thursday") ],
            [ "id" => 5, "name" => _("Friday") ],
            [ "id" => 6, "name" => _("Saturday") ],
            [ "id" => 7, "name" => _("Sunday") ],
        ];

        return [
            'newsletter_handler' => $settings['newsletter_subscriptionType'],
            'recipients'         => $recipients,
            'hours'              => $hours,
            'days'               => $days,
        ];
    }

    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $ns  = $this->get('api.service.newsletter');
        $oql = $request->query->get('oql', '');

        $response = $ns->getList($oql);

        return new JsonResponse([
            'items' => $ns->responsify($response['items']),
            'total' => $response['total'],
        ]);
    }

    /**
     * Updates some properties for an newsletter.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function patchAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.newsletter')
            ->patchItem($id, $request->request->all());

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates some properties for a list of newsletters.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        $updated = $this->get('api.service.newsletter')
            ->patchList($ids, $params);

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s items updated successfully'), $updated),
                'success'
            );
        }

        if ($updated !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be updated successfully'),
                count($ids) - $updated
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Saves a new newsletter.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function saveAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $newsletter = $this->get('api.service.newsletter')
            ->createItem($request->request->all());
        $msg->add(_('Item saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'api_v1_backend_newsletter_show',
                [ 'id' => $newsletter->id ]
            )
        );

        return $response;
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

        $settings['actOn.marketingLists'] = $settings['actOn_marketingLists'];
        unset($settings['actOn_marketingLists']);

        try {
            $this->get('orm.manager')->getDataSet('Settings', 'instance')
                ->set($settings);

            $msg->add(_('Settings saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns an newsletter.
     *
     * @param integer $id The group id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function showAction($id)
    {
        $ns   = $this->get('api.service.newsletter');
        $item = $ns->getItem($id);

        return new JsonResponse([
            'item'  => $ns->responsify($item),
            'extra' => $this->getExtraData(),
        ]);
    }

    /**
     * Updates the newsletter information given its id and the new information.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function updateAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.newsletter')
            ->updateItem($id, $request->request->all());

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
