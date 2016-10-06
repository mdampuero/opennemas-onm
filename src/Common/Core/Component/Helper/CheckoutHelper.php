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
     * @param ServiceContainer $contaienr The service container.
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

        // Get domains
        $domains = array_filter($items, function ($a) {
            return strpos($a['uuid'], 'es.openhost.domain') !== false;
        });

        // Remove domains
        $items = array_diff($items, $domains);
        $items = array_map(function ($a) {
            return $a['uuid'];
        }, $items);

        // Get activable themes
        $themes = array_filter($items, function ($a) {
            return strpos($a, 'es.openhost.theme') !== false;
        });

        // Get activable extensions
        $extensions = array_diff($items, $themes);

        // Get domain URLs
        $domains = array_map(function ($a) {
            return substr($a['description'], strrpos($a['description'], ' '));
        }, $domains);


        $this->instance->domains =
            array_merge($this->instance->domains, $domains);
        $this->instance->activated_modules =
            array_merge($this->instance->activated_modules, $extensions);
        $this->instance->purchased =
            array_merge($this->instance->purchased, $themes);

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

        $date = new \DateTime();

        $this->purchase = new Purchase();

        $this->purchase->created     = $date;
        $this->purchase->instance_id = $this->instance->id;
        $this->purchase->step        = 'start';
        $this->purchase->updated     = $date;

        if (!empty($id)) {
            $this->purchase = $this->container->get('orm.manager')
                ->getRepository('Purchase')->find($id);
        }

        if (!empty($this->client) && empty($this->purchase->client)) {
            $this->purchase->client = $this->client;
        }

        if (!empty($this->client) && empty($this->purchase->client_id)) {
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

        $terms  = '';
        $notes  = '';

        foreach ($items as $item) {
            $terms       .= $item->getTerms($lang) . "\n";
            $notes       .= $item->getNotes($lang) . "\n";
            $uuid         = $item->uuid;
            $description  = $item->getName($lang);
            $price        = $item->getPrice($ids[$item->uuid]);
            $subtotal    += $price;

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
                    $this->purchase->details[$i] = $line;
                    $this->purchase->details[$i]['description'] .= ': '
                        . $params[$uuid][$i];
                }
            }
        }

        $this->purchase->notes = trim("\n", $notes);
        $this->purchase->terms = trim("\n", $terms);

        $vat = ($vatTax/100) * $subtotal;

        if ($this->purchase->method === 'CreditCard') {
            $this->purchase->fee = round($subtotal * 0.029 + 0.30, 2);

            $this->purchase->details[] = [
                'description'  => _('Pay with credit card'),
                'unit_cost'    => str_replace(
                    ',',
                    '.',
                    (string) round($this->purchase->fee, 2)
                ),
                'quantity'     => 1,
                'tax1_name'    => 'IVA',
                'tax1_percent' => 0
            ];
        }

        $this->purchase->total =
            round($subtotal + $vat + $this->purchase->fee, 2);

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
     *
     * @param array $items The purchased items.
     */
    public function sendEmailToCustomer($items)
    {
        $params  = $this->container->getParameter('manager_webservice');
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

        if ($this->instance->contact_mail !== $this->client->email) {
            $message->setBcc($this->instance->contact_mail);
        }

        $this->container->get('mailer')->send($message);
    }

    /**
     * Sends an email to sales department.
     *
     * @param array $items The purchased items.
     */
    public function sendEmailToSales($items)
    {
        $params  = $this->container->getParameter('manager_webservice');
        $message = \Swift_Message::newInstance()
            ->setSubject('Opennemas Store purchase request')
            ->setFrom($params['no_reply_from'])
            ->setSender($params['no_reply_sender'])
            ->setTo($this->container->getParameter('sales_email'))
            ->setBody(
                $this->container->get('view')->fetch(
                    'store/email/_purchaseToSales.tpl',
                    [
                        'client'   => $this->client,
                        'instance' => $this->instance,
                        'items'    => $items,
                        'user'     => $this->container->get('core.user')
                    ]
                ),
                'text/html'
            );

        $this->container->get('mailer')->send($message);
    }
}
