<?php

namespace BackendWebService\Controller;

use Common\ORM\Entity\Client;
use Common\ORM\Entity\Invoice;
use Common\ORM\Entity\Payment;
use Common\ORM\Entity\Purchase;
use Onm\Framework\Controller\Controller;
use Pdp\Parser;
use Pdp\PublicSuffixListManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

class DomainController extends Controller
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
        $create = $request->query->get('create');

        if (empty($domain) || !$this->isTLDValid($domain, $create)) {
            return new JsonResponse(
                sprintf(_('The domain %s is not valid'), $domain),
                400
            );
        }

        if (!$this->isDomainAvailable($domain)) {
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
    public function checkConfiguredAction(Request $request)
    {
        $domain   = $request->query->get('domain');
        $end      = substr($domain, strrpos($domain, '.') + 1);
        $instance = $this->get('core.instance');

        $expected = "{$instance->internal_name}.{$end}.opennemas.net";

        if (empty($domain) || !$this->isDomainValid($domain, $expected)) {
            return new JsonResponse(
                sprintf(_('Your domain has to point to %s'), $expected),
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
        $domain = $request->query->get('domain');
        $create = $request->query->get('create');

        if (empty($domain) || !$this->isTLDValid($domain, $create)) {
            return new JsonResponse(
                sprintf(_('The domain %s is not valid'), $domain),
                400
            );
        }

        return new JsonResponse(_('Your domain is valid'));
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
        $instance = $this->get('core.instance');

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
        $instance = $this->get('core.instance');

        $base    = $instance->internal_name
            . $this->getParameter('opennemas.base_domain');
        $primary = $instance->getMainDomain();

        $domains = [];
        foreach ($instance->domains as $value) {
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
        $purchase  = $request->request->get('purchase');
        $nonce     = $request->request->get('nonce');

        $em = $this->get('orm.manager');

        try {
            $ph = $this->get('core.helper.checkout');

            $ph->getPurchase($purchase);

            if (empty($nonce)) {
                return;
            }

            $ph->pay($nonce);

            $purchase = $ph->getPurchase();
            $items    = $purchase->details;

            // Remove CreditCard entry from purchase details
            if ($purchase->method === 'CreditCard') {
                array_pop($items);
            }

            $ph->sendEmailToCustomer($items);
            $ph->sendEmailToSales($items);

            $ph->enable();

            $this->get('application.log')->info(
                'The user ' . $this->getUser()->username
                . '(' . $this->getUser()->id  .') has purchased '
                . json_encode($purchase->details)
            );
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return new JsonResponse([
                'message' => $e->getMessage(),
                'type' => 'error'
            ], 400);
        }

        return new JsonResponse(_('Purchase completed!'));
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
    private function isDomainAvailable($domain)
    {
        return !checkdnsrr($domain, 'ANY');
    }

    /**
     * Checks if the given domain is valid.
     *
     * @param string $domain The domain to check.
     * @param string $create Whether it is a creation or a redirection.
     *
     * @return boolean True if the TLD is valid. Otherwise, returns false.
     */
    private function isTLDValid($domain, $create = false)
    {
        $pslManager = new PublicSuffixListManager();
        $parser     = new Parser($pslManager->getList());

        if ($create) {
            $tlds = [
                '.com', '.net', '.co.uk', '.es', '.cat', '.ch', '.cz', '.de',
                '.dk', '.at', '.be', '.eu', '.fi', '.fr', '.in', '.info', '.it',
                '.li', '.lt', '.mobi', '.name', '.nl', '.nu', '.org', '.pl',
                '.pro', '.pt', '.re', '.se', '.tel', '.tf', '.us', '.wf', '.yt',
            ];

            $tld = substr($domain, strpos($domain, '.', 4));

            if (!in_array($tld, $tlds)) {
                return false;
            }
        }

        return $parser->isSuffixValid($domain);
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
            return gethostbyname($domain);
        }

        return $output[0]['target'];
    }

    /**
     * Sends an email to the customer.
     *
     * @param array   $client   The client information.
     * @param array   $domains  The requested domains.
     * @param integer $purchase The purchase id.
     */
    private function sendEmailToCustomer($client, $domains, $purchase)
    {
        $instance  = $this->get('core.instance');
        $params    = $this->getParameter('manager_webservice');
        $countries = Intl::getRegionBundle()
            ->getCountryNames(CURRENT_LANGUAGE_LONG);

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Domain request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($client->email)
            ->setBody(
                $this->renderView(
                    'domain_management/email/_purchaseToCustomer.tpl',
                    [
                        'client'    => $client,
                        'countries' => $countries,
                        'domains'   => $domains,
                        'url'       => $this->get('router')->generate(
                            'backend_ws_purchase_get_pdf',
                            [ 'id' => $purchase ],
                            true
                        )
                    ]
                ),
                'text/html'
            );

        if ($instance->contact_mail !== $client->email) {
            $message->setBcc($instance->contact_mail);
        }

        $this->get('mailer')->send($message);
    }

    /**
     * Sends an email to sales department.
     *
     * @param array $client  The client information.
     * @param array $domains The requested domains.
     */
    private function sendEmailToSales($client, $domains)
    {
        $countries = Intl::getRegionBundle()->getCountryNames();
        $instance  = $this->get('core.instance');
        $params    = $this->getParameter("manager_webservice");

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Domain request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->container->getParameter('sales_email'))
            ->setBody(
                $this->renderView(
                    'domain_management/email/_purchaseToSales.tpl',
                    [
                        'client'    => $client,
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
