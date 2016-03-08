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
        $billing = [];

        $instance = $this->get('instance');

        if (!empty($instance->metas)) {
            foreach ($instance->metas as $key => $value) {
                if (strpos($key, 'billing_') !== false) {
                    $billing[str_replace('billing_', '', $key)] = $value;
                }
            }
        }

        $countries = array_flip(Intl::getRegionBundle()->getCountryNames());
        $taxes     = $this->get('vat')->getTaxes();

        return $this->render(
            'domain_management/add.tpl',
            [
                'billing'   => $billing,
                'create'    => $request->query->get('create'),
                'countries' => $countries,
                'taxes'     => $taxes
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
