<?php
/**
 * Handles the actions for Paypal IPN - Instant Payment notifications
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for Paypal IPN - Instant Payment notifications
 *
 * @package Frontend_Controllers
 **/
class IpnController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        // Init code here
    }

    /**
     * NOT IMPLEMENTED
     *
     * @return void
     * @author
     **/
    public function dispatchAction(Request $request)
    {
        $eventDispatcher = getService('event_dispatcher');

        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        foreach ($params as $paramName => $paramValue) {
            $event->setArgument($paramName, $paramValue);
        }

        $eventDispatcher->dispatch('content.update', $event);
    }

    /**
     * Handles IPN notifications for paywall recurring payments
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function paywallAction(Request $request)
    {
        // Get paypal developer mode settings
        $databaseSettings = s::get('paywall_settings');

        $config = array(
            'mode'            => ($databaseSettings['developer_mode'] == false) ? 'sandbox' : 'live',
            "acct1.UserName"  => $databaseSettings['paypal_username'],
            "acct1.Password"  => $databaseSettings['paypal_password'],
            "acct1.Signature" => $databaseSettings['paypal_signature'],
        );

        // Sample data
        if (empty($_POST)) {
            $data = "cmd=_notify-validate&".
                    "amount=100.00&".
                    "initial_payment_amount=100.00&".
                    "profile_status=Active&".
                    "payer_id=PAW8PJ6JUHSA6&".
                    "product_type=1&".
                    "ipn_track_id=82341f8071453&".
                    "outstanding_balance=0.00&".
                    "shipping=0.00&".
                    "charset=windows-1252&".
                    "period_type=+Regular&".
                    "currency_code=EUR&".
                    "verify_sign=AiPC9BjkCyDFQXbSkoZcgqH3hpacAR4GUPVkvt3BJLXdefWYumRusC8u&".
                    "test_ipn=1&".
                    "initial_payment_status=Pending&".
                    "payment_cycle=Monthly&".
                    "txn_type=recurring_payment_profile_created&".
                    "payer_status=verified&".
                    "first_name=Alex&".
                    "product_name=Month&".
                    "amount_per_cycle=100.00&".
                    "last_name=Rod&".
                    "initial_payment_txn_id=9F023010RX693811A&".
                    "time_created=09%3A59%3A16+Oct+14%2C+2013+PDT&".
                    "resend=true&".
                    "notify_version=3.7&".
                    "recurring_payment_id=I-9LTDP7PHBDNF&".
                    "payer_email=testdrexarj%40gmail.com&".
                    "receiver_email=drexarj-facilitator%40hotmail.com&".
                    "next_payment_date=02%3A00%3A00+Nov+14%2C+2013+PST&".
                    "tax=0.00&".
                    "residence_country=ES";
        } else {
            $data = $_POST;
        }

        // First param takes ipn data to be validated. If null, raw POST data is read from input stream
        $ipnMessage = new \PPIPNMessage($data, $config);
        foreach($ipnMessage->getRawData() as $key => $value) {
            error_log("IPN: $key => $value");
        }

        if($ipnMessage->validate()) {
            error_log("Success: Got valid IPN data");
        } else {
            error_log("Error: Got invalid IPN data");
        }
    }
}
