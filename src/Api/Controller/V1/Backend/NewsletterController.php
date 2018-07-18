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
class NewsletterController extends Controller
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

        $extra = [
            'days' => [
                _("Monday"),
                _("Tuesday"),
                _("Wednesday"),
                _("Thursday"),
                _("Friday"),
                _("Saturday"),
                _("Sunday"),
            ],
        ];

        return new JsonResponse([
            'items' => \Onm\StringUtils::convertToUtf8($ns->responsify($response['items'])),
            'total' => $response['total'],
            'extra' => $extra,
        ]);
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
            'item'  => \Onm\StringUtils::convertToUtf8($ns->responsify($item)),
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
        if ($id != $this->getUser()->id
            && !$this->get('core.security')->hasPermission('USER_UPDATE')
        ) {
            throw new AccessDeniedException();
        }

        $msg = $this->get('core.messenger');

        $this->get('api.service.newsletter')
            ->updateItem($id, $request->request->all());

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
