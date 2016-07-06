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

use Framework\ORM\Entity\Purchase;
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

        $purchase = $em->getRepository('manager.purchase')->findOneBy([
            'id'          => [ [ 'value' => $id ] ],
            'instance_id' => [ [ 'value' => $this->get('instance')->id ] ]
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
        $instance = $this->get('instance');
        $em       = $this->get('orm.manager');
        $client   = $instance->getClient();

        if (!empty($client)) {
            $client = $em->getRepository('manager.client', 'Database')->find($client);
        }

        $purchase = new Purchase();
        $purchase->instance_id = $instance->id;
        $purchase->step        = 'cart';
        $purchase->created     = date('Y-m-d H:i:s');

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
        $purchase = $em->getRepository('manager.purchase')->find($id);
        $vatTax = null;

        if (!empty($this->get('instance')->getClient())) {
            $client = $this->get('instance')->getClient();
            $client = $em->getRepository('manager.client', 'Database')->find($client);

            if (!empty($client)) {
                $purchase->client_id = $client->id;
                $purchase->client    = $client;
            }

            $vatTax = $this->get('vat')->getVatFromCode($purchase->client->country);
        }

        $purchase->updated = date('Y-m-d H:i:s');
        $purchase->method  = $request->request->get('method', null);
        $subtotal          = 0;
        $purchase->step    = $request->request->get('step', 'cart');
        $purchase->fee     = 0;

        $ids = $request->request->get('ids', []);

        if (!empty($ids)) {
            $items = $em->getRepository('manager.extension')->findBy([
                'uuid' => [ [ 'value' => array_keys($ids), 'operator' => 'in' ] ]
            ]);

            $themes = $this->get('orm.loader')->getPlugins();

            $themes = array_filter($themes, function ($a) use ($ids) {
                return in_array($a->uuid, array_keys($ids));
            });

            $items = array_merge($items, $themes);

            $purchase->details = [];
            $i = 0;

            foreach ($items as $item) {
                $subtotal    += $item->getPrice();
                $description  = is_array($item->name) ?
                    $item->name[CURRENT_LANGUAGE_SHORT] : $item->name;

                if (!empty($request->request->get('domains'))) {
                    $description .= ': ' . $request->request->get('domains')[$i];
                }

                $price = $item->getPrice();

                // Fix price for custom themes
                if ($ids[$item->uuid]) {
                    $description .= ' (Custom)';
                    if ($price === 35) {
                        // monthly
                        $price = 350;
                    } else {
                        // yearly
                        $price = 1450;
                    }
                }

                $purchase->details[] = [
                    'description'  => $description,
                    'unit_cost'    => $price,
                    'quantity'     => 1,
                    'tax1_name'    => 'IVA',
                    'tax1_percent' => $vatTax
                ];

                $i++;
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
        }

        $em->persist($purchase);

        return new JsonResponse();
    }
}
