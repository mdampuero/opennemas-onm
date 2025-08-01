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

use Common\Core\Controller\Controller;
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

        if (empty($pdf)) {
            return $this->render('purchase/error.tpl');
        }

        $response = new Response($pdf);

        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    /**
     * @api {get} /purchases List of purchases
     * @apiName GetPurchases
     * @apiGroup Purchase
     *
     * @apiParam {String} oql The OQL query.
     *
     * @apiSuccess {Integer} total   The total number of elements.
     * @apiSuccess {Array}   results The list of purchases.
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        if (!empty($oql) && !preg_match('/^(order|limit)/', $oql)) {
            $oql = ' and ' . $oql;
        }

        $oql = sprintf(
            'instance_id = "%s" and step = "done"',
            $this->get('core.instance')->id
        ) .  $oql;

        $repository = $this->get('orm.manager')->getRepository('Purchase');
        $converter  = $this->get('orm.manager')->getConverter('Purchase');

        $total     = $repository->countBy($oql);
        $purchases = $repository->findBy($oql);

        $purchases = $converter->responsify($purchases);

        return new JsonResponse([
            'results' => $purchases,
            'total'   => $total,
        ]);
    }

    /**
     * @api {post} /purchases Creates a new purchase
     * @apiName CreatePurchase
     * @apiGroup Purchase
     */
    public function saveAction(Request $request)
    {
        $ids      = $request->request->get('ids', []);
        $ph       = $this->get('core.helper.checkout');
        $purchase = $ph->getPurchase();

        $ph->next('start', $ids, [], null);

        return new JsonResponse([ 'id' => $purchase->id ]);
    }

    /**
    ** @api {post} /purchases/:id Shows a purchase
     * @apiName ShowPurchase
     * @apiGroup Purchase
     */
    public function showAction($id)
    {
        $em  = $this->get('orm.manager');
        $oql = sprintf(
            'id = %s and instance_id = %s',
            $id,
            $this->get('core.instance')->id
        );

        $converter = $em->getConverter('Purchase');
        $purchase  = $em->getRepository('purchase')->findOneBy($oql);

        // Remove payment line from purchase
        array_pop($purchase->details);

        return new JsonResponse([
            'purchase' => $converter->responsify($purchase),
            'extra'    => $this->getExtraData()
        ]);
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

        $ph       = $this->get('core.helper.checkout');
        $purchase = $ph->getPurchase($id);
        $ph->next($step, $ids, $params, $method);

        return new JsonResponse([ 'id' => $purchase->id ]);
    }

    /**
     * Returns an array with extra parameters for template.
     *
     * @return array Array of extra parameters for template.
     */
    protected function getExtraData()
    {
        $countries = Intl::getRegionBundle()
            ->getCountryNames($this->get('core.locale')->getLocaleShort());

        asort($countries);

        return [ 'countries'  => $countries ];
    }
}
