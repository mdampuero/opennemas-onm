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
        $maxAllowed      = $this->get('setting_repository')->get('max_mailing');
        $totalSendings   = $this->get('core.helper.newsletter')->getTotalNumberOfNewslettersSend();
        $lastInvoice     = new \DateTime($this->get('setting_repository')->get('last_invoice'));
        $lastInvoiceText = $lastInvoice->format(_('Y-m-d'));

        // Check if the module is configured, if not redirect to the config form
        $configuredRedirection = $this->checkModuleActivated();

        if ($configuredRedirection != false) {
            return $configuredRedirection;
        }

        $message = sprintf(_('No newsletter sent from %s.'), $lastInvoiceText);
        if ($totalSendings > 0) {
            $message = sprintf(_('%d newsletter sent from %s.'), (int) $totalSendings, $lastInvoiceText);
        }

        if ($maxAllowed > 0) {
            $message = sprintf(
                _('%s newsletter sent from %s (%d allowed).'),
                (int) $totalSendings,
                $lastInvoiceText,
                (int) $maxAllowed
            );
        }

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
        $id         = $request->query->getDigits('id');
        $newsletter = new \Newsletter($id);

        $containers = json_decode($newsletter->data);

        foreach ($containers as $container) {
            foreach ($container->items as &$item) {
                if (!property_exists($item, 'content_type_l10n_name')) {
                    $type    = $item->content_type;
                    $content = new $type();

                    $item->content_type_l10n_name = $content->content_type_l10n_name;
                    $item->content_type_name      = \underscore($type);
                }
            }
        }

        return $this->render('newsletter/steps/1-pick-elements.tpl', [
            'with_html'         => true,
            'newsletter'        => $newsletter,
            'newsletterContent' => $containers,
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

        $time = new \DateTime();
        $time = $time->format('d/m/Y');

        $title = $request->request->filter(
            'title',
            $this->get('setting_repository')->get('site_name') . ' [' . $time . ']',
            FILTER_SANITIZE_STRING
        );

        $nm = $this->get('newsletter_manager');

        if ($id > 0) {
            $newsletter = new \Newsletter($id);

            $newValues = [
                'title' => $title,
                'data'  => $contentsRAW,
                'html'  => $nm->render($containers),
            ];

            if (is_null($newsletter->html)) {
                $newValues['html'] = $nm->render($containers);
            }

            $newsletter->update($newValues);
        } else {
            $newsletter = new \Newsletter();
            $newsletter->create([
                'title'   => $title,
                'data'    => json_encode($containers),
                'html'    => $nm->render($containers),
            ]);
        }

        return $this->redirect($this->generateUrl(
            'backend_newsletters_preview',
            ['id' => $newsletter->pk_newsletter]
        ));
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
        $id         = (int) $request->query->getDigits('id');
        $newsletter = new \Newsletter($id);

        return $this->render('newsletter/steps/2-preview.tpl', [
            'newsletter' => $newsletter
        ]);
    }

    /**
     * Description of this action
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

        $newsletter = new \Newsletter($id);
        $newsletter->update([
            'title' => $request->request->filter('title', FILTER_SANITIZE_STRING),
            'html'  => $request->request->filter('html', FILTER_SANITIZE_STRING),
        ]);

        return new JsonResponse(['messages' => [[
            'id'      => '200',
            'type'    => 'success',
            'message' => sprintf(_('Content saved successfully'))
        ]]]);
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
        $content    = new \Newsletter($id);
        $recipients = [];


        $ss       = $this->get('api.service.subscription');
        $ssb      = $this->get('api.service.subscriber');
        $oql      = $request->query->get('oql', '');
        $response = $ss->getList($oql);

        $recipients = array_map(function ($list) use ($ssb) {
            $item = [
                'uuid' => uniqid(),
                'type' => 'list',
                'name' => $list->name,
                'id'   => $list->pk_user_group,
                'subscribers' => $ssb->getList(
                    '(user_group_id ~ "' . $list->pk_user_group
                    . '" and status != 0)'
                )['total']
            ];

            return $item;
        }, array_filter($response['items'], function ($list) {
            return in_array(224, $list->privileges);
        }));


        $maillistConfigs = $sm->get('newsletter_maillist');

        if (!empty($maillistConfigs['email'])) {
            $recipients[] = [
                'uuid' => uniqid(),
                'type' => 'external',
                'name' => $maillistConfigs['email'],
                'email' => $maillistConfigs['email'],
            ];
        }

        return $this->render('newsletter/steps/3-pick-recipients.tpl', [
            'id'      => $id,
            'content' => $content,
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
        $id = $request->query->getDigits('id');

        $recipients = $request->request->get('recipients');
        $recipients = json_decode($recipients);

        $newsletterSender = $this->container->getParameter('mailer_no_reply_address');
        $configurations   = $this->get('setting_repository')->get('newsletter_maillist');

        if (empty($newsletterSender)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Your newsletter configuration is not complete. You must complete the sender email address.')
            );

            return $this->redirect($this->generateUrl('backend_newsletters_list'));
        }

        // Prepare the newsletter contents to send
        $newsletter = new \Newsletter($id);

        if (empty($newsletter->title)) {
            $newsletter->title = '[Boletin]';
        }
        $newsletter->html = htmlspecialchars_decode($newsletter->html, ENT_QUOTES);

        $report = $this->get('core.helper.newsletter')->send($newsletter, $recipients);

        return $this->render('newsletter/steps/4-send.tpl', [
            'send_report' => $report,
            'newsletter'  => $newsletter,
        ]);
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
    public function configAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $configurations = [
                'newsletter_maillist'         => $request->request->get('newsletter_maillist'),
                'newsletter_subscriptionType' => $request->request->get('newsletter_subscriptionType'),
            ];

            foreach ($configurations as $key => $value) {
                $this->get('setting_repository')->set($key, $value);
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Newsletter module settings saved successfully.')
            );

            return $this->redirect($this->generateUrl('backend_newsletters_config'));
        }

        $configurations = $this->get('setting_repository')->get([
            'newsletter_maillist',
            'newsletter_subscriptionType',
            'recaptcha',
            'max_mailing'
        ]);

        // Check that user has configured reCaptcha keys if newsletter is enabled
        $missingRecaptcha = false;
        if (empty($configurations['recaptcha']['public_key'])
            || empty($configurations['recaptcha']['private_key'])
        ) {
            $missingRecaptcha = true;
        }

        return $this->render('newsletter/config.tpl', [
            'configs'           => $configurations,
            'missing_recaptcha' => $missingRecaptcha,
        ]);
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

        // If the module doesnt have settings already saved
        // we redirect to the module configuration form
        if (is_null($config) || !$type) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please fill the mail list email address in the module configuration.')
            );

            return $this->redirect($this->generateUrl('backend_newsletter_config'));
        }

        // There is settings saved but we will check if they are valid
        foreach ($config as $key => $value) {
            if ($type == 'submit' || ($key != 'subscription' && $key != 'email')) {
                if (empty($value)) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        _(
                            'Your newsletter configuration is not completed. Please' .
                            ' go to settings and complete the form.'
                        )
                    );

                    return $this->redirect($this->generateUrl('admin_newsletter_config'));
                }
            }
        }

        return false;
    }
}
