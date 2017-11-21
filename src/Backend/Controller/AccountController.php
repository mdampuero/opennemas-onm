<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

class AccountController extends Controller
{
    /**
     * Returns account information.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('ROLE_ADMIN')")
     */
    public function defaultAction()
    {
        $instance  = $this->get('core.instance');
        $id        = $instance->getClient();
        $client    = null;
        $countries = $countries = Intl::getRegionBundle()
            ->getCountryNames(CURRENT_LANGUAGE_LONG);

        if (!empty($id)) {
            $client = $this->get('orm.manager')->getRepository('Client')
                ->find($id);
        }

        return $this->render('account/account.tpl', [
            'client'    => $client,
            'countries' => $countries,
            'instance'  => $instance
        ]);
    }
}
