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
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for paywall module
 *
 * @package Frontend_Controllers
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
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Shows the list of paywall plans available to buy
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showcaseAction(Request $request)
    {
        $settings = s::get('paywall_settings');


        $articleID = $request->query->getDigits('content_id');

        $er = $this->get('entity_repository');
        $content = $er->find('Article', $articleID);

        return $this->render(
            'paywall/showcase.tpl',
            array(
                'settings' => $settings,
                'content'  => $content,
            )
        );
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function preparePaymentAction(Request $request)
    {
        $selectedPlanId = $request->request->filter('plan');

        $paywallSettings = s::get('paywall_settings');

        foreach ($paywallSettings['payment_modes'] as $mode) {
            if ($mode['time'] == $selectedPlanId) {
                $selectedPlan = $mode;
                break;
            }
        }

        // URL to which the buyer's browser is returned after choosing to pay with PayPal
        $returnUrl = $this->generateUrl('frontend_paywall_success_payment', array('user' => $_SESSION['userid']), true);

        // URL to which the buyer is returned if the buyer does not approve the use of PayPal to pay you
        $cancelUrl = $this->generateUrl('frontend_paywall_cancel_payment', array(), true);

        $orderTotal = new \BasicAmountType();
        $orderTotal->currencyID = $paywallSettings['money_unit'];
        $orderTotal->value      = $selectedPlan['price'];

        $taxTotal = new \BasicAmountType();
        $taxTotal->currencyID = $paywallSettings['money_unit'];
        $taxTotal->value      = '0.0';

        $itemDetails = new \PaymentDetailsItemType();
        $itemDetails->Name         = $selectedPlan['description'];
        $itemDetails->Amount       = $orderTotal;
        $itemDetails->Quantity     = '1';
        $itemDetails->ItemCategory = 'Digital';

        $paymentDetails = new \PaymentDetailsType();
        $paymentDetails->PaymentDetailsItem[0] = $itemDetails;
        $paymentDetails->OrderTotal            = $orderTotal;
        $paymentDetails->PaymentAction         = 'Sale';

        // Sum of cost of all items in this order. For digital goods, this field is required.
        $paymentDetails->ItemTotal             = $orderTotal;
        $paymentDetails->TaxTotal              = $taxTotal;

        $setECDetails = new \SetExpressCheckoutRequestDetailsType();
        $setECDetails->PaymentDetails[0] = $paymentDetails;
        $setECDetails->CancelURL         = $cancelUrl;
        $setECDetails->ReturnURL         = $returnUrl;

        // Without shipping address. Required for digital goods
        $setECDetails->ReqConfirmShipping = 0;
        $setECDetails->NoShipping         = 1;

        $setECReqType = new \SetExpressCheckoutRequestType();
        $setECReqType->SetExpressCheckoutRequestDetails = $setECDetails;

        $setECReq = new \SetExpressCheckoutReq();
        $setECReq->SetExpressCheckoutRequest = $setECReqType;

        $paypalService = new \PayPalAPIInterfaceServiceService(array());
        $setECResponse = $paypalService->SetExpressCheckout($setECReq);
        if ($setECResponse->Ack == 'Success') {
            $token = $setECResponse->Token;

            // storing in session to use in DoExpressCheckout
            $_SESSION['paywall_transaction']   = array(
                'plan'  => $selectedPlan,
                'token' => $token,
            );

            return $this->redirect('https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token='.$token);

        } else {
            var_dump($setECResponse);
            $this->get('logger')->notice(
                "Paywall: Error in SetEC API call"
                ."user {$user->id}: ".$e->getMessage()
            );
        }

    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function returnSuccessPaymentAction(Request $request)
    {
        $token = $request->query->get('token');
        $paywallSettings = s::get('paywall_settings');

        $getExpressCheckoutDetailsRequest = new \GetExpressCheckoutDetailsRequestType($token);

        $getExpressCheckoutReq = new \GetExpressCheckoutDetailsReq();
        $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

        $paypalService = new \PayPalAPIInterfaceServiceService();
        try {
            $getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
        } catch (\Exception $ex) {
            $this->get('logger')->notice(
                "Paywall: Error in GetExpressCheckoutDetils API call"
                ."user {$user->id}: ".$e->getMessage()
            );
            exit;
        }
        if (isset($getECResponse)) {
            echo "<table>";
            echo "<tr><td>Ack :</td><td><div id='Ack'>".$getECResponse->Ack."</div> </td></tr>";
            echo "<tr><td>Token :</td><td><div id='Token'>".$getECResponse->GetExpressCheckoutDetailsResponseDetails->Token."</div></td></tr>";
            echo "<tr><td>PayerID :</td><td><div id='PayerID'>".$getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID."</div></td></tr>";
            echo "<tr><td>PayerStatus :</td><td><div id='PayerStatus'>".$getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerStatus."</div></td></tr>";
            echo "</table>";
            echo '<pre>';
            print_r($getECResponse);
            echo '</pre>';
        }

        $payerId = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID;

        $orderTotal = new \BasicAmountType();
        $orderTotal->currencyID = $paywallSettings['money_unit'];
        $orderTotal->value      = $_SESSION['paywall_transaction']['plan']['price'];

        $paymentDetails = new \PaymentDetailsType();
        $paymentDetails->OrderTotal = $orderTotal;

        $DoECRequestDetails = new \DoExpressCheckoutPaymentRequestDetailsType();
        $DoECRequestDetails->PayerID           = $payerId;
        $DoECRequestDetails->Token             = $token;
        $DoECRequestDetails->PaymentAction     = 'Sale';
        $DoECRequestDetails->PaymentDetails[0] = $paymentDetails;

        $DoECRequest = new \DoExpressCheckoutPaymentRequestType();
        $DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;


        $DoECReq = new \DoExpressCheckoutPaymentReq();
        $DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;

        try {
            $DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);
        } catch (Exception $ex) {
            $this->get('logger')->notice(
                "Paywall: Error in DoExpressCheckoutPayment API call"
                ."user {$user->id}: ".$e->getMessage()
            );

            exit;
        }
        if (isset($DoECResponse)) {
            echo "<table>";
            echo "<tr><td>Ack :</td><td><div id='Ack'>$DoECResponse->Ack</div> </td></tr>";
            if(isset($DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo)) {
                echo "<tr><td>TransactionID :</td><td><div id='TransactionID'>". $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID."</div> </td></tr>";
            }
            echo "</table>";
            echo "<pre>";
            print_r($DoECResponse);
            echo "</pre>";
        }

        return $this->render('paywall/payment_success.tpl');
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function returnCancelPaymentAction(Request $request)
    {
        return $this->render('paywall/payment_error.tpl');
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function ipnPaymentAction(Request $request)
    {
        return $this->redirect($this->generateUrl(''));
        var_dump('IPN_PAYMENT_ACTION', $request);die();
    }
}
