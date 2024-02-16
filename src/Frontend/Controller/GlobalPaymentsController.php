<?php

namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use GlobalPayments\Api\Entities\Address;
use GlobalPayments\Api\Entities\Enums\AddressType;
use GlobalPayments\Api\ServiceConfigs\Gateways\GpEcomConfig;
use GlobalPayments\Api\HostedPaymentConfig;
use GlobalPayments\Api\Entities\HostedPaymentData;
use GlobalPayments\Api\Entities\Enums\HppVersion;
use GlobalPayments\Api\Entities\Exceptions\ApiException;
use GlobalPayments\Api\Services\HostedService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GlobalPaymentsController extends Controller
{
    /**
     * Sends the data of the hosted payments page to the front.
     *
     * @param Request $request the request object.
     *
     * @return JsonResponse The response with the data necessary for the hpp.
     */
    public function sendDataAction(Request $request)
    {
        $params   = $request->query->all();
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('payments');

        $config               = new GpEcomConfig();
        $config->merchantId   = $settings['merchant_id'];
        $config->accountId    = "internet";
        $config->sharedSecret = $settings['shared_secret'];
        $config->serviceUrl   = "https://hpp.addonpayments.com/pay";

        $config->hostedPaymentConfig          = new HostedPaymentConfig();
        $config->hostedPaymentConfig->version = HppVersion::VERSION_2;
        $service                              = new HostedService($config);

        $hostedPaymentData                      = new HostedPaymentData();
        $hostedPaymentData->customerFirstName   = $params['firstname'];
        $hostedPaymentData->customerLastName    = $params['lastname'];
        $hostedPaymentData->customerEmail       = $params['email'];
        $hostedPaymentData->customerPhoneMobile = $params['phone'];
        $hostedPaymentData->addressesMatch      = false;

        $billingAddress                 = new Address();
        $billingAddress->streetAddress1 = $params['address'];
        $billingAddress->city           = $params['council'];
        $billingAddress->postalCode     = $params['postal-code'];
        $billingAddress->country        = $params['country'];

        try {
            $hppJson = $service->charge($settings['amount'])
                ->withCurrency("EUR")
                ->withHostedPaymentData($hostedPaymentData)
                ->withAddress($billingAddress, AddressType::BILLING)
                ->serialize();

            $this->sendEmail($hostedPaymentData);

            return new JsonResponse($hppJson, 200, [], true);
        } catch (ApiException $e) {
            return new JsonResponse(null, 500, false);
        }
    }

    /**
     * Sends an email with payment data to contact email
     *
     * @param HostedPaymentData     $hostedPaymentData the payment data
     *
     * @return int the number of emails sent
     */
    private function sendEmail($hostedPaymentData)
    {
        $appLog       = $this->get('application.log');
        $mailer       = $this->get('mailer');
        $globals      = $this->get('core.globals');
        $settings     = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([ 'site_name', 'contact_email' ]);
        $contactEmail = $settings['contact_email'];
        $siteName     = $settings['site_name'];
        $subject      = 'Nueva colaboración de pago vía Global Payments';
        $text[]       = "Nombre: {$hostedPaymentData->customerFirstName}";
        $text[]       = "Apellidos: {$hostedPaymentData->customerLastName}";
        $text[]       = "Email: {$hostedPaymentData->customerEmail}";
        $text[]       = "Teléfono: {$hostedPaymentData->customerPhoneMobile}";
        $body         = implode("\r\n", $text);

        try {
            $message = \Swift_Message::newInstance();
            $message
                ->setSubject($subject)
                ->setBody($body, 'text/plain')
                ->setFrom([ 'no-reply@postman.opennemas.com' => $siteName])
                ->setSender([ 'no-reply@postman.opennemas.com' => $siteName])
                ->setTo($contactEmail);

            $headers = $message->getHeaders();
            $headers->addParameterizedHeader(
                'ACUMBAMAIL-SMTPAPI',
                $globals->getInstance()->internal_name . ' - Global Payments collaboration'
            );

            $appLog->notice(
                "Email sent. Backend Global Payments payment registered sent (to: " . $contactEmail . ")"
            );
        } catch (\Exception $e) {
            $appLog->notice('Unable to deliver your email: ' . $e->getMessage());

            return 0;
        }

        return ($mailer->send($message)) ? 1 : 0;
    }

    /**
     * Process the response from the payment service and return a message to the user.
     *
     * @param Request $request the request object.
     *
     * @return JsonResponse The response indicating if the transaction was successfull or not.
     */
    public function sendResponseAction(Request $request)
    {
        $settings = getService('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('payments');

        $config               = new GpEcomConfig();
        $config->merchantId   = $settings['merchant_id'];
        $config->accountId    = "internet";
        $config->sharedSecret = $settings['shared_secret'];
        $config->serviceUrl   = "https://hpp.addonpayments.com/pay";

        $service = new HostedService($config);

        $responseJson = $request->request->get('hppResponse');

        $ph = $this->get('core.helper.payment');

        try {
            $response = $service->parseResponse($responseJson, true);
            $url      = $response->responseValues['MERCHANT_RESPONSE_URL'];

            return new RedirectResponse($ph->getRefererUrlWithMessage($url, $response->responseCode), 301);
        } catch (ApiException $e) {
            return new RedirectResponse($ph->getRefererUrlWithMessage($url, $response->responseCode), 301, []);
        } catch (\Exception $e) {
            return new RedirectResponse($ph->getRefererUrlWithMessage($url, 'default'), 301, []);
        }
    }
}
