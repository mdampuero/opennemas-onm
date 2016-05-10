<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Framework\ORM\Entity\Purchase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * Displays, saves, modifies and removes purchases.
 */
class PurchaseController extends Controller
{
    /**
     * @api {delete} /purchases/:id Delete a purchase
     * @apiName DeletePurchase
     * @apiGroup Purchase
     *
     * @apiSuccess {String} message The success message.
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $purchase = $em->getRepository('Purchase')->find($id);

        $em->remove($purchase);
        $msg->add(_('Purchase deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * @api {delete} /purchases/ Delete selected purchases
     * @apiName DeletePuchases
     * @apiGroup Purchase
     *
     * @apiParam {Integer} selected The clients ids.
     *
     * @apiSuccess {String} message The success message.
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || empty($ids)) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em  = $this->get('orm.manager');
        $oql = sprintf('id in [%s]', implode(',', $ids));

        $purchases = $em->getRepository('Purchase')->findBy($oql);

        $deleted = 0;
        foreach ($purchases as $purchase) {
            try {
                $em->remove($purchase);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s users deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

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

        $purchase = $em->getRepository('manager.purchase')->find($id);

        $pdf = $em->getRepository('invoice', 'FreshBooks')
            ->getPDF($purchase->invoice_id);

        $response = new Response($pdf);

        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    /**
     * @api {get} /purchases List of purchases
     * @apiName GetPurchases
     * @apiGroup Purchase
     *
     * @apiParam {String} oql  The OQL query.
    *
     * @apiSuccess {Integer} total   The total number of elements.
     * @apiSuccess {Array}   results The list of purchases.
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('Purchase');
        $converter  = $this->get('orm.manager')->getConverter('Purchase');

        $total     = $repository->countBy($oql);
        $purchases = $repository->findBy($oql);

        $purchases = array_map(function ($a) use ($converter, &$ids) {
            $ids[] = $a->instance_id;

            return $converter->responsify($a->getData());
        }, $purchases);

        $extra = $this->getExtraData();

        // Find instances by ids
        if (!empty($ids)) {
            $oql = sprintf('id in %s', $ids);

            $items = $this->get('orm.manager')
                ->getRepository('Instance')
                ->findBy($oql);

            $extra['instances'] = [];
            foreach ($items as $item) {
                $extra['instances'][$item[$id]] = $item->internal_name;
            }
        }

        return new JsonResponse([
            'extra'   => $extra,
            'results' => $purchases,
            'total'   => $total,
        ]);
    }

    /**
     * @api {get} /purchases/:id Show a purchase
     * @apiName GetPurchase
     * @apiGroup Purchase
     *
     * @apiSuccess {Array} purchase The purchases.
     */
    public function showAction($id)
    {
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('Purchase');
        $purchase  = $em->getRepository('Purchase', 'manager')->find($id);

        // Remove payment line from purchase
        if ($purchase->method === 'CreditCard') {
            array_pop($purchase->details);
        }

        return new JsonResponse([
            'purchase' => $converter->responsify($purchase->getData()),
            'extra'    => $this->getExtraData()
        ]);
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

        return [
            'braintree'  => [
                'url'         => $this->getparameter('braintree.url'),
                'merchant_id' => $this->getparameter('braintree.merchant_id')
            ],
            'countries'  => $countries,
            'freshbooks' => [
                'url' => $this->getparameter('freshbooks.url')
            ]
        ];
    }
}
