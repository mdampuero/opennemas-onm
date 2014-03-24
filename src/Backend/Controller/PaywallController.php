<?php
/**
 * Defines the PaywallController class
 *
 * @package  Backend_Controllers
 **/
/**
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for paywall module
 *
 * @package Backend_Controllers
 **/
class PaywallController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        \Onm\Module\ModuleManager::checkActivatedOrForward('PAYWALL');

        $this->times = array(
            'Day'       => _('Day'),
            'Week'      => _('Week'),
            'SemiMonth' => _('SemiMonth'),
            'Month'     => _('Month'),
            'Year'      => _('Year'),
        );

        $this->moneyUnits = array(
            'EUR' => '€',
            'USD' => '$',
        );
    }

    /**
     * Shows a list of purchases for the paywall module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PAYWALL_ADMIN')")
     **/
    public function defaultAction(Request $request)
    {
        $settings = s::get('paywall_settings');

        if (empty($settings)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please configure your Paywall module before using it.')
            );
            return $this->redirect($this->generateUrl('admin_paywall_settings'));
        }
        $users = \User::getUsersWithSubscription(array('limit' => 10));

        $countUsersPaywall = \User::countUsersWithSubscription();

        // Get the last purchases
        $purchases = \Order::find(
            "type='paywall'",
            array(
                'limit' => 10
            )
        );


        // Count how many purchases were done in the last month
        $time = new \DateTime();
        $time->setTimezone(new \DateTimeZone('UTC'))
             ->modify("-1 month");
        $time = $time->format('Y-m-d H:i:s');

        $purchasesLastMonth = \Order::count(
            "type='paywall' AND created > '$time'",
            array()
        );

        return $this->render(
            'paywall/list.tpl',
            array(
                'users'                      => $users,
                'count_users_paywall'        => $countUsersPaywall,
                'purchases'                  => $purchases,
                'count_purchases_last_month' => $purchasesLastMonth,
                'settings'                   => $settings,
                'money_units'                => $this->moneyUnits,
            )
        );
    }

    /**
     * Description of this action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PAYWALL_ADMIN')")
     **/
    public function usersAction(Request $request)
    {
        $page  = $this->request->query->getDigits('page', 1);
        $type  = $this->request->query->filter('type', '', FILTER_SANITIZE_STRING);
        $order = $this->request->query->filter('order', 'username', FILTER_SANITIZE_STRING);
        $name  = $this->request->query->filter('searchname', '', FILTER_SANITIZE_STRING);

        $settings = s::get('paywall_settings');

        $users = array();
        if ($type === '0') {
            $users = \User::getUsersWithSubscription();
        } elseif ($type === '1') {
            $users = \User::getUsersOnlyRegistered();
        } elseif ($type === '2') {
            $usersRegistered = \User::getUsersOnlyRegistered();
            foreach ($usersRegistered as $user) {
                if (isset($user->meta['paywall_time_limit'])) {
                    $users[] = $user;
                }
            }
        } else {
            $usersRegistered = \User::getUsersOnlyRegistered();
            $usersWithSubscription = \User::getUsersWithSubscription();

            $users = array_merge($usersRegistered, $usersWithSubscription);
        }

        // Sort array of users by property
        if (!isset($order) || empty($order)) {
            $order = 'username';
        }

        $users = \ContentManager::sortArrayofObjectsByProperty($users, $order);

        // Filter array by name
        if (!empty($name)) {
            $users = array_filter(
                $users,
                function ($obj) use ($name) {
                    if (strpos($obj->username, $name) !== false) {
                        return true;
                    }
                    return false;
                }
            );
        }

        $itemsPerPage = s::get('items_per_page') ?: 20;

        $usersPage = array_slice($users, ($page-1)*$itemsPerPage, $itemsPerPage);

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => count($users),
                'fileName'    => $this->generateUrl(
                    'admin_paywall_users',
                    array(
                        'order'      => $order,
                        'type'       => $type,
                        'searchname' => $name,
                    )
                ).'&page=%d',
            )
        );

        return $this->render(
            'paywall/users.tpl',
            array(
                'users'       => $usersPage,
                'settings'    => $settings,
                'money_units' => $this->moneyUnits,
                'pagination'  => $pagination,
            )
        );
    }

    /**
     * Returns a CSV file with all the users information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PAYWALL_ADMIN')")
     **/
    public function userListExportAction(Request $request)
    {
        $type = $this->request->query->filter('type', '', FILTER_SANITIZE_STRING);
        $order  = $this->request->query->filter('order', 'name', FILTER_SANITIZE_STRING);
        $name = $this->request->query->filter('searchname', '', FILTER_SANITIZE_STRING);

        $users = array();
        if ($type === '0') {
            $users = \User::getUsersWithSubscription();
        } elseif ($type === '1') {
            $users = \User::getUsersOnlyRegistered();
        } elseif ($type === '2') {
            $usersRegistered = \User::getUsersOnlyRegistered();
            foreach ($usersRegistered as $user) {
                if (isset($user->meta['paywall_time_limit'])) {
                    $users[] = $user;
                }
            }
        } else {
            $usersRegistered = \User::getUsersOnlyRegistered();
            $usersWithSubscription = \User::getUsersWithSubscription();

            $users = array_merge($usersRegistered, $usersWithSubscription);
        }

        // Sort array of users by property
        if (isset($order) && !empty($order)) {
            $users = \ContentManager::sortArrayofObjectsByProperty($users, $order);
        }

        // Filter array by search name
        if (isset($name) && !empty($name)) {
            $users = array_filter(
                $users,
                function ($obj) use ($name) {
                    if (strpos($obj->username, $name) !== false) {
                        return true;
                    }
                    return false;
                }
            );
        }

        $response = $this->render(
            'paywall/partials/users_csv.tpl',
            array(
                'users'   => $users,
            )
        );

        $fileName = 'paywall_users.csv';

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Returns a CSV file with all the purchases information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PAYWALL_ADMIN')")
     **/
    public function purchasesListExportAction(Request $request)
    {
        $order = $this->request->query->filter('order', '', FILTER_SANITIZE_STRING);
        $name  = $this->request->query->filter('searchname', '', FILTER_SANITIZE_STRING);

        $settings = s::get('paywall_settings');
        $purchases = \Order::find(
            "type='paywall'",
            array(
                'limit' => 0
            )
        );

        // Sort array of users by property
        if (isset($order) && !empty($order)) {
            $purchases = \ContentManager::sortArrayofObjectsByProperty($purchases, $order);
        }

        // Filter array by name
        if (isset($name) && !empty($name)) {
            $purchases = array_filter(
                $purchases,
                function ($obj) use ($name) {
                    if (strpos($obj->username, $name) !== false ||
                        strpos($obj->name, $name) !== false
                    ) {
                        return true;
                    }
                    return false;
                }
            );
        }

        $response = $this->render(
            'paywall/partials/purchases_csv.tpl',
            array(
                'purchases'   => $purchases,
                'settings'    => $settings,
                'money_units' => $this->moneyUnits,
            )
        );

        $fileName = 'paywall_purchases.csv';

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Description of this action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PAYWALL_ADMIN')")
     **/
    public function purchasesAction(Request $request)
    {
        $page  = $this->request->query->getDigits('page', 1);
        $order = $this->request->query->filter('order', '', FILTER_SANITIZE_STRING);
        $name  = $this->request->query->filter('searchname', '', FILTER_SANITIZE_STRING);

        $settings = s::get('paywall_settings');
        $purchases = \Order::find(
            "type='paywall'",
            array(
                'limit' => 0
            )
        );

        // Sort array of users by property
        if (isset($order) && !empty($order)) {
            $purchases = \ContentManager::sortArrayofObjectsByProperty($purchases, $order);
        }

        // Filter array by name
        if (isset($name) && !empty($name)) {
            $purchases = array_filter(
                $purchases,
                function ($obj) use ($name) {
                    if (strpos($obj->username, $name) !== false ||
                        strpos($obj->name, $name) !== false
                    ) {
                        return true;
                    }
                    return false;
                }
            );
        }

        $itemsPerPage = s::get('items_per_page') ?: 20;

        $purchasesPage = array_slice($purchases, ($page-1)*$itemsPerPage, $itemsPerPage);

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => count($purchases),
                'fileName'    => $this->generateUrl(
                    'admin_paywall_purchases',
                    array(
                        'order'      => $order,
                        'searchname' => $name,
                    )
                ).'&page=%d',
            )
        );

        return $this->render(
            'paywall/purchases.tpl',
            array(
                'purchases'   => $purchasesPage,
                'settings'    => $settings,
                'money_units' => $this->moneyUnits,
                'pagination'  => $pagination,
            )
        );
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PAYWALL_ADMIN')")
     **/
    public function settingsAction(Request $request)
    {
        $settings = s::get('paywall_settings');

        return $this->render(
            'paywall/settings.tpl',
            array(
                'settings'    => $settings,
                'times'       => $this->times,
                'money_units' => $this->moneyUnits,
            )
        );
    }

    /**
     * Saves the paywall settings
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function settingsSaveAction(Request $request)
    {
        $settingsForm = $request->request->get('settings');

        $settings = array(
            'payment_modes' => array(),
            'recurring_payment_modes' => array(),
        );

        // Check values
        $settings['paypal_username']  = $request->request->filter(
            'settings[paypal_username]',
            '',
            FILTER_SANITIZE_STRING
        );
        $settings['paypal_password']  = $request->request->filter(
            'settings[paypal_password]',
            '',
            FILTER_SANITIZE_STRING
        );
        $settings['paypal_signature'] = $request->request->filter(
            'settings[paypal_signature]',
            '',
            FILTER_SANITIZE_STRING
        );
        $settings['money_unit']       = $request->request->filter(
            'settings[money_unit]',
            'USD',
            FILTER_SANITIZE_STRING
        );
        $settings['recurring']        = $request->request->filter(
            'settings[recurring]',
            '0',
            FILTER_SANITIZE_STRING
        );
        $settings['terms']            = $request->request->filter(
            'settings[terms]',
            '0',
            FILTER_SANITIZE_STRING
        );
        $settings['developer_mode']   = (boolean) $settingsForm['developer_mode'];
        $settings['vat_percentage']   = (int) $settingsForm['vat_percentage'];

        // Check API credentials
        $isValid = (s::get('valid_credentials'))? s::get('valid_credentials') : false;

        // Check IPN end point if recurring payment is enabled
        $isIpnValid = true;

        if ($settings['recurring'] == '1') {
            $isIpnValid = (s::get('valid_ipn'))? s::get('valid_ipn') : false;
        }

        // Check direct payment modes
        $number = count($settingsForm['payment_modes']['time']);
        if ($number > 0) {
            for ($i=0; $i < $number; $i++) {
                $settings['payment_modes'] []= array(
                    'time'              => $settingsForm['payment_modes']['time'][$i],
                    'description'       => $settingsForm['payment_modes']['description'][$i],
                    'price'             => $settingsForm['payment_modes']['price'][$i],
                );
            }
        }

        if (!$isValid) {
            $this->get('session')->getFlashBag()->add('error', _("Paypal API authentication is incorrect. Please try again."));
        } elseif ($isIpnValid === 'waiting') {
            $this->get('session')->getFlashBag()->add('notice', _("We are checking your IPN url. Please wait a minute and try again."));
        } elseif (!$isIpnValid) {
            $this->get('session')->getFlashBag()->add('error', _("Paypal IPN configuration is incorrect. Please validate it and try again."));
        } else {
            $this->get('session')->getFlashBag()->add('success', _("Paywall settings saved."));
            // If config is all ok save data
            s::set('paywall_settings', $settings);
        }

        return $this->redirect($this->generateUrl('admin_paywall_settings'));
    }

    /**
     * Validates user Paypal API Credentials
     *
     * @param $userName the username from credentials
     * @param $password the password from credentials
     * @param $signature the signature from credentials
     * @param $mode the paypal selected mode (sandbox, live)
     *
     * @return Response the response object
     *
     * @Security("has_role('PAYWALL_ADMIN')")
     **/
    public function validateCredentialsAction(Request $request)
    {
        $userName  = $request->request->filter('username', '', FILTER_SANITIZE_STRING);
        $password  = $request->request->filter('password', '', FILTER_SANITIZE_STRING);
        $signature = $request->request->filter('signature', '', FILTER_SANITIZE_STRING);
        $mode      = $request->request->filter('mode', '', FILTER_SANITIZE_STRING);

        // Try getting balance to check API credentials
        $getBalanceRequest = new \GetBalanceRequestType();

        // 0 – Return only the balance for the primary currency holding.
        // 1 – Return the balance for each currency holding.
        $getBalanceRequest->ReturnAllCurrencies = 1;

        $getBalanceReq = new \GetBalanceReq();
        $getBalanceReq->GetBalanceRequest = $getBalanceRequest;

        $APICredentials = array(
            "acct1.UserName"  => $userName,
            "acct1.Password"  => $password,
            "acct1.Signature" => $signature,
            "mode"            => $mode
        );

        $paypalService = new \PayPalAPIInterfaceServiceService($APICredentials);
        try {
            /* wrap API method calls on the service object with a try catch */
            $getBalanceResponse = $paypalService->GetBalance($getBalanceReq);
        } catch (\Exception $ex) {
            $this->get('logger')->notice("Paywall: Error in getBalanceResponse API call.");
        }

        // Connection is ok return true
        if (isset($getBalanceResponse) && $getBalanceResponse->Ack == 'Success') {
            s::set('valid_credentials', 'valid');
            return new Response('ok', '200');
        } else {
            s::set('valid_credentials', false);
            return new Response('fail', '404');
        }
    }

    /**
     * Set checkout to validate user IPN end point
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PAYWALL_ADMIN')")
     **/
    public function setValidateIpnAction(Request $request)
    {
        $userName  = $request->request->filter('username', '', FILTER_SANITIZE_STRING);
        $password  = $request->request->filter('password', '', FILTER_SANITIZE_STRING);
        $signature = $request->request->filter('signature', '', FILTER_SANITIZE_STRING);
        $mode      = $request->request->filter('mode', '', FILTER_SANITIZE_STRING);

        $itemDetails = new \PaymentDetailsItemType();
        $itemDetails->Name         = 'Test IPN url';
        $itemDetails->Amount       = new \BasicAmountType("EUR", '0.01');
        $itemDetails->Quantity     = '1';
        $itemDetails->ItemCategory = 'Digital';

        $paymentDetails = new \PaymentDetailsType();
        $paymentDetails->PaymentDetailsItem[0] = $itemDetails;
        $paymentDetails->OrderTotal = new \BasicAmountType("EUR", '0.01');
        $paymentDetails->PaymentAction = "Sale";

        $setECReqDetails = new \SetExpressCheckoutRequestDetailsType();
        $setECReqDetails->PaymentDetails[0] = $paymentDetails;
        $setECReqDetails->CancelURL = $this->generateUrl('admin_paywall_settings', array(), true);
        $setECReqDetails->ReturnURL = $this->generateUrl(
            'admin_paywall_do_validate_ipn',
            array('username' => $userName, 'password' => $password, 'signature' => $signature, 'mode' => $mode),
            true
        );
        $setECReqDetails->BrandName = s::get('site_name');

        $setECReqType = new \SetExpressCheckoutRequestType();
        $setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;

        $setECReq = new \SetExpressCheckoutReq();
        $setECReq->SetExpressCheckoutRequest = $setECReqType;

        $APICredentials = array(
            "acct1.UserName"  => $userName,
            "acct1.Password"  => $password,
            "acct1.Signature" => $signature,
            "mode"            => $mode
        );

        $paypalService = new \PayPalAPIInterfaceServiceService($APICredentials);
        try {
            /* wrap API method calls on the service object with a try catch */
            $setECResponse = $paypalService->SetExpressCheckout($setECReq);
        } catch (\Exception $ex) {
            $this->get('logger')->notice("Paywall: Error in setECResponse validate IPN API call.");
        }

        if (isset($setECResponse) && $setECResponse->Ack == 'Success') {
            $service = new \Onm\Merchant\PaypalWrapper($APICredentials);
            $token = $setECResponse->Token;
            $paypalUrl = $service->getServiceUrl().'&token='.$token;

            return new Response($paypalUrl);
        } else {
            return new Response('fail', '404');
        }
    }

    /**
     * Do the checkout to validate user IPN end point
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PAYWALL_ADMIN')")
     **/
    public function doValidateIpnAction(Request $request)
    {
        $token     = $request->query->get('token');
        $userName  = $request->query->get('username', '', FILTER_SANITIZE_STRING);
        $password  = $request->query->get('password', '', FILTER_SANITIZE_STRING);
        $signature = $request->query->get('signature', '', FILTER_SANITIZE_STRING);
        $mode      = $request->query->get('mode', '', FILTER_SANITIZE_STRING);

        $getExpressCheckoutDetailsRequest = new \GetExpressCheckoutDetailsRequestType($token);
        $getExpressCheckoutReq = new \GetExpressCheckoutDetailsReq();
        $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

        $APICredentials = array(
            "acct1.UserName"  => $userName,
            "acct1.Password"  => $password,
            "acct1.Signature" => $signature,
            "mode"            => $mode
        );

        $paypalService = new \PayPalAPIInterfaceServiceService($APICredentials);
        try {
            /* wrap API method calls on the service object with a try catch */
            $getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
        } catch (\Exception $ex) {
            $this->get('logger')->notice("Paywall: Error in getECResponse validate IPN API call.");
            $this->get('session')->getFlashBag()->add('error', _("Paypal IPN configuration is incorrect. Please correct it and try again."));
            return $this->redirect($this->generateUrl('admin_paywall_settings'));
        }

        $payerId = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID;

        $orderTotal = new \BasicAmountType();
        $orderTotal->currencyID = 'EUR';
        $orderTotal->value = '0.01';

        $paymentDetails= new \PaymentDetailsType();
        $paymentDetails->OrderTotal = $orderTotal;

        $DoECRequestDetails = new \DoExpressCheckoutPaymentRequestDetailsType();
        $DoECRequestDetails->PayerID = $payerId;
        $DoECRequestDetails->Token = $token;
        $DoECRequestDetails->PaymentAction = "Sale";
        $DoECRequestDetails->PaymentDetails[0] = $paymentDetails;

        $DoECRequest = new \DoExpressCheckoutPaymentRequestType();
        $DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;

        $DoECReq = new \DoExpressCheckoutPaymentReq();
        $DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;

        try {
            $DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);
        } catch (\Exception $ex) {
            $this->get('logger')->notice("Paywall: Error in DoECResponse validate IPN API call.");
        }

        // Payment done, let's update some registries in the app
        if (isset($DoECResponse) && $DoECResponse->Ack == 'Success') {
            s::set('valid_ipn', 'waiting');

            $paymentInfo = $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0];
            // Do the refund of the transaction
            $refundReqest = new \RefundTransactionRequestType();

            $refundReqest->RefundType = 'Full';
            $refundReqest->TransactionID = $paymentInfo->TransactionID;

            $refundReq = new \RefundTransactionReq();
            $refundReq->RefundTransactionRequest = $refundReqest;
            try {
                /* wrap API method calls on the service object with a try catch */
                $paypalService->RefundTransaction($refundReq);
            } catch (\Exception $ex) {
                $this->get('logger')->notice("Paywall: Error in refundResponse validate IPN API call.");
            }
        } else {
            $this->get('session')->getFlashBag()->add('error', _("Paypal IPN configuration is incorrect. Please correct it and try again."));
        }

        return $this->redirect($this->generateUrl('admin_paywall_settings'));
    }
}
