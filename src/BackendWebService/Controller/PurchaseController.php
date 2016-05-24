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
        $purchase->step        = 1;
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

        if (!empty($this->get('instance')->getClient())) {
            $client = $this->get('instance')->getClient();
            $client = $em->getRepository('manager.client', 'Database')->find($client);

            if (!empty($client)) {
                $purchase->client_id = $client->id;
                $purchase->client    = $client;
            }
        }

        $vatTax = $this->get('vat')->getVatFromCode($purchase->client->country);

        $method         = $request->request->get('method', null);
        $subtotal       = 0;
        $purchase->step = $request->request->get('step', 1);
        $purchase->fee  = 0;

        $ids = $request->request->get('ids', []);

        if (!empty($ids)) {
            $items = $em->getRepository('manager.extension')->findBy([
                'id' => [ [ 'value' => $ids, 'operator' => 'in' ] ]
            ]);

            $purchase->details = [];
            foreach ($items as $item) {
                $subtotal    += $item->getPrice();
                $description  = $item->name[CURRENT_LANGUAGE_SHORT];

                if (!empty($request->request->get('domain'))) {
                    $description .= ': ' . $request->request->get('domain');
                }

                $purchase->details[] = [
                    'description'  => $description,
                    'unit_cost'    => $item->getPrice(),
                    'quantity'     => 1,
                    'tax1_name'    => 'IVA',
                    'tax1_percent' => $vatTax
                ];
            }

            $vat = ($vatTax/100) * $subtotal;

            if (!empty($method) === 'PaypalAccount') {
                $purchase->fee = $subtotal * 2.9 + 0.30;
            }

            $purchase->total = $subtotal + $vat + $purchase->fee;
        }

        $em->persist($purchase);

        return new JsonResponse();
    }
}
