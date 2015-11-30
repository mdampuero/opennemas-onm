<?php

namespace BackendWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $primary = $instance->domains[$instance->main_domain];

        $domains = [];
        foreach ($instance->domains as $key => $value) {
            $domains[] = [
                'free' => $value === $base,
                'name' => $value,
                'main' => $key == $instance->main_domain
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
     * Returns the information for a domain
     *
     * @param string $id The domain.
     *
     * @return JsonResponse The response object.
     */
    public function showAction($id)
    {
        $info = [
            'expires' => '',
            'target'  => '',
        ];

        exec('dig ' . $id, $output, $code);

        if ($code === 0) {
            $i = 0;

            while ($i < count($output) && strpos($output[$i], $id) !== 0) {
                $i++;
            }

            if ($i < count($output)) {
                $target = str_replace("\t", ' ', $output[$i]);

                $info['target'] = substr($target, strrpos($target, ' ') + 1);
            }
        }

        return new JsonResponse($info);
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
        exec('dig +short ' . $domain, $output, $code);

        if ($code !== 0 || !empty($output)) {
            return false;
        }

        return true;
    }

    /**
     * Checks if a domain is pointing to the right opennemas.net domain.
     *
     * @param string $domain   The domain to check.
     * @param string $expected The expected domain.
     *
     * @return boolean True if the domain is pointing to the right opennemas.net
     *                 domain. Otherwise, returns false.
     */
    private function checkDomainValid($domain, $expected)
    {
        exec('dig +short' . $domain, $output, $code);

        if ($code !== 0) {
            return false;
        }

        foreach ($output as $value) {
            if (strpos($value, $domain) === 0
                && strpos($value, $expected) !== false
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sends an email to the customer.
     *
     * @param array    $billing  The billing information.
     * @param array    $domains  The requested domains.
     * @param Instance $instance The instance.
     */
    private function sendEmailToCustomer($billing, $domains, $instance)
    {
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
                        'billing'  => $billing,
                        'domains'  => $domains,
                        'instance' => $instance
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
     */
    private function sendEmailToSales($billing, $domains, $instance)
    {
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
                        'billing'  => $billing,
                        'domains'  => $domains,
                        'instance' => $instance
                    ]
                ),
                'text/html'
            );

        $this->get('mailer')->send($message);
    }
}
