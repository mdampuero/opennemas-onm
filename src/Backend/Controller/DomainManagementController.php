<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Framework\ORM\Entity\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Onm\Framework\Controller\Controller;

class DomainManagementController extends Controller
{
    /**
     * Lists all the available ads.
     *
     * @return Response The response object.
     */
    public function listAction()
    {
        return $this->render('domain_management/list.tpl');
    }

    /**
     * Lists all the available ads.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function addAction(Request $request)
    {
        $client = [];
        $params = [];

        $instance = $this->get('instance');

        if (array_key_exists('client', $instance->metas)
            && !empty($instance->metas['client'])
        ) {
            $params = [ 'customerId' => $instance->metas['client'] ];
            $client = $this->get('orm.manager')
                ->getRepository('manager.client', 'Database')
                ->find($instance->metas['client']);
        }

        $countries    = array_flip(Intl::getRegionBundle()->getCountryNames());
        $taxes        = $this->get('vat')->getTaxes();
        $tokenFactory = $this->get('onm.braintree.factory')->get('ClientToken');
        $token        = $tokenFactory::generate($params);

        return $this->render(
            'domain_management/add.tpl',
            [
                'client'    => $client->getData(),
                'create'    => $request->query->get('create'),
                'countries' => $countries,
                'taxes'     => $taxes,
                'token'     => $token
            ]
        );
    }

    /**
     * Lists all the available ads.
     *
     * @return Response The response object.
     */
    public function showAction()
    {
        return $this->render('domain_management/show.tpl');
    }
}
