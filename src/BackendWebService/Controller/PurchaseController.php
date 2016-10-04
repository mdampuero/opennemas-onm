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
        $em  = $this->get('orm.manager');
        $oql = sprintf('id = %s', $id);

        $purchase = $em->getRepository('Purchase')->findOneBy($oql);

        if (!$purchase) {
            throw new \Exception(_('Unable to find the purchase'));
        }

        $pdf = $em->getRepository('invoice', 'freshbooks')
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
        $purchase = $this->get('core.helper.checkout')->getPurchase();

        $this->get('orm.manager')->persist($purchase);

        return new JsonResponse($purchase->id);
    }

    /**
     * @api {put} /purchases/:id Updates a purchase
     * @apiName UpdatePurchase
     * @apiGroup Purchase
     */
    public function updateAction(Request $request, $id)
    {
        $step   = $request->request->get('step', 'cart');
        $ids    = $request->request->get('ids', []);
        $params = $request->request->get('params', []);
        $method = $request->request->get('method', null);

        $ph = $this->get('core.helper.checkout');
        $ph->getPurchase($id);
        $ph->next($step, $ids, $params, $method);

        return new JsonResponse();
    }
}
