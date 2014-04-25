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
namespace Frontend\Controller;

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
        if (empty($_SESSION['userid'])) {
            return $this->redirect($this->generateUrl('frontend_auth_login'));
        } elseif (isset($_SESSION['meta']['paywall_time_limit'])) {
            $currentTime = new \DateTime();
            $currentTime->setTimezone(new \DateTimeZone('UTC'));
            $currentTime = $currentTime->format('Y-m-d H:i:s');

            if ($currentTime < $_SESSION['meta']['paywall_time_limit']) {
                $user = new \User($_SESSION['userid']);
                $user->getMeta();

                m::add(
                    sprintf(
                        _('You already have an active Subscription till: %s'),
                        $_SESSION['meta']['paywall_time_limit']
                    ),
                    m::ERROR
                );

                return $this->redirect(
                    $this->generateUrl('frontend_user_show').'#subscription'
                );
            }
        }
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
        if (!array_key_exists('userid', $_SESSION)) {
            return $this->redirect($this->generateUrl('frontend_auth_login'));
        }

        $selectedPlanId   = $request->query->filter('plan');
        $recurringPayment = $request->query->filter('recurring');

        if (empty($selectedPlanId)) {
            return $this->redirect($this->generateUrl('frontend_paywall_showcase'));
        }

        $paywallSettings = s::get('paywall_settings');

        foreach ($paywallSettings['payment_modes'] as $mode) {
            if ($mode['time'] == $selectedPlanId) {
                $selectedPlan = $mode;
                break;
            }
        }

        // URL to which the buyer's browser is returned after choosing to pay with PayPal
        if ($recurringPayment == '1') {
            $returnUrl = $this->generateUrl('frontend_paywall_success_recurring_payment', array('user' => $_SESSION['userid']), true);
        } else {
            $returnUrl = $this->generateUrl('frontend_paywall_success_payment', array('user' => $_SESSION['userid']), true);
        }
        $cancelUrl = $this->generateUrl('frontend_paywall_cancel_payment', array(), true);

        // Total costs of this operation
        $orderTotalAmount = (int) $selectedPlan['price'];
        $orderTotal = new \BasicAmountType($paywallSettings['money_unit'], $orderTotalAmount);

        $taxesTotal = 0; //(int) $selectedPlan['price'] *($paywallSettings['vat_percentage']/100);
        $taxTotal = new \BasicAmountType($paywallSettings['money_unit'], $taxesTotal);

        // Information about the products to buy
        $itemDetails = new \PaymentDetailsItemType();
        $itemDetails->Name         = $selectedPlan['description'];
        $itemDetails->Amount       = $orderTotal;
        $itemDetails->Quantity     = '1';

        // Complete informatin about the buy
        $paymentDetails = new \PaymentDetailsType();
        $paymentDetails->PaymentDetailsItem[0] = $itemDetails;
        $paymentDetails->PaymentAction         = 'Sale';
        $paymentDetails->OrderTotal            = new \BasicAmountType(
            $paywallSettings['money_unit'],
            $orderTotalAmount + $taxesTotal
        );
        $paymentDetails->ItemTotal             = $orderTotal;
        $paymentDetails->TaxTotal              = $taxTotal;

        // Information about the purchase
        $setECDetails = new \SetExpressCheckoutRequestDetailsType();
        $setECDetails->PaymentDetails[0] = $paymentDetails;
        $setECDetails->CancelURL         = $cancelUrl;
        $setECDetails->ReturnURL         = $returnUrl;
        $setECDetails->ReqConfirmShipping = 0; // no shipping
        $setECDetails->NoShipping         = 1; // no shipping
        $setECDetails->BrandName          = s::get('site_name');
        if ($recurringPayment == '1') {
            // Billing agreement details
            $billingAgreementDetails = new \BillingAgreementDetailsType('RecurringPayments');
            $billingAgreementDetails->BillingAgreementDescription = $selectedPlan['description'];
            $setECDetails->BillingAgreementDetails = array($billingAgreementDetails);
        }


        $setECReqType = new \SetExpressCheckoutRequestType();
        $setECReqType->SetExpressCheckoutRequestDetails = $setECDetails;

        $setECReq = new \SetExpressCheckoutReq();
        $setECReq->SetExpressCheckoutRequest = $setECReqType;

        // Perform the paypal API call
        $paypalWrapper = $this->getPaypalService();
        $paypalService = $paypalWrapper->getMerchantService();

        $setECResponse = $paypalService->SetExpressCheckout($setECReq);

        if ($setECResponse->Ack == 'Success') {
            $token = $setECResponse->Token;

            // storing in session to use in DoExpressCheckout
            $_SESSION['paywall_transaction']   = array(
                'plan'  => $selectedPlan,
                'token' => $token,
            );

            $paypalUrl = $paypalWrapper->getServiceUrl().'&token='.$token;
            return $this->redirect($paypalUrl);

        } else {
            $errors = array();

            foreach ($setECResponse->Errors as $error) {
                $errors []= "[{$error->ErrorCode}] {$error->ShortMessage} | {$error->LongMessage}";
            }
            $this->get('logger')->notice(
                "Paywall: Error in SetEC API call. Original errors: ".implode(' ;', $errors)
            );

            return $this->render(
                'paywall/payment_error.tpl',
                array(
                    'settings' => $paywallSettings
                )
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

        // Some sanity checks before continue with the payment
        if (!array_key_exists('paywall_transaction', $_SESSION)
            || $token != $_SESSION['paywall_transaction']['token']
        ) {
            return $this->render(
                'paywall/payment_error.tpl',
                array(
                    'settings' => $paywallSettings
                )
            );
        }

        $getExpressCheckoutDetailsRequest = new \GetExpressCheckoutDetailsRequestType($token);

        $getExpressCheckoutReq = new \GetExpressCheckoutDetailsReq();
        $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

        // Perform the paypal API call
        $paypalWrapper = $this->getPaypalService();
        $paypalService = $paypalWrapper->getMerchantService();

        try {
            $getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
        } catch (\Exception $ex) {
            $errors = array();

            foreach ($getECResponse->Errors as $error) {
                $errors []= "[{$error->ErrorCode}] {$error->ShortMessage} | {$error->LongMessage}";
            }
            $this->get('logger')->notice(
                "Paywall: Error in GetExpresCheckoutDetails API call. Original errors: ".implode(' ;', $errors)
            );

            return $this->render(
                'paywall/payment_error.tpl',
                array(
                    'settings' => $paywallSettings
                )
            );
        }

        // if (isset($getECResponse)) {
        //     echo "<table>";
        //     echo "<tr><td>Ack :</td><td><div id='Ack'>".$getECResponse->Ack."</div> </td></tr>";
        //     echo "<tr><td>Token :</td><td><div id='Token'>"
        //     .$getECResponse->GetExpressCheckoutDetailsResponseDetails->Token."</div></td></tr>";
        //     echo "<tr><td>PayerID :</td><td><div id='PayerID'>"
        //     .$getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID."</div></td></tr>";
        //     echo "<tr><td>PayerStatus :</td><td><div id='PayerStatus'>"
        //     .$getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerStatus."</div></td></tr>";
        //     echo "</table>";
        //     echo '<pre>';
        //     print_r($getECResponse);
        //     echo '</pre>';
        // }

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
        } catch (\Exception $ex) {
            $errors = array();

            foreach ($DoECResponse->Errors as $error) {
                $errors []= "[{$error->ErrorCode}] {$error->ShortMessage} | {$error->LongMessage}";
            }
            $this->get('logger')->notice(
                "Paywall: Error in DoExpressCheckoutPayment API call. Original errors: ".implode(' ;', $errors)
            );

            return $this->render(
                'paywall/payment_error.tpl',
                array(
                    'settings' => $paywallSettings
                )
            );
        }

        $paymentInfo = $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0];

        // Payment done, let's update some registries in the app
        if (isset($DoECResponse) && $DoECResponse->Ack == 'Success') {

            $order = new \Order();
            $order->create(
                array(
                    'user_id'        => $_SESSION['userid'],
                    'content_id'     => 0,
                    'created'        => new \DateTime(),
                    'payment_id'     => $paymentInfo->TransactionID,
                    'payment_status' => $paymentInfo->PaymentStatus,
                    'payment_amount' => (int) $paymentInfo->GrossAmount->value,
                    'payment_method' => $paymentInfo->TransactionType,
                    'type'           => 'paywall',
                    'params'         => array(
                        ''
                    ),
                )
            );

            $planTime = strtolower($_SESSION['paywall_transaction']['plan']['time']);

            $newUserSubscriptionDate = new \DateTime();
            $newUserSubscriptionDate->setTimezone(new \DateTimeZone('UTC'));
            $newUserSubscriptionDate->modify("+1 {$planTime}");

            $user = new \User($_SESSION['userid']);

            $user->addSubscriptionLimit($newUserSubscriptionDate);

            $_SESSION['meta'] = $user->getMeta();
            unset($_SESSION['paywall_transaction']);

            return $this->render('paywall/payment_success.tpl', array('time' => $newUserSubscriptionDate));
        } elseif ($DoECResponse->Errors[0]->ErrorCode == '11607') {
            $message = _('Your payment was already registered');
        }

        return $this->render(
            'paywall/payment_error.tpl',
            array(
                'settings' => $paywallSettings,
                'message'  => $message,
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
    public function returnSuccessRecurringPaymentAction(Request $request)
    {
        $token = $request->query->get('token');
        $paywallSettings = s::get('paywall_settings');

        // Some sanity checks before continue with the payment
        if (!array_key_exists('paywall_transaction', $_SESSION)
            || $token != $_SESSION['paywall_transaction']['token']
        ) {
            return $this->render(
                'paywall/payment_error.tpl',
                array(
                    'settings' => $paywallSettings
                )
            );
        }

        $getExpressCheckoutDetailsRequest = new \GetExpressCheckoutDetailsRequestType($token);

        $getExpressCheckoutReq = new \GetExpressCheckoutDetailsReq();
        $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

        // Perform the paypal API call
        $paypalWrapper = $this->getPaypalService();
        $paypalService = $paypalWrapper->getMerchantService();

        try {
            $getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
        } catch (\Exception $ex) {
            $errors = array();

            foreach ($getECResponse->Errors as $error) {
                $errors []= "[{$error->ErrorCode}] {$error->ShortMessage} | {$error->LongMessage}";
            }
            $this->get('logger')->notice(
                "Paywall: Error in GetExpresCheckoutDetails API call. Original errors: ".implode(' ;', $errors)
            );

            return $this->render(
                'paywall/payment_error.tpl',
                array(
                    'settings' => $paywallSettings
                )
            );
        }

        // Some transaction data
        $payerId         = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID;
        $payerName       = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerName->FirstName;
        $payerLastName   = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerName->LastName;
        $currencyID      = $paywallSettings['money_unit'];
        $price           = $_SESSION['paywall_transaction']['plan']['price'];
        $planDescription = $_SESSION['paywall_transaction']['plan']['description'];
        $planPeriod      = strtolower($_SESSION['paywall_transaction']['plan']['time']);

        // Check if is an activation
        if (isset($_SESSION['paywall_transaction']['plan']['paywallTimeLimit'])) {
            $timeLimit = $_SESSION['paywall_transaction']['plan']['paywallTimeLimit'];
        } else {
            $timeLimit = false;
        }

        // Must be a valid date, in UTC/GMT format; for example, 2012-08-24T05:38:48Z
        if ($timeLimit) {
            // if is an reactivation
            $billingStartDate = gmdate('c', strtotime($timeLimit));
        } else {
            $billingStartDate = gmdate('c', strtotime("+1 {$planPeriod}"));
        }

        $RPProfileDetails = new \RecurringPaymentsProfileDetailsType();
        $RPProfileDetails->SubscriberName = $payerName.' '.$payerLastName;
        $RPProfileDetails->ProfileReference = $_SESSION['userid'];
        $RPProfileDetails->BillingStartDate = $billingStartDate;

        // Initial non-recurring payment amount due immediately upon profile creation. Use an initial amount for enrolment or set-up fees.
        $activationDetails = new \ActivationDetailsType();
        if (!$timeLimit) {
            // if is not an activation charge initial amount
            $activationDetails->InitialAmount = new \BasicAmountType($currencyID, $price);
        }

        // CancelOnFailure – If this field is not set or you set it to CancelOnFailure, PayPal creates the recurring payment profile,
        // but places it into a pending status until the initial payment completes.
        // If the initial payment clears, PayPal notifies you by IPN that the pending profile has been activated.
        // If the payment fails, PayPal notifies you by IPN that the pending profile has been canceled.
        $activationDetails->FailedInitialAmountAction = 'CancelOnFailure';

        // Regular payment period for this schedule which takes mandatory params
        $paymentBillingPeriod =  new \BillingPeriodDetailsType();
        $paymentBillingPeriod->BillingFrequency = '1';
        $paymentBillingPeriod->BillingPeriod = $_SESSION['paywall_transaction']['plan']['time'];
        $paymentBillingPeriod->Amount = new \BasicAmountType($currencyID, $price);

        // Describes the recurring payments schedule, including the regular
        // payment period, whether there is a trial period, and the number of
        // payments that can fail before a profile is suspended which takes
        // mandatory params
        $scheduleDetails = new \ScheduleDetailsType();
        $scheduleDetails->Description = $planDescription;
        $scheduleDetails->ActivationDetails = $activationDetails;
        $scheduleDetails->PaymentPeriod = $paymentBillingPeriod;

        // CreateRecurringPaymentsProfileRequestDetailsType which takes mandatory params
        $createRPProfileRequestDetail = new \CreateRecurringPaymentsProfileRequestDetailsType();
        $createRPProfileRequestDetail->Token  = $token;
        $createRPProfileRequestDetail->ScheduleDetails = $scheduleDetails;
        $createRPProfileRequestDetail->RecurringPaymentsProfileDetails = $RPProfileDetails;

        $createRPProfileRequest = new \CreateRecurringPaymentsProfileRequestType();
        $createRPProfileRequest->CreateRecurringPaymentsProfileRequestDetails = $createRPProfileRequestDetail;

        $createRPProfileReq =  new \CreateRecurringPaymentsProfileReq();
        $createRPProfileReq->CreateRecurringPaymentsProfileRequest = $createRPProfileRequest;

        try {
            /* wrap API method calls on the service object with a try catch */
            $createRPProfileResponse = $paypalService->CreateRecurringPaymentsProfile($createRPProfileReq);
        } catch (\Exception $ex) {
            $errors = array();

            foreach ($createRPProfileResponse->Errors as $error) {
                $errors []= "[{$error->ErrorCode}] {$error->ShortMessage} | {$error->LongMessage}";
            }
            $this->get('logger')->notice(
                "Paywall: Error in CreateRecurringPaymentsProfile API call. Original errors: ".implode(' ;', $errors)
            );

            return $this->render(
                'paywall/payment_error.tpl',
                array(
                    'settings' => $paywallSettings
                )
            );
        }

        // if(isset($createRPProfileResponse)) {
        //     echo "<table>";
        //     echo "<tr><td>Ack :</td><td><div id='Ack'>$createRPProfileResponse->Ack</div> </td></tr>";
        //     echo "<tr><td>ProfileID :</td><td><div id='ProfileID'>".$createRPProfileResponse->CreateRecurringPaymentsProfileResponseDetails->ProfileID ."</div> </td></tr>";
        //     echo "</table>";

        //     echo "<pre>";
        //     print_r($createRPProfileResponse);
        //     echo "</pre>";
        // }


        // Profile created
        if (isset($createRPProfileResponse) && $createRPProfileResponse->Ack == 'Success') {
            // Redirect user to success page
            return $this->render('paywall/profile_created.tpl');
        } else {
            $message = _('Your subscription could not been created. Please try again.');
        }

        return $this->render(
            'paywall/payment_error.tpl',
            array(
                'settings' => $paywallSettings,
                'message'  => $message,
            )
        );
    }

    /**
     * Cancel the active recurring payment for paywall subscription
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function cancelRecurringPaymentAction(Request $request)
    {
        // Get recurring profile ID for this user
        $user = new \User($_SESSION['userid']);
        $recurringProfileId = $user->getMeta('recurring_payment_id');
        if (!$recurringProfileId) {
            return false;
        }

        // The ManageRecurringPaymentsProfileStatus API operation cancels, suspends, or reactivates a recurring payments profile.
        // Cancel – Only profiles in Active or Suspended state can be canceled.
        // Suspend – Only profiles in Active state can be suspended.
        // Reactivate – Only profiles in a suspended state can be reactivated.
        $manageRPPStatusReqestDetails = new \ManageRecurringPaymentsProfileStatusRequestDetailsType();
        $manageRPPStatusReqestDetails->Action =  'Cancel';
        $manageRPPStatusReqestDetails->ProfileID =  $recurringProfileId;

        $manageRPPStatusReqest = new \ManageRecurringPaymentsProfileStatusRequestType();
        $manageRPPStatusReqest->ManageRecurringPaymentsProfileStatusRequestDetails = $manageRPPStatusReqestDetails;

        $manageRPPStatusReq = new \ManageRecurringPaymentsProfileStatusReq();
        $manageRPPStatusReq->ManageRecurringPaymentsProfileStatusRequest = $manageRPPStatusReqest;

        // Perform the paypal API call
        $paypalWrapper = $this->getPaypalService();
        $paypalService = $paypalWrapper->getMerchantService();

        try {
            /* wrap API method calls on the service object with a try catch */
            $manageRPPStatusResponse = $paypalService->ManageRecurringPaymentsProfileStatus($manageRPPStatusReq);
        } catch (\Exception $ex) {
            $errors = array();

            foreach ($manageRPPStatusResponse->Errors as $error) {
                $errors []= "[{$error->ErrorCode}] {$error->ShortMessage} | {$error->LongMessage}";
            }
            $this->get('logger')->notice(
                "Paywall: Error in ManageRecurringPaymentsProfileStatus API call. Original errors: ".implode(' ;', $errors)
            );
        }

        // Check if Profile was canceled
        if (isset($manageRPPStatusResponse) && $manageRPPStatusResponse->Ack == 'Success') {
            $user->setMeta(array('canceled_recurring_payment_id' => $recurringProfileId));
            $error = false;
        } else {
            $error = true;
        }

        return $this->render(
            'paywall/profile_canceled.tpl',
            array(
                'error'  => $error,
            )
        );
    }

    /**
     * Activate the canceled recurring payment for paywall subscription
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function activateRecurringPaymentAction(Request $request)
    {
        // Get recurring profile ID for this user
        $user = new \User($_SESSION['userid']);
        $recurringProfileId = $user->getMeta('recurring_payment_id');
        if (!$recurringProfileId) {
            return false;
        }

        // Obtain information about a recurring payments profile.
        $getRPPDetailsReqest = new \GetRecurringPaymentsProfileDetailsRequestType();
        $getRPPDetailsReqest->ProfileID = $recurringProfileId;

        $getRPPDetailsReq = new \GetRecurringPaymentsProfileDetailsReq();
        $getRPPDetailsReq->GetRecurringPaymentsProfileDetailsRequest = $getRPPDetailsReqest;

        // Perform the paypal API call
        $paypalWrapper = $this->getPaypalService();
        $paypalService = $paypalWrapper->getMerchantService();

        try {
            /* wrap API method calls on the service object with a try catch */
            $getRPPDetailsResponse = $paypalService->GetRecurringPaymentsProfileDetails($getRPPDetailsReq);
        } catch (\Exception $ex) {
            $errors = array();

            foreach ($getRPPDetailsResponse->Errors as $error) {
                $errors []= "[{$error->ErrorCode}] {$error->ShortMessage} | {$error->LongMessage}";
            }
            $this->get('logger')->notice(
                "Paywall: Error in GetRecurringPaymentsProfileDetails API call. Original errors: ".implode(' ;', $errors)
            );
        }

        // if(isset($getRPPDetailsResponse)) {
        //     echo "<table>";
        //     echo "<tr><td>Ack :</td><td><div id='Ack'>$getRPPDetailsResponse->Ack</div> </td></tr>";
        //     echo "<tr><td>ProfileID :</td><td><div id='ProfileID'>". $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileID ."</div> </td></tr>";
        //     echo "</table>";

        //     echo "<pre>";
        //     print_r($getRPPDetailsResponse);
        //     echo "</pre>";
        // }

        if (isset($getRPPDetailsResponse) && $getRPPDetailsResponse->Ack == 'Success') {
            // Fetch paywall settings
            $paywallSettings = s::get('paywall_settings');

            // Set some values from response
            $price = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->CurrentRecurringPaymentsPeriod->Amount->value;
            $period = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->CurrentRecurringPaymentsPeriod->BillingPeriod;
            $description = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->Description;

            // Get user next day of payment
            $paywallTimeLimit = $user->getMeta('paywall_time_limit');

            // Set selectedPlan user when creating the recurring profile
            $selectedPlan = array(
                'price' => $price,
                'description' => $description,
                'time' => $period,
                'paywallTimeLimit' => $paywallTimeLimit,
            );

            // URL to which the buyer's browser is returned after choosing to pay with PayPal
            $returnUrl = $this->generateUrl(
                'frontend_paywall_success_recurring_payment',
                array('user' => $_SESSION['userid'], ),
                true
            );
            $cancelUrl = $this->generateUrl('frontend_paywall_cancel_payment', array(), true);

            // Total costs of this operation
            $orderTotalAmount = (int) $price;
            $orderTotal = new \BasicAmountType($paywallSettings['money_unit'], $orderTotalAmount);

            $taxesTotal = 0; //(int) $selectedPlan['price'] *($paywallSettings['vat_percentage']/100);
            $taxTotal = new \BasicAmountType($paywallSettings['money_unit'], $taxesTotal);

            // Information about the products to buy
            $itemDetails = new \PaymentDetailsItemType();
            $itemDetails->Name         = $description;
            $itemDetails->Amount       = $orderTotal;
            $itemDetails->Quantity     = '1';

            // Complete informatin about the buy
            $paymentDetails = new \PaymentDetailsType();
            $paymentDetails->PaymentDetailsItem[0] = $itemDetails;
            $paymentDetails->PaymentAction         = 'Sale';
            $paymentDetails->OrderTotal            = new \BasicAmountType(
                $paywallSettings['money_unit'],
                $orderTotalAmount + $taxesTotal
            );
            $paymentDetails->ItemTotal             = $orderTotal;
            $paymentDetails->TaxTotal              = $taxTotal;

            // Information about the purchase
            $setECDetails = new \SetExpressCheckoutRequestDetailsType();
            $setECDetails->PaymentDetails[0] = $paymentDetails;
            $setECDetails->CancelURL         = $cancelUrl;
            $setECDetails->ReturnURL         = $returnUrl;
            $setECDetails->ReqConfirmShipping = 0; // no shipping
            $setECDetails->NoShipping         = 1; // no shipping
            $setECDetails->BrandName          = s::get('site_name');

            // Billing agreement details
            $billingAgreementDetails = new \BillingAgreementDetailsType('RecurringPayments');
            $billingAgreementDetails->BillingAgreementDescription = $description;
            $setECDetails->BillingAgreementDetails = array($billingAgreementDetails);

            $setECReqType = new \SetExpressCheckoutRequestType();
            $setECReqType->SetExpressCheckoutRequestDetails = $setECDetails;

            $setECReq = new \SetExpressCheckoutReq();
            $setECReq->SetExpressCheckoutRequest = $setECReqType;

            // Perform the paypal API call
            $paypalWrapper = $this->getPaypalService();
            $paypalService = $paypalWrapper->getMerchantService();

            $setECResponse = $paypalService->SetExpressCheckout($setECReq);

            if ($setECResponse->Ack == 'Success') {
                $token = $setECResponse->Token;

                $_SESSION['paywall_transaction']   = array(
                    'plan'  => $selectedPlan,
                    'token' => $token,
                );

                $paypalUrl = $paypalWrapper->getServiceUrl().'&token='.$token;
                return $this->redirect($paypalUrl);

            } else {
                $errors = array();

                foreach ($setECResponse->Errors as $error) {
                    $errors []= "[{$error->ErrorCode}] {$error->ShortMessage} | {$error->LongMessage}";
                }
                $this->get('logger')->notice(
                    "Paywall: Error in SetEC API call. Original errors: ".implode(' ;', $errors)
                );

                return $this->render(
                    'paywall/prifile_activated.tpl',
                    array(
                        'error' => true,
                    )
                );
            }
        } else {

            return $this->render(
                'paywall/prifile_activated.tpl',
                array(
                    'error' => true,
                )
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
    public function returnCancelPaymentAction(Request $request)
    {
        $paywallSettings = s::get('paywall_settings');

        return $this->render(
            'paywall/payment_error.tpl',
            array(
                'settings' => $paywallSettings
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
    public function ipnPaymentAction(Request $request)
    {
        return $this->redirect($this->generateUrl(''));
        // var_dump('IPN_PAYMENT_ACTION', $request);die();
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    private function getPaypalService()
    {
        $settings = array();

        $databaseSettings = s::get('paywall_settings');
        $settings = array(
            "acct1.UserName"  => $databaseSettings['paypal_username'],
            "acct1.Password"  => $databaseSettings['paypal_password'],
            "acct1.Signature" => $databaseSettings['paypal_signature'],
            "mode"            => ($databaseSettings['developer_mode'] == false) ? 'sandbox' : 'live',
        );

        return  new \Onm\Merchant\PaypalWrapper($settings);
    }
}
