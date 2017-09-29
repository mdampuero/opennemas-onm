<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
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
 *
 * @package Backend_Controllers
 */
class NewsletterController extends Controller
{
    /**
     * Lists all the available newsletters
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $maxAllowed      = s::get('max_mailing');
        $totalSendings   = $this->getTotalNumberOfNewslettersSend();
        $lastInvoice     = new \DateTime(s::get('last_invoice'));
        $lastInvoiceText = $lastInvoice->format(_('Y-m-d'));

        // Check if the module is configured, if not redirect to the config form
        $configuredRedirection = $this->checkModuleActivated();

        if ($configuredRedirection != false) {
            return $configuredRedirection;
        }

        if ($maxAllowed > 0) {
            $message = sprintf(
                _('%s newsletter sent from %d (%d allowed).'),
                (int) $totalSendings,
                $lastInvoiceText,
                (int) $maxAllowed
            );
        } elseif ($totalSendings == 0) {
            $message = sprintf(_('No newsletter sent from %s.'), $lastInvoiceText);
        } else {
            $message = sprintf(_('%d newsletter sent from %s.'), (int) $totalSendings, $lastInvoiceText);
        }

        return $this->render('newsletter/list.tpl', ['message' => $message]);
    }

    /**
     * List the form for create or load contents in a newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function createAction(Request $request)
    {
        $configurations = s::get('newsletter_maillist');

        $newsletterContent = [];
        $menu              = new \Menu();

        $menu->getMenu('frontpage');
        $i = 1;
        foreach ($menu->items as $item) {
            if ($item->type == 'category' ||
                $item->type == 'blog-category' ||
                $item->type == 'internal'
            ) {
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
        }

        $time = new \DateTime();
        $time = $time->format('d/m/Y');

        return $this->render('newsletter/steps/1-pick-elements.tpl', [
            'name'              => $configurations['name'] . " [" . $time . "]",
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

        return $this->render(
            'newsletter/steps/1-pick-elements.tpl',
            [
                'with_html'         => true,
                'newsletter'        => $newsletter,
                'newsletterContent' => $containers,
            ]
        );
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
            s::get('site_name') . ' [' . $time . ']',
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
            $newsletter->create(
                [
                    'title'   => $title,
                    'data'    => json_encode($containers),
                    'html'    => $nm->render($containers),
                ]
            );
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_newsletter_preview',
                ['id' => $newsletter->pk_newsletter]
            )
        );
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

        return $this->render(
            'newsletter/steps/2-preview.tpl',
            [ 'newsletter' => $newsletter ]
        );
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

        $values = [
            'title' => $request->request->filter('title', FILTER_SANITIZE_STRING),
            'html'  => $request->request->filter('html', FILTER_SANITIZE_STRING),
        ];

        $newsletter->update($values);

        return new JsonResponse([
            'messages' => [
                [
                    'id'      => '200',
                    'type'    => 'success',
                    'message' => sprintf(_('Content saved successfully'))
                ]
            ]
        ]);
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
        $newsletter = new \Newsletter($id);

        $subscriptionType = \Onm\Settings::get('newsletter_subscriptionType');

        $accounts = [];
        if ($subscriptionType === 'create_subscriptor') {
            $sbManager = new \Subscriber();
            $accounts  = $sbManager->getUsers(
                'status > 0 AND subscription = 1',
                '',
                'pk_pc_user ASC'
            );
        } else {
            $configurations = \Onm\Settings::get('newsletter_maillist');

            if (!is_null($configurations)
                && array_key_exists('email', $configurations)
                && !empty($configurations['email'])
            ) {
                $subscriber = new \Subscriber();

                $subscriber->email = $configurations['email'];
                $subscriber->name  = $configurations['name'];

                $accounts[] = $subscriber;
            }
        }

        $accounts = \Onm\StringUtils::convertToUtf8($accounts);

        // Ajax request
        if ($request->isXmlHttpRequest()) {
            return new Response(
                json_encode($accounts),
                200,
                ['Content-type' => 'application/json']
            );
        }

        $recipients = [];

        return $this->render(
            'newsletter/steps/3-pick-recipients.tpl',
            [
                'id'               => $id,
                'accounts'         => $accounts,
                'recipients'       => $recipients,
                'subscriptionType' => $subscriptionType,
            ]
        );
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

        $sentResult = [];
        $newsletter = new \Newsletter($id);

        $htmlContent = htmlspecialchars_decode($newsletter->html, ENT_QUOTES);

        $newsletterSender = $this->container->getParameter('mailer_no_reply_address');
        $configurations   = s::get('newsletter_maillist');

        if (empty($newsletterSender)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Your newsletter configuration is not complete. You must complete the sender email address.')
            );

            return $this->redirect($this->generateUrl('admin_newsletters'));
        }

        $params = [
            'subject'            => $newsletter->title,
            'mail_from'          => $configurations['sender'],
            'mail_from_name'     => s::get('site_name'),
        ];

        $maxAllowed = s::get('max_mailing');
        $remaining  = $maxAllowed - $this->getTotalNumberOfNewslettersSend();

        $subject = (!isset($params['subject'])) ? '[Boletin]' : $params['subject'];

        $message = $htmlContent;

        $sent = 0;
        if (!empty($recipients)) {
            foreach ($recipients as $mailbox) {
                if (empty($maxAllowed) || (!empty($maxAllowed) && !empty($remaining))) {
                    try {
                         // Build the message
                        $message = \Swift_Message::newInstance();
                        $message
                            ->setSubject($subject)
                            ->setBody($htmlContent, 'text/html')
                            ->setFrom([$params['mail_from'] => $params['mail_from_name']])
                            ->setSender($newsletterSender)
                            ->setTo([$mailbox->email => $mailbox->name]);

                        // Send it
                        $properlySent = $this->get('mailer')->send($message);

                        $this->get('application.log')->notice(
                            "Email sent. Backend newsletter sent (to: " . $mailbox->email . ")"
                        );

                        $sentResult [] = [$mailbox, (bool) $properlySent, _('Unable to deliver your email')];
                        $remaining--;
                        $sent++;
                    } catch (\Exception $e) {
                        $sentResult [] = [$mailbox, false, $e->getMessage()];
                    }
                } else {
                    $sentResult [] = [$mailbox, false, _('Max sents reached.')];
                }
            }
        }

        if (empty($newsletter->sent)) {
            $newsletter->update(['sent' => $sent]);
        } else {
            //duplicated newsletter for count month mail send
            $newsletter->create(
                [
                    'title'   => $newsletter->title,
                    'data'    => $newsletter->data,
                    'html'    => $newsletter->html,
                    'sent'    => $sent,
                ]
            );
        }

        return $this->render(
            'newsletter/steps/4-send.tpl',
            [
                'sent_result' => $sentResult,
                'newsletter'  => $newsletter,
            ]
        );
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
                s::set($key, $value);
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Newsletter module settings saved successfully.')
            );

            return $this->redirect($this->generateUrl('admin_newsletter_config'));
        } else {
            $configurations = s::get(
                [
                    'newsletter_maillist',
                    'newsletter_subscriptionType',
                    'recaptcha',
                    'max_mailing'
                ]
            );

            // Check that user has configured reCaptcha keys if newsletter is enabled
            $missingRecaptcha = false;
            if (empty($configurations['recaptcha']['public_key'])
                || empty($configurations['recaptcha']['private_key'])
            ) {
                $missingRecaptcha = true;
            }

            return $this->render(
                'newsletter/config.tpl',
                [
                    'configs'           => $configurations,
                    'missing_recaptcha' => $missingRecaptcha,
                ]
            );
        }
    }

    /**
     * Checks if the module is activated, if not redirect to the configuration form
     *
     * @return boolean
     */
    private function checkModuleActivated()
    {
        $type   = s::get('newsletter_subscriptionType');
        $config = s::get('newsletter_maillist');

        if (is_null($config) || !$type) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please fill the mail list email address in the module configuration.')
            );

