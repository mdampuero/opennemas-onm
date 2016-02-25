<?php

namespace BackendWebService\Controller;

use Onm\Framework\Controller\Controller;
use Pdp\Parser;
use Pdp\PublicSuffixListManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

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
        $instance = $this->get('instance');

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
        $billing  = $request->request->get('billing');
        $domains  = $request->request->get('domains');
        $create   = $request->request->get('create');
        $instance = $this->get('instance');

        foreach ($billing as $key => $value) {
            $instance->metas['billing_' . $key] = $value;
        }

        $this->get('instance_manager')->persist($instance);

        $this->sendEmailToCustomer($billing, $domains, $instance, $create);
        $this->sendEmailToSales($billing, $domains, $instance, $create);

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
    private function isDomainAvailable($domain)
    {
        return empty($this->getTarget($domain));
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

            $tld = substr($domain, strrpos($domain, '.'));

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
