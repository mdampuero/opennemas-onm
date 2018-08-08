<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Common\ORM\Entity\Invoice;
use Common\ORM\Entity\Payment;
use Common\ORM\Entity\Purchase;
use Symfony\Component\Intl\Intl;

class CheckoutHelper
{
    /**
     * The current client.
     *
     * @var Client
     */
    protected $client;

    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * Initializes the PurchaseManager.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->instance  = $container->get('core.instance');
        $this->client    = $this->getClient();
    }

    /**
     * Updates the instance basing on the purchased items.
     */
    public function enable()
    {
        if (empty($this->instance->purchased)) {
            $this->instance->purchased = [];
        }

        // Filter items (remove payment line)
        $items = array_filter($this->purchase->details, function ($a) {
            return array_key_exists('uuid', $a);
        });

        $domains    = $this->getDomains($items);
        $extensions = $this->getExtensions($items);
        $themes     = $this->getThemes($items);

        $this->instance->domains =
            array_unique(array_merge($this->instance->domains, $domains));

        $this->instance->activated_modules =
            array_unique(array_merge($this->instance->activated_modules, $extensions));

        $this->instance->purchased =
            array_unique(array_merge($this->instance->purchased, $themes));

        $this->container->get('orm.manager')->persist($this->instance);
    }

    /**
     * Returns a new purchase or the last incompleted purchase.
     *
     * @param integer $id The purchase id.
     *
     * @return Purchase A new purchase or the last incompleted purchase.
     */
    public function getPurchase($id = null)
    {
        if (!empty($this->purchase)) {
            return $this->purchase;
        }

        $this->purchase = new Purchase();

        if (!empty($id)) {
            try {
                $this->purchase = $this->container->get('orm.manager')
                    ->getRepository('Purchase')->find($id);
            } catch (\Exception $e) {
            }
        }

        $date = new \DateTime();

        $this->purchase->created     = $date;
        $this->purchase->instance_id = $this->instance->id;
        $this->purchase->step        = 'start';
        $this->purchase->updated     = $date;

        if (!empty($this->client)) {
            $this->purchase->client    = $this->client;
            $this->purchase->client_id = $this->client->id;
        }

        return $this->purchase;
    }

    /**
     * Adds more information to the current purchase.
     *
     * @param string $step   The current step.
     * @param array  $ids    The list of items and price names.
     * @param array  $params The list of parameters for the items.
     * @param string $method The payment method.
     */
    public function next($step, $ids, $params, $method)
    {
        $em       = $this->container->get('orm.manager');
        $lang     = $this->container->get('core.locale')->getLocaleShort();
        $vatTax   = 0;
        $subtotal = 0;

        $this->purchase->fee     = 0;
        $this->purchase->method  = $method;
        $this->purchase->step    = $step;
        $this->purchase->updated = new \DateTime();

        if (empty($ids)) {
            $em->persist($this->purchase);
            return;
        }

        $oql = sprintf('uuid in ["%s"]', implode('","', array_keys($ids)));

        $items  = $em->getRepository('Extension')->findBy($oql);
        $themes = $em->getRepository('Theme')->findBy($oql);
        $items  = array_merge($items, $themes);

        $this->purchase->details = [];

        if (!empty($this->client)) {
            $vatTax = $this->container->get('vat')
                ->getVatFromCode($this->client->country, $this->client->state);
        }

        $terms = '';
        $notes = '';
        foreach ($items as $item) {
            $terms      .= $item->getTerms($lang) . "\n";
            $notes      .= $item->getNotes($lang) . "\n";
            $uuid        = $item->uuid;
            $description = $item->getName($lang);
            $price       = $item->getPrice($ids[$item->uuid]);
            $subtotal   += $price;

            $line = [
                'uuid'         => $uuid,
                'description'  => $description,
                'unit_cost'    => $price,
                'quantity'     => 1,
                'tax1_name'    => 'IVA',
                'tax1_percent' => $vatTax
            ];

            $this->purchase->details[] = $line;

            if (!empty($params[$uuid])) {
                // Fix descriptions and subtotal for domains
                array_pop($this->purchase->details);
                $subtotal += $price * (count($params[$uuid]) - 1);

                for ($i = 0; $i < count($params[$uuid]); $i++) {
                    $this->purchase->details[$i]                 = $line;
                    $this->purchase->details[$i]['description'] .= ': '
                        . $params[$uuid][$i];
                }
            }
        }

        $this->purchase->notes = trim($notes, "\n");
        $this->purchase->terms = trim($terms, "\n");
        $this->purchase->fee   = 0;

        if ($subtotal > 0) {
            $this->purchase->fee = round($subtotal * 0.029 + 0.30, 2);
        }

        $subtotal += $this->purchase->fee;

        $this->purchase->details[] = [
            'description'  => $this->purchase->method === 'CreditCard' ?
                _('Pay with credit card') : _('Pay via PayPal'),
            'unit_cost'    => str_replace(
                ',',
                '.',
                (string) round($this->purchase->fee, 2)
            ),
            'quantity'     => 1,
            'tax1_name'    => 'IVA',
            'tax1_percent' => $vatTax
        ];

        $vat = round(($vatTax / 100) * $subtotal, 2);

        $this->purchase->total = round($subtotal + $vat, 2);

        $em->persist($this->purchase);
    }

    /**
     * Pays a purchase by using a payment method nonce.
     *
     * @param string $nonce The payment method nonce.
     */
    public function pay($nonce)
    {
        $em   = $this->container->get('orm.manager');
        $date = new \DateTime();

        if (empty($this->client)) {
            throw new \Exception(_('There is no billing information.'));
        }

        $this->purchase->step    = 'apply';
        $this->purchase->updated = $date;

        $payment = new Payment([
            'client_id' => $this->client->id,
            'amount'    => $this->purchase->total,
            'date'      => $date,
            'type'      => $this->purchase->method
        ]);

        // Save in Braintree
        $payment->nonce = $nonce;
        $em->persist($payment, 'braintree');

        // Save Braintree payment id
        $this->purchase->payment_id = $payment->id;
        $this->container->get('orm.manager')->persist($this->purchase);

        $invoice = new Invoice([
            'client_id' => $this->client->id,
            'date'      => $date,
            'status'    => 'sent',
            'lines'     => $this->purchase->details,
            'notes'     => $this->purchase->notes,
            'terms'     => $this->purchase->terms
        ]);

        // Save invoice in FreshBooks
        $em->persist($invoice, 'freshbooks');

        // Save FreshBooks invoice id
        $this->purchase->invoice_id = $invoice->id;
        $this->container->get('orm.manager')->persist($this->purchase);

        // Pay invoice in FreshBooks
        $payment->invoice_id = $invoice->id;
        $payment->notes      = 'Braintree Transaction Id: ' . $payment->id;

        $em->persist($payment, 'freshbooks');

        $em->persist($this->purchase);
    }

    /**
     * Returns the client for the current instance.
     *
     * @return mixed The client for the current instance or null.
     */
    public function getClient()
    {
        if (empty($this->client) && !empty($this->instance->getClient())) {
            $this->client = $this->container->get('orm.manager')
                ->getRepository('Client')->find($this->instance->getClient());
        }

        return $this->client;
    }

    /**
     * Sends an email to the customer.
     */
    public function sendEmailToClient()
    {
        $params = $this->container->getParameter('manager_webservice');
        $items  = array_filter($this->purchase->details, function ($a) {
            return array_key_exists('uuid', $a);
        });

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Store purchase request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->client->email)
            ->setBody(
                $this->container->get('view')->fetch(
                    'store/email/_purchaseToCustomer.tpl',
                    [
                        'instance' => $this->instance,
                        'items'    => $items,
                        'purchase' => $this->purchase,
                    ]
                ),
                'text/html'
            );

        $headers = $message->getHeaders();
        $headers->addParameterizedHeader(
            'ACUMBAMAIL-SMTPAPI',
            $this->container->get('core.instance')->internal_name
                . ' - Purchase mail to client'
        );

        if ($this->instance->contact_mail !== $this->client->email) {
            $message->setBcc($this->instance->contact_mail);
        }

        $this->container->get('mailer')->send($message);
    }

    /**
     * Sends an email to sales department.
     */
    public function sendEmailToSales()
    {
        $lang      = $this->container->get('core.locale')->getLocaleShort();
        $params    = $this->container->getParameter('manager_webservice');
        $countries = Intl::getRegionBundle() ->getCountryNames($lang);
        $items     = array_filter($this->purchase->details, function ($a) {
            return array_key_exists('uuid', $a);
        });

        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Store purchase request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->container->getParameter('sales_email'))
            ->setBody(
                $this->container->get('view')->fetch(
                    'store/email/_purchaseToSales.tpl',
                    [
                        'countries' => $countries,
                        'client'    => $this->client,
                        'instance'  => $this->instance,
                        'items'     => $items,
                        'user'      => $this->container->get('core.user')
                    ]
                ),
                'text/html'
            );

        $headers = $message->getHeaders();
        $headers->addParameterizedHeader(
            'ACUMBAMAIL-SMTPAPI',
            $this->container->get('core.instance')->internal_name
                . ' - Purchase mail to sales'
        );

        $this->container->get('mailer')->send($message);
    }

    /**
     * Returns the list of URLs to add to the instance.
     *
     * @param array $items The list of purchased items.
     *
     * @return array The list of URLs to add to the instance.
     */
    protected function getDomains($items)
    {
        $domains = array_filter($items, function ($a) {
            return strpos($a['uuid'], 'es.openhost.domain') !== false;
        });

        return array_map(function ($a) {
            return trim(substr($a['description'], strrpos($a['description'], ' ')));
        }, $domains);
    }

    /**
     * Returns the list of extensions to add to the instance.
     *
     * @param array $items The list of purchased items.
     *
     * @return array The list of extensions to add to the instance.
     */
    protected function getExtensions($items)
    {
        $extensions = array_filter($items, function ($a) {
            return strpos($a['uuid'], 'es.openhost.domain') === false
                && strpos($a['uuid'], 'es.openhost.theme') === false;
        });

        $extensions = array_map(function ($a) {
            return $a['uuid'];
        }, $extensions);

        // Find extensions included in purchased items
        $oql = sprintf(
            'uuid in ["%s"] and modules_included !is null',
            implode('", "', $extensions)
        );

        $includes = $this->container->get('orm.manager')
            ->getRepository('Extension')
            ->findBy($oql);

        $uuids = [];
        foreach ($includes as $extension) {
            $uuids[]    = $extension->uuid;
            $extensions = array_merge($extensions, $extension->modules_included);
        }

        // Exclude packs
        $extensions = array_diff($extensions, $uuids);

        return $extensions;
    }

    /**
     * Returns the list of themes to add to the instance.
     *
     * @param array $items The list of purchased items.
     *
     * @return array The list of themes to add to the instance.
     */
    protected function getThemes($items)
    {
        $themes = array_filter($items, function ($a) {
            return strpos($a['uuid'], 'es.openhost.theme') !== false;
        });

        return array_map(function ($a) {
            return $a['uuid'];
        }, $themes);
    }
}
