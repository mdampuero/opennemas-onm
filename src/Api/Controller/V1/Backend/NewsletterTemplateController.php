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

        $values = $this->parseValues($request->request->all());

        $newsletter = $this->get('api.service.newsletter')
            ->createItem($values);
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

        $values = $this->parseValues($request->request->all());

        $this->get('api.service.newsletter')
            ->updateItem($id, $values);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Cleans and formats the newsletter template values
     *
     * @param array $values the RAW values to clean
     *
     * @return array the cleaned values
     */
    protected function parseValues($values)
    {
        if (!is_array($values['schedule']['hours'])) {
            $values['schedule']['hours'] = [];
        }

        foreach ($values['schedule']['hours'] as &$hour) {
            $hour = $hour['text'];
        }
        $values['schedule']['hours'] = array_unique($values['schedule']['hours']);
        sort($values['schedule']['hours']);


        if (!is_array($values['schedule']['days'])) {
            $values['schedule']['days'] = [];
        }
        foreach ($values['schedule']['days'] as &$day) {
            $day = (int) $day;
        }
        $values['schedule']['days'] = array_unique($values['schedule']['days']);

        if (!is_array($values['contents'])) {
            $values['contents'] = [];
        }
        foreach ($values['contents'] as &$container) {
            if (!is_array($container['items'])
                || empty($container['items'])
            ) {
                continue;
            }

            foreach ($container['items'] as &$item) {
                $newItem = new \stdClass();

                if ($item['content_type'] === 'list') {
                    if (!array_key_exists('filter', $item['criteria'])) {
                        $item['criteria']['filter'] = '';
                    }

                    $newItem->content_type_l10n_name = _('List of contents');
                    $newItem->criteria               = $item['criteria'];
                    $newItem->content_type           = $item['content_type'];
                } else {
                    $newItem->content_type           = array_key_exists('content_type_name', $item)
                        ? $item['content_type_name'] : $item['content_type'];
                    $newItem->content_type_l10n_name = $item['content_type_l10n_name'];
                    $newItem->id                     = $item['id'];
                    $newItem->title                  = $item['title'];
                }

                $item = $newItem;
            }
        }

        return $values;
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
        $nh = $this->get('core.helper.newsletter');
        $ss = $this->get('api.service.subscription');

        // Get subscriptions with the newsletter privilege enabled.
        $response      = $ss->getList();
        $subscriptions = array_values(
            array_filter($response['items'], function ($a) {
                return in_array(224, $a->privileges);
            })
        );

        $extra = [
            'content_types' => $nh->getContentTypes(),
            'days' => [
                _("Monday"), _("Tuesday"), _("Wednesday"), _("Thursday"),
                _("Friday"), _("Saturday"), _("Sunday"),
            ],
            'filters' => [
                [ 'value' => '', 'title' => _('No filter') ],
                [ 'value' => 'in_last_day', 'title' => _('Last in 24 hours') ],
                [ 'value' => 'most_viewed_24', 'title' => _('Most viewed 24hs') ],
                [ 'value' => 'most_viewed', 'title' => _('Most viewed 3 days') ],
            ],
            'opinion_types' => [
                [ 'value' => '', 'title' => _('Any') ],
                [ 'value' => 'opinion', 'title' => _('Opinion') ],
                [ 'value' => 'blog', 'title' => _('Blog') ],
            ],
            'users'              => $ss->getStats($subscriptions),
            'hours'              => [],
            'newsletter_handler' => $nh->getSubscriptionType(),
            'recipients'         => $nh->getRecipients(),
        ];

        for ($i = 0; $i < 24; $i++) {
            $extra['hours'][] = sprintf("%02d:00", $i);
        }

        return $extra;
    }
}
