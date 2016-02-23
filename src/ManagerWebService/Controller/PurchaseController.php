<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Framework\ORM\Entity\Purchase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Handles the Purchase resource.
 */
class PurchaseController extends Controller
{
    /**
     * @api {get} /purchases List of purchases
     * @apiName GetPurchases
     * @apiGroup Purchase
     *
     * @apiParam {String} client  The client's name or email.
     * @apiParam {String} from    The start date.
     * @apiParam {String} to      The finish date.
     * @apiParam {String} orderBy The values to sort by.
     * @apiParam {Number} epp     The number of elements per page.
     * @apiParam {Number} page    The current page.
     *
     * @apiSuccess {Integer} epp     The number of elements per page.
     * @apiSuccess {Integer} page    The current page.
     * @apiSuccess {Integer} total   The total number of elements.
     * @apiSuccess {Array}   results The list of purchases.
     */
    public function listAction(Request $request)
    {
        $q        = $request->query->filter('q');
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);
        $criteria = $request->query->filter('criteria') ? : [];
        $orderBy  = $request->query->filter('orderBy') ? : [];
        $extra    = $this->getTemplateParams();

        $order = [];
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        $repository = $this->get('orm.manager')
            ->getRepository('manager.purchase');

        $ids       = [];
        $purchases = $repository->findBy($criteria, $order, $epp, $page);
        $total     = $repository->countBy($criteria);

        // Clean purchases
        foreach ($purchases as &$purchase) {
            $ids[]    = $purchase->instance_id;
            $purchase = $purchase->getData();
        }

        // Find instances by ids
        if (!empty($ids)) {
            $extra['instances'] = $this->get('instance_manager')->findBy([
                'id' => [ [ 'value' => $ids, 'operator' => 'IN' ] ]
            ]);
        }

        return new JsonResponse([
            'epp'     => $epp,
            'extra'   => $extra,
            'page'    => $page,
            'results' => $purchases,
            'total'   => $total,
        ]);
    }

    /**
     * Returns an array with extra parameters for template.
     *
     * @return array Array of extra parameters for template.
     */
    protected function getTemplateParams()
    {
        return [
            'braintree'  => [
                'url' => $this->getParameter('braintree.url'),
                'merchant_id' => $this->getParameter('braintree.merchant_id')
            ],
            'freshbooks' => [
                'url' => $this->getParameter('freshbooks.url')
            ]
        ];
    }
}
