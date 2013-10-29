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
class PaypalNotificationsController extends Controller
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

        // First param takes ipn data to be validated.
        // If null, raw POST data is read from input stream
        $ipnMessage = new \PPIPNMessage(null, $config);

        if ($ipnMessage->validate()) {
            // Get ipn data
            $ipnData = $ipnMessage->getRawData();

            dispatchEventWithParams('paywall.recurring', array('ipnData' => $ipnData));

        } else {
            // Write in log
            $logger = getService('logger');
            $logger->error('Unable to validate IPN data');
        }

        return true;
    }
}
