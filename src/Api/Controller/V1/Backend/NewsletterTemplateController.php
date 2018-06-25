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
     * Returns a list of extra data.
     *
     * @param array $item The list of items.
     *
     * @return array Array of template parameters.
     */
    private function getExtraData()
    {
        $extra = [];

        $recipients = [];

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([
                'newsletter_maillist',
                'newsletter_subscriptionType',
                'actOn.marketingLists',
            ]);

        $extra['newsletter_handler'] = $settings['newsletter_subscriptionType'];

        $ss       = $this->get('api.service.subscription');
        $ssb      = $this->get('api.service.subscriber');
        $response = $ss->getList('');

        $lists = array_filter($response['items'], function ($list) {
            return in_array(224, $list->privileges);
        });

        $extra['recipients'] = [];
        foreach ($lists as $list) {
            $extra['recipients'][] = [
                'type' => 'list',
                'name' => $list->name,
                'id'   => (string) $list->pk_user_group,
                'subscribers' => (string) $ssb->getList(
                    '(user_group_id = "' . $list->pk_user_group
                    . '" and status != 0)'
                )['total']
            ];
        }

        if (!empty($settings['newsletter_maillist'])) {
            $extra['recipients'][] = [
                'type' => 'external',
                'name' => $settings['newsletter_maillist']['email'],
                'email' => $settings['newsletter_maillist']['email'],
            ];
        }

        if (empty($settings['actOn.marketingLists'])) {
            $settings['actOn.marketingLists'] = [];
        }

        foreach ($settings['actOn.marketingLists'] as $list) {
            $extra['recipients'][] = [
                'type' => 'acton',
                'name' => $list['name'],
                'id'   => $list['id'],
            ];
        }

        $contentTypesAvailable = \ContentManager::getContentTypesFiltered();
        unset($contentTypesAvailable['comment']);

        $extra['content_types'] = [
            [ 'title' => _('Any'), 'value' => null ]
        ];

        foreach ($contentTypesAvailable as $key => $value) {
            $extra['content_types'][] = [
                'title' => _($value),
                'value' => $key
            ];
        }


        // $hours = [];
        // $date  = new DateTime(null, new DatetTimeZome('UTC'));
        // for ($i = 0; $i < 24; $i++) {
        //     $date->add('1 hour');
        //     $hours[] = [ "internal" => $i, "text" => $date->format('h:m')];
        // }
        $extra['hours'] = [];
        for ($i = 0; $i < 24; $i++) {
            $extra['hours'][] = sprintf("%02d:00", $i);
        }

        $extra['days'] = [
            _("Monday"),
            _("Tuesday"),
            _("Wednesday"),
            _("Thursday"),
            _("Friday"),
            _("Saturday"),
            _("Sunday"),
        ];

        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy('internal_category = 1');

        $extra['categories'] = $converter->responsify($categories);
        array_unshift($extra['categories'], [
            'pk_content_category' => null,
            'title' => _('All')
        ]);

        return $extra;
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

        $values = $request->request->all();

        foreach ($values['schedule']['hours'] as &$hour) {
            $hour = $hour['text'];
        }

        foreach ($values['schedule']['days'] as &$day) {
            $day = (int) $day;
        }
        $values['schedule']['days'] = array_unique($values['schedule']['days']);

        foreach ($values['contents'] as &$container) {
            foreach ($container['items'] as &$item) {
                if ($item['content_type_name'] === 'list') {
                    $newItem = [
                        'content_type_l10n_name' => _('List of contents'),
                        'oql' => $item['oql']
                    ];
                } else {
                    $newItem = [
                        'id'                     => $item['id'],
                        'title'                  => $item['title'],
                        'content_type_l10n_name' => $item['content_type_l10n_name'],
                    ];
                }

                $item = array_merge([
                    'content_type_name'      => $item['content_type_name'],
                ], $newItem);
            }
        }

        $this->get('api.service.newsletter')
            ->updateItem($id, $values);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
