<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\ORM\Entity\Purchase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * Handles the Purchase resource.
 */
class PurchaseController extends Controller
{
    /**
     * @api {get} /purchases/:id.pdf Get PDF for purchase
     * @apiName GetPurchasePDF
     * @apiGroup Purchase
     *
     * @apiParam {String} id  The purchase id.
     */
    public function getPdfAction($id)
    {
        $em = $this->get('orm.manager');

        $purchase = $em->getRepository('Purchase')->findOneBy([
            'id'          => [ [ 'value' => $id ] ],
            'instance_id' => [ [ 'value' => $this->get('core.instance')->id ] ]
        ]);

        if (!$purchase) {
            throw new \Exception(_('Unable to find the purchase'));
        }

        $pdf = $em->getRepository('invoice', 'FreshBooks')
            ->getPDF($purchase->invoice_id);

        $response = new Response($pdf);

        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    /**
     * @api {post} /purchases Creates a new purchase
     * @apiName CreatePurchase
     * @apiGroup Purchase
     */
    public function saveAction()
    {
        $instance = $this->get('core.instance');
        $em       = $this->get('orm.manager');
        $client   = $instance->getClient();
        $date     = new \DateTime();

        if (!empty($client)) {
            try {
                $client = $em->getRepository('client')->find($client);
            } catch (\Exception $e) {
                $client = null;
            }
        }

        $purchase = new Purchase();
        $purchase->instance_id = $instance->id;
        $purchase->step        = 'cart';
        $purchase->created     = $date;
        $purchase->updated     = $date;

        if (!empty($client)) {
            $purchase->client_id = $client->id;
            $purchase->client    = $client;
        }

        $em->persist($purchase);

        return new JsonResponse($purchase->id);
    }

    /**
     * @api {put} /purchases/:id Updates a purchase
     * @apiName UpdatePurchase
     * @apiGroup Purchase
     */
    public function updateAction(Request $request, $id)
    {
        $em       = $this->get('orm.manager');
        $lang     = $this->get('core.locale')->getLocaleShort();
        $purchase = $em->getRepository('Purchase')->find($id);
        $subtotal = 0;
        $vatTax   = 0;

        if (!empty($this->get('core.instance')->getClient())) {
            $client = $this->get('core.instance')->getClient();
            try {
                $client = $em->getRepository('Client')->find($client);

                $purchase->client_id = $client->id;
                $purchase->client    = $client;

                $vatTax = $this->get('vat')
                    ->getVatFromCode($client->country, $client->state);
            } catch (\Exception $e) {
            }
        }

        $purchase->updated = new \DateTime();
        $purchase->method  = $request->request->get('method', null);
        $purchase->step    = $request->request->get('step', 'cart');
        $purchase->fee     = 0;

        $ids     = $request->request->get('ids', []);
        $domains = $request->get('domains', []);

        if (empty($ids)) {
            $em->persist($purchase);
            return new JsonResponse();
        }

        $oql = sprintf('uuid in ["%s"]', implode('","', array_keys($ids)));

        $items  = $em->getRepository('Extension')->findBy($oql);
        $themes = $em->getRepository('Theme')->findBy($oql);
        $items  = array_merge($items, $themes);

        $purchase->details = [];

        foreach ($items as $item) {
            $uuid         = $item->uuid;
            $description  = $item->getName($lang);
            $price        = $item->getPrice($ids[$item->uuid]);
            $subtotal    += $price;

            if (strpos($ids[$item->uuid], '_custom') !== false) {
                $uuid        .= '.custom';
                $description .= ' (Custom)';
            }

            $line = [
                'uuid'         => $uuid,
                'description'  => $description,
                'unit_cost'    => $price,
                'quantity'     => 1,
                'tax1_name'    => 'IVA',
                'tax1_percent' => $vatTax
            ];

            $purchase->details[] = $line;

            if (!empty($domains)) {
                // Fix descriptions and subtotal
                array_pop($purchase->details);
                $subtotal += $price * (count($domains) - 1);

                for ($i = 0; $i < count($domains); $i++) {
                    $purchase->details[$i] = $line;
                    $purchase->details[$i]['description'] .= ': ' . $domains[$i];
                }
            }
        }

        $vat = ($vatTax/100) * $subtotal;

        if ($purchase->method === 'CreditCard') {
            $purchase->fee = $subtotal * 0.029 + 0.30;

            $purchase->details[] = [
                'description'  => _('Pay with credit card'),
                'unit_cost'    => str_replace(',', '.', (string) round($purchase->fee, 2)),
                'quantity'     => 1,
                'tax1_name'    => 'IVA',
                'tax1_percent' => 0
            ];
        }

        $purchase->total = $subtotal + $vat + $purchase->fee;

        $em->persist($purchase);

        return new JsonResponse();
    }
}
