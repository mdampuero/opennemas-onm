<?php

namespace BackendWebService\Controller;

use Framework\ORM\Entity\Client;
use Framework\ORM\Entity\Payment;
use Framework\ORM\Entity\Purchase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Onm\Framework\Controller\Controller;

class DomainManagementController extends Controller
{
    /**
     * Checks if the domain is not in use.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function checkAvailableAction(Request $request)
    {
        $domain = $request->query->get('domain');

        if (empty($domain) || !$this->checkDomainAvailable($domain)) {
            return new JsonResponse(
                sprintf(_('The domain %s is not available'), $domain),
                400
            );
        }

        return new JsonResponse(_('Your domain is configured correctly'));
    }

    /**
     * Checks if the domain is configured correcty basing on information from
     * dig command.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function checkValidAction(Request $request)
    {
        $domain   = $request->query->get('domain');
        $end      = substr($domain, strrpos($domain, '.') + 1);
        $instance = $this->get('instance');

        $expected = "{$instance->internal_name}.{$end}.opennemas.net";

        if (empty($domain) || !$this->checkDomainValid($domain, $expected)) {
            return new JsonResponse(
                sprintf(_('Your domain has to point to %s'), $expected),
                400
            );
        }

        return new JsonResponse(_('Your domain is configured correctly'));
    }

    /**
     * Deletes an instance domain.
     *
     * @param string $domain The domain to delete.
     *
     * @return JsonResponse The response object
     */
    public function delete($domain)
    {
        $instance = $this->get('instance');

        $index = array_search($instance->domains, $domain);

        if ($index !== false) {
            unset($instance->domains[$index]);
            $this->get('instance_manager')->persist($instance);

            return new JsonResponse(
                sprintf(_('Domain deleted successfully'))
            );
        }

        return new JsonResponse(
            sprintf(_('Unable to delete the domain %s'), $domain),
            400
        );
    }

    /**
     * Returns the list of domains for the current instance.
     *
     * @return JsonResponse The response object.
     */
    public function listAction()
    {
        $instance = $this->get('instance');

        $base    = $instance->internal_name
            . $this->getParameter('opennemas.base_domain');
        $primary = $instance->domains[$instance->main_domain - 1];

        $domains = [];
        foreach ($instance->domains as $key => $value) {
            $domains[] = [
                'free'   => $value === $base,
                'name'   => $value,
                'main'   => $value == $primary,
                'target' => $this->getTarget($value)
            ];
        }

        return new JsonResponse([
            'primary' => $primary,
            'base'    => $base,
            'domains' => $domains,
        ]);
    }

    /**
     * Adds a new domain to the current instance.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveAction(Request $request)
    {
        $domains  = $request->request->get('domains');
        $create   = $request->request->get('create');
        $nonce    = $request->request->get('nonce');
        $instance = $this->get('instance');
        $date     = new \Datetime('now');
        $price    = $create ? 18.00 : 12.00;

        $client = $instance->getClient();

        if (empty($client)) {
            $client = $this->createClient($request->request->get('client'));
        }

        $vatTax = $this->get('vat')->getVatFromCode($client->country);

        $payment = new Payment([
            'client_id' => $client->client_id,
            'amount'    => count($domains) * $price + (count($domains) * ($vatTax / 100) * $price),
            'date'      => $date->format('Y-m-d'),
            'type'      => 'Check'
        ]);

        if (!empty($nonce)) {
            $payment->nonce = $nonce;
        }

        $this->get('orm.manager')->persist($payment, 'Braintree');

        $invoice = new \Framework\ORM\Entity\Invoice([
            'client_id' => $client->client_id,
            'date'      => '2016-02-17',
            'status'    => 'sent',
            'lines'     => [
                'line' => [
                    [
                        'name'      => 'Domain + redirection',
                        'unit_cost' => $price,
                        'quantity'  => 1,
                        'tax1_name'  => 'IVA',
                        'tax1_percent' => $vatTax
                    ]
                ]
            ]
        ]);

        $this->get('orm.manager')->persist($invoice, 'FreshBooks');
        $payment->invoice_id = $invoice->invoice_id;

        $payment->notes = 'Braintree Transaction Id:' . $payment->payment_id;

        $this->get('orm.manager')->persist($payment, 'FreshBooks');

        $order = new Purchase([
            'client'     => $client,
            'payment_id' => $payment->payment_id,
            'invoice_id' => $invoice->invoice_id,
            'created'    => $date->format('Y-m-d H:i:s'),
            'details'    => $invoice->lines,
        ]);

        $this->get('orm.manager')->persist($order);

        $this->sendEmailToCustomer($client->getData(), $domains, $instance, $create);
        $this->sendEmailToSales($client->getData(), $domains, $instance, $create);

        return new JsonResponse(_('Domain added successfully'));
    }

    /**
     * Checks if a domain is available.
     *
     * @param string $domain   The domain to check.
     * @param string $expected The expected domain.
     *
     * @return boolean True if the domain is available to purchase. Otherwise,
     *                 returns false.
     */
    private function checkDomainAvailable($domain)
    {
        return empty($this->getTarget($domain));
    }

    /**
     * Creates the client from the client data.
     *
     * @param array $data The client data.
     *
     * @return Client The client.
     */
    private function createClient($billing)
    {
        $client = new Client($billing);

        $this->get('orm.manager')->persist($client, 'FreshBooks');
        $this->get('orm.manager')->persist($client, 'Braintree');
        $this->get('orm.manager')->persist($client, 'Database');

        $instance = $this->get('instance');
        $instance->metas['client'] = $client->id;
        $this->get('instance_manager')->persist($instance);

        return $client;
    }

    /**
     * Returns the target for the given domain.
     *
     * @param string $domain  The domain to check.
     *
     * @return string The domain or IP where the given domain is pointing to.
     */
    private function getTarget($domain)
    {
        $output = dns_get_record($domain, DNS_CNAME);

        if (empty($output)) {
            return '';
        }

        return $output[0]['target'];
    }

    /**
     * Sends an email to the customer.
     *
     * @param array    $billing  The billing information.
     * @param array    $domains  The requested domains.
     * @param Instance $instance The instance.
     * @param boolean  $create   The creation flag.
     */
    private function sendEmailToCustomer($billing, $domains, $instance, $create)
    {
        $countries = Intl::getRegionBundle()->getCountryNames();

        $params = $this->container
            ->getParameter("manager_webservice");

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Domain mapping request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->getUser()->contact_mail)
            ->setBody(
                $this->renderView(
                    'domain_management/email/_purchaseToCustomer.tpl',
                    [
                        'billing'   => $billing,
                        'create'    => $create,
                        'countries' => $countries,
                        'domains'   => $domains,
                        'instance'  => $instance
                    ]
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
    }

    /**
     * Sends an email to sales department.
     *
     * @param array    $billing  The billing information.
     * @param array    $domains  The requested domains.
     * @param Instance $instance The instance.
     * @param boolean  $create   The creation flag.
     */
    private function sendEmailToSales($billing, $domains, $instance, $create)
    {
        $countries = Intl::getRegionBundle()->getCountryNames();

        $params = $this->container
            ->getParameter("manager_webservice");

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Domain mapping request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->container->getParameter('sales_email'))
            ->setBody(
                $this->renderView(
                    'domain_management/email/_purchaseToSales.tpl',
                    [
                        'billing'   => $billing,
                        'create'    => $create,
                        'countries' => $countries,
                        'domains'   => $domains,
                        'instance'  => $instance
                    ]
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
    }
}
