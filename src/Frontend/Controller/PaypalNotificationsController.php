<?php
/**
 * Handles the actions for Paypal IPN - Instant Payment notifications
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for Paypal IPN - Instant Payment notifications
 *
 * @package Frontend_Controllers
 */
class PaypalNotificationsController extends Controller
{
    /**
     * Handles IPN notifications for paywall recurring payments
     *
     * @return Response the response object
     */
    public function paywallAction()
    {
        // Get paypal developer mode settings
        $databaseSettings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('paywall_settings');

        $config = [
            'mode'            => ($databaseSettings['developer_mode'] == false) ? 'sandbox' : 'live',
            "acct1.UserName"  => $databaseSettings['paypal_username'],
            "acct1.Password"  => $databaseSettings['paypal_password'],
            "acct1.Signature" => $databaseSettings['paypal_signature'],
        ];

        // First param takes ipn data to be validated.
        // If null, raw POST data is read from input stream
        $ipnMessage = new \PPIPNMessage(null, $config);

        if ($ipnMessage->validate()) {
            // Get ipn data
            $ipnData = $ipnMessage->getRawData();

            $this->get('core.dispatcher')->dispatch('paywall.recurring', [ 'ipnData' => $ipnData ]);
        } else {
            // Write in log
            $logger = getService('logger');
            $logger->error('Unable to validate IPN data');
        }

        return new Response('', 200);
    }
}
