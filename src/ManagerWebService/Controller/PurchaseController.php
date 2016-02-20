<?php

namespace ManagerWebService\Controller;

use Framework\ORM\Entity\Purchase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PurchaseController extends Controller
{
    /**
     * Returns the list of instances as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);
        $criteria = $request->query->filter('criteria') ? : [];
        $orderBy  = $request->query->filter('orderBy') ? : [];
        $extra    = $this->getTemplateParams();

        $order = array();
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        if (!empty($criteria)) {
            $criteria['union'] = 'OR';
        }

        $nr = $this->get('orm.manager')->getRepository('manager.purchase');

        $purchases = $nr->findBy($criteria, $order, $epp, $page);

        $ids = [];
        foreach ($purchases as &$purchase) {
            $ids[] = $purchase->instance_id;

            $purchase = $purchase->getData();
        }

        $ids       = array_unique(array_diff($ids, [ -1, 0 ]));
        $instances = [];
        if (!empty($ids)) {
            $instances = $this->get('instance_manager')->findBy([
                'id' => [ [ 'value' => $ids, 'operator' => 'IN' ] ]
            ]);
        }

        //$extra['instances'] = [
            //'-1' => [ 'name' => _('Manager'), 'value' => -1 ],
            //'0'  => [ 'name' => _('All'), 'value' => 0 ]
        //];

        //foreach ($instances as $instance) {
            //$extra['instances'][$instance->id] = [
                //'name'  => $instance->internal_name,
                //'value' => $instance->id,
            //];
        //}

        $total = $nr->countBy($criteria);

        return new JsonResponse([
            'epp'     => $epp,
            'extra'   => $extra,
            'page'    => $page,
            'results' => $purchases,
            'total'   => $total,
        ]);
    }

    public function getTemplateParams()
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