            return $this->redirect($this->generateUrl('admin_newsletter_config'));
        } else {
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
        }

        return false;
    }

    /**
     * Count total mailing sends in current month
     *
     * @return int Total number of mail sent in current mount
     */
    private function getTotalNumberOfNewslettersSend()
    {
        // Get maximum number of allowed sending mails
        $maxAllowed = s::get('max_mailing');

        // Get last invoice DateTime
        $lastInvoiceDate = $this->updateLastInvoice();

        // Get today DateTime
        $today = new \DateTime();

        // Get all newsletters updated between today and last invoice
        $nm                          = $this->get('newsletter_manager');
        $where                       = " updated >= '" . $lastInvoiceDate->format('Y-m-d H:i:s') . "'
            AND updated <= '" . $today->format('Y-m-d H:i:s') . "' and sent > 0";
        list($nmCount, $newsletters) = $nm->find($where, 'created DESC');

        // Check if user has reached the limit of sent newsletters
        $totalSent = 0;
        if ($nmCount > 0) {
            foreach ($newsletters as $newsletter) {
                $totalSent += $newsletter->sent;
            }

            if ($maxAllowed > 0 && ($maxAllowed - $totalSent <= 0)) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('You have reached the maximum of emails allowed to send')
                );

                return $maxAllowed;
            }
        }

        return $totalSent;
    }

    /**
     * Updates last invoice date
     *
     * @param string $date Date of last invoice
     *
     * @return DateTime Last invoice
     */
    private function updateLastInvoice()
    {
        // Generate last invoice DateTime
        $lastInvoice = new \DateTime(s::get('last_invoice'));

        // Set day to 28 if it's more than that
        if ($lastInvoice->format('d') > 28) {
            $lastInvoice->setDate(
                $lastInvoice->format('Y'),
                $lastInvoice->format('m'),
                28
            );
        }

        // Get today DateTime
        $today = new \DateTime();

        // Get next invoice DateTime
        $nextInvoiceDate = new \DateTime($lastInvoice->format('Y-m-d H:i:s'));
        $nextInvoiceDate->modify('+1 month');

        // Update next invoice DateTime
        while ($today > $nextInvoiceDate) {
            $nextInvoiceDate->modify('+1 month');
        }

        // Update last invoice DateTime
        $lastInvoice = $nextInvoiceDate->modify('-1 month');

        s::set('last_invoice', $lastInvoice->format('Y-m-d H:i:s'));

        return $lastInvoice;
    }
}
