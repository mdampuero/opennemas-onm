<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;
use Common\ORM\Entity\Newsletter;

/**
 * Handles the actions for the newsletter
 */
class NewsletterController extends Controller
{
    /**
     * Lists all the available newsletters.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function listAction()
    {
        $sr                = $this->get('setting_repository');
        $newsletterService = $this->get('api.service.newsletter');

        $maxAllowed      = $sr->get('max_mailing');
        $lastInvoice     = new \DateTime($sr->get('last_invoice'));
        $totalSendings   = $newsletterService->getSentNewslettersSinceLastInvoice($lastInvoice);
        $lastInvoiceText = $lastInvoice->format(_('Y-m-d'));

        // Check if the module is configured, if not redirect to the config form
        $redirection = $this->checkModuleActivated();
        if ($redirection != false) {
            return $redirection;
        }

        if ($maxAllowed > 0 && $totalSendings >= $maxAllowed) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You have reached your maximum of emails allowed to send per month')
            );
        }

        $message = sprintf(_('No newsletters were sent from %s'), $lastInvoiceText);
        if ($totalSendings > 0) {
            $message = sprintf(_('%d newsletter sent from %s'), (int) $totalSendings, $lastInvoiceText);
        }

        if ($maxAllowed > 0) {
            $message = sprintf(
                _('%s newsletter sent from %s (%d allowed).'),
                (int) $totalSendings,
                $lastInvoiceText,
                (int) $maxAllowed
            );
        }

        $this->get('session')->getFlashBag()->add(
            'info',
            $message
        );


        return $this->render('newsletter/list.tpl', [ 'message' => $message ]);
    }

    /**
     * List the form for create or load contents in a newsletter.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function createAction()
    {
        $configurations = $this->get('setting_repository')->get('newsletter_maillist');

        $newsletterContent = [];
        $menu              = new \Menu();

        $menu->getMenu('frontpage');
        $i = 1;
        foreach ($menu->items as $item) {
            if ($item->type != 'category' &&
                $item->type != 'blog-category' &&
                $item->type != 'internal'
            ) {
                continue;
            }

            unset($item->pk_item);
            unset($item->link);
            unset($item->pk_father);
            unset($item->type);
            $item->id           = $i;
            $item->items        = [];
            $item->content_type = 'container';

            $newsletterContent[] = $item;

            if (!empty($item->submenu)) {
                foreach ($item->submenu as $subitem) {
                    unset($subitem->pk_item);
                    unset($subitem->link);
                    unset($subitem->pk_father);
                    unset($subitem->type);
                    unset($subitem->submenu);
                    $subitem->id           = $i++;
                    $subitem->items        = [];
                    $subitem->content_type = 'container';
                    $newsletterContent[]   = $subitem;
                }
            }

            unset($item->submenu);
            $i++;
        }

        $time = new \DateTime();
        $time = $time->format('d/m/Y');

        return $this->render('newsletter/steps/1-pick-elements.tpl', [
            'name'              => $configurations['name'] . ' [' . $time . ']',
            'newsletterContent' => $newsletterContent,
        ]);
    }

    /**
     * Shows the contents of a newsletter given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function showContentsAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $item = $this->get('api.service.newsletter')->getItem($id);

        foreach ($item->contents as $container) {
            foreach ($container['items'] as &$containerElement) {
                if (!array_key_exists('content_type_l10n_name', $containerElement)) {
                    $type    = $containerElement['type'];
                    $content = new $type();

                    $containerElement->content_type_l10n_name = $content->content_type_l10n_name;
                    $containerElement->content_type_name      = \underscore($type);
                }
            }
        }

        return $this->render('newsletter/steps/1-pick-elements.tpl', [
            'with_html'         => true,
            'newsletter'        => $item,
            'newsletterContent' => $item->contents,
        ]);
    }

    /**
     * Saves the newsletter items
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function saveContentsAction(Request $request)
    {
        $id          = (int) $request->request->getDigits('id');
        $contentsRAW = $request->request->get('content_ids');
        $containers  = json_decode($contentsRAW);

        $this->get('core.locale')->setContext('frontend');

        foreach ($containers as $container) {
            foreach ($container->items as &$content) {
                $content->content_type = \classify($content->content_type_name);
                unset($content->content_type_name);
                unset($content->content_type_l10n_name);
            }
        }

        $ns = $this->get('api.service.newsletter');
        $nm = $this->get('core.renderer.newsletter');

        $siteTitle    = $this->get('setting_repository')->get('site_name');
        $time         = new \DateTime();
        $defaultTitle = sprintf('%s [%s]', $siteTitle, $time->format('d/m/Y'));
        $title        = $request->request->filter('title', $defaultTitle, FILTER_SANITIZE_STRING);
        $html         = $nm->render($containers);

        try {
            if ($id > 0) {
                $newsletter = $ns->patchItem($id, [
                    'status'   => 0,
                    'title'    => $title,
                    'contents' => $containers,
                    'html'     => $html,
                    'updated'  => new \Datetime(),
                ]);
            } else {
                $newsletter = $ns->createItem([
                    'status'   => 0,
                    'title'    => $title,
                    'contents' => $containers,
                    'sent'     => null,
                    'html'     => $html,
                ]);

                $id = $newsletter->id;
            }

            return $this->redirect($this->generateUrl(
                'backend_newsletters_preview',
                [ 'id' => $id ]
            ));
        } catch (\Exception $e) {
            $this->get('error.log')->error(sprintf(
                'Error while saving the newsletter contents: %s',
                $e->getMessage()
            ));

            $this->get('session')->getFlashBag()->add(
                'error',
                _("There was an error while saving the newsletter ")
            );

            return $this->redirect($this->generateUrl('backend_newsletters_list'));
        }
    }

    /**
     * Previews the html contents for a newsletter given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function previewAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id');

        try {
            $item = $this->get('api.service.newsletter')->getItem($id);

            return $this->render('newsletter/steps/2-preview.tpl', [
                'newsletter' => $item
            ]);
        } catch (\Api\Exception\GetItemException $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("There was an error while fetching the newsletter ")
            );

            return $this->redirect($this->generateUrl(
                'backend_newsletters_list'
            ));
        }
    }

    /**
     * Saves the HTML content of a newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function saveHtmlContentAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id');

        try {
            $newsletter = $this->get('api.service.newsletter')->patchItem($id, [
                'title'   => $request->request->filter('title', FILTER_SANITIZE_STRING),
                'html'    => $request->request->filter('html', FILTER_SANITIZE_STRING),
                'updated' => new \Datetime(),
            ]);

            return new JsonResponse(['messages' => [[
                'id'      => '200',
                'type'    => 'success',
                'message' => sprintf(_('Content saved successfully'))
            ]]]);
        } catch (\Exception $e) {
            $this->get('error.log')->error(sprintf(
                'Error while updating the newsletter (%d): %s',
                $id,
                $e->getMessage()
            ));

            $this->get('session')->getFlashBag()->add(
                'error',
                _("There was an error while saving the newsletter ")
            );

            return new JsonResponse(['messages' => [[
                'id'      => '400',
                'type'    => 'error',
                'message' => sprintf(_('Error while updating content'))
            ]]]);
        }
    }

    /**
     * Lists all the available recipients and allows to select them before send
     * the newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function pickRecipientsAction(Request $request)
    {
        $id         = $request->query->getDigits('id');
        $sm         = $this->get('setting_repository');
        $newsletter = $this->get('api.service.newsletter')->getItem($id);
        $recipients = [];


        $ss       = $this->get('api.service.subscription');
        $ssb      = $this->get('api.service.subscriber');
        $oql      = $request->query->get('oql', '');
        $response = $ss->getList($oql);

        $lists = array_filter($response['items'], function ($list) {
            return in_array(224, $list->privileges);
        });

        $recipients = [];
        foreach ($lists as $list) {
            $recipients[] = [
                'uuid' => uniqid(),
                'type' => 'list',
                'name' => $list->name,
                'id'   => $list->pk_user_group,
                'subscribers' => $ssb->getList(
                    '(user_group_id = "' . $list->pk_user_group
                    . '" and status != 0)'
                )['total']
            ];
        }

        $maillistConfigs = $sm->get('newsletter_maillist');

        if (!empty($maillistConfigs['email'])) {
            $recipients[] = [
                'uuid' => uniqid(),
                'type' => 'external',
                'name' => $maillistConfigs['email'],
                'email' => $maillistConfigs['email'],
            ];
        }

        $actOnMarketingList = $this->get('orm.manager')->getDataSet('Settings', 'instance')
            ->get('actOn.marketingLists', []);

        foreach ($actOnMarketingList as $list) {
            $recipients[] = [
                'uuid' => uniqid(),
                'type' => 'acton',
                'name' => $list['name'],
                'id'   => $list['id'],
            ];
        }

        return $this->render('newsletter/steps/3-pick-recipients.tpl', [
            'id'      => $id,
            'content' => $newsletter,
            'extra'   => [
                'newsletter_handler' => $sm->get('newsletter_subscriptionType'),
                'recipients'         => $recipients,
            ]
        ]);
    }

    /**
     * Sends a newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function sendAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id');

        $recipients = $request->request->get('recipients');
        $recipients = json_decode($recipients);

        $newsletterService = $this->get('api.service.newsletter');
        $newsletterSender  = $this->get('core.helper.newsletter_sender');

        try {
            $newsletter = $newsletterService->getItem($id);
            $report     = $newsletterSender->send($newsletter, $recipients);

            // Duplicate newsletter if it was sent before.
            if ($newsletter->sent_items > 0) {
                $data = array_merge($newsletter->getStored(), [
                    'recipients' => $recipients,
                    'sent'       => new \Datetime(),
                    'sent_items' => $report['total'],
                    'updated'    => new \Datetime(),
                ]);

                unset($data['id']);

                $newsletter = $this->get('api.service.newsletter')->createItem($data);
            } else {
                $this->get('api.service.newsletter')->patchItem($id, [
                    'recipients' => $recipients,
                    'sent'       => new \Datetime(),
                    'sent_items' => $report['total'],
                    'updated'    => new \Datetime(),
                ]);
            }

            return $this->render('newsletter/steps/4-send.tpl', [
                'send_report' => $report,
                'newsletter'  => $newsletter,
            ]);
        } catch (\Exception $e) {
            $this->get('error.log')->error(
                sprintf('Error while sending the newsletter %s: %s', $id, $e->getMessage())
            );

            $this->get('session')->getFlashBag()->add(
                'error',
                _("There was an error while sending the newsletter ")
            );

            return $this->redirect($this->generateUrl('backend_newsletters_list'));
        }
    }

    /**
     * Configures the newsletter module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function configAction()
    {
        return $this->render('newsletter/settings.tpl');
    }

    /**
     * Checks if the module is activated, if not redirect to the configuration form
     *
     * @return boolean
     */
    private function checkModuleActivated()
    {
        $type   = $this->get('setting_repository')->get('newsletter_subscriptionType');
        $config = $this->get('setting_repository')->get('newsletter_maillist');

        // If the module doesn't have settings already saved
        // we redirect to the module configuration form
        if (is_null($config) || !$type) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please fill the mail list email address in the module configuration.')
            );

            return $this->redirect($this->generateUrl('backend_newsletters_config'));
        }

        // There is settings saved but we will check if they are valid
        foreach ($config as $key => $value) {
            if ($type == 'submit' || ($key != 'subscription' && $key != 'email')) {
                if (!empty($value)) {
                    continue;
                }
                $this->get('session')->getFlashBag()->add('error', _(
                    'Your newsletter configuration is not completed. Please' .
                    ' go to settings and complete the form.'
                ));

                return $this->redirect($this->generateUrl('backend_newsletters_config'));
            }
        }

        return false;
    }
}
