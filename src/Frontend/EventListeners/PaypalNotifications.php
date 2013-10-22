<?php
/**
 * Handles all the events after content updates
 *
 * @package Framework_EventListeners
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Onm\Settings as s;

/**
 * Handles all the events after content updates
 *
 * @package Backend_EventListeners
 **/
class PaypalNotifications implements EventSubscriberInterface
{
    /**
     * Register the content event handler
     *
     * @return void
     **/
    public static function getSubscribedEvents()
    {
        return array(
            'paywall.recurring' => array(
                array('paywallRecurring', 5),
            ),
        );
    }

    /**
     * Perform the actions to process paypal ipn for recurring payments
     *
     * @param Event $event The event to handle
     *
     * @return void
     **/
    public function paywallRecurring(Event $event)
    {
        $ipnData = $event->getArgument('ipnData');

        switch ($ipnData['txn_type']) {
            case 'recurring_payment_profile_created':
                // Create initial order registry
                $order = new \Order();
                $order->create(
                    array(
                        'user_id'        => $ipnData['rp_invoice_id'],
                        'content_id'     => 0,
                        'created'        => new \DateTime(),
                        'payment_id'     => $ipnData['initial_payment_txn_id'],
                        'payment_status' => $ipnData['initial_payment_status'],
                        'payment_amount' => (int) $ipnData['initial_payment_amount'],
                        'payment_method' => 'initial_payment_amount',
                        'type'           => 'paywall',
                        'params'         => array(
                            'recurring_payment_id' => $ipnData['recurring_payment_id'],
                        ),
                    )
                );

                // Update subscription date
                $newUserSubscriptionDate = new \DateTime($ipnData['next_payment_date']);
                $newUserSubscriptionDate->setTimezone(new \DateTimeZone('UTC'));

                // Update user subscription limit
                $user = new \User($ipnData['rp_invoice_id']);
                $user->addSubscriptionLimit($newUserSubscriptionDate);

                // Send mail to user notificating that subscription is activated
                $tplMail = new \Template(TEMPLATE_USER);
                $tplMail->caching = 0;
                $mailBody = $tplMail->fetch('paywall/emails/payment_success.tpl');
                $email = \Swift_Message::newInstance();
                $email
                    ->setSubject(sprintf(_('%s - Premium subscription activated'), s::get('site_title')))
                    ->setBody($mailBody, 'text/plain')
                    ->setTo($ipnData['payer_email'])
                    ->setFrom(array($ipnData['receiver_email'] => s::get('site_name')))
                    ->setSender(array('no-reply@postman.opennemas.com' => s::get('site_name')));

                try {
                    $mailer = $this->get('mailer');
                    $mailer->send($email);
                } catch (\Swift_SwiftException $e) {

                }


                // $_SESSION['meta'] = $user->getMeta();
                // unset($_SESSION['paywall_transaction']);
                break;
            case 'recurring_payment':
                // Create recurring order registry
                $order = new \Order();
                $order->create(
                    array(
                        'user_id'        => $ipnData['rp_invoice_id'],
                        'content_id'     => 0,
                        'created'        => new \DateTime(),
                        'payment_id'     => $ipnData['txn_id'],
                        'payment_status' => $ipnData['payment_status'],
                        'payment_amount' => (int) $ipnData['mc_gross'],
                        'payment_method' => $ipnData['payment_type'],
                        'type'           => 'paywall',
                        'params'         => array(
                            'recurring_payment_id' => $ipnData['recurring_payment_id'],
                        ),
                    )
                );

                // Update subscription date
                $newUserSubscriptionDate = new \DateTime($ipnData['next_payment_date']);
                $newUserSubscriptionDate->setTimezone(new \DateTimeZone('UTC'));

                // Update user subscription limit
                $user = new \User($ipnData['rp_invoice_id']);
                $user->addSubscriptionLimit($newUserSubscriptionDate);

                // $_SESSION['meta'] = $user->getMeta();
                // unset($_SESSION['paywall_transaction']);
                break;

            default:
                // code...
                break;
        }
    }
}
