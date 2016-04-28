<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Onm\Framework\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * Handles all the request for Client page actions
 *
 * @package Backend_Controllers
 **/
class ClientInformationController extends Controller
{
    /**
     * Handles the default action
     *
     * @return void
     **/
    public function defaultAction()
    {
        $instance = $this->get('instance');
        $id       = $instance->getClient();
        $client   = null;
        $countries = $countries = Intl::getRegionBundle()
            ->getCountryNames(CURRENT_LANGUAGE_LONG);

        if (!empty($id)) {
            $client = $this->get('orm.manager')
                ->getRepository('manager.client', 'Database')
                ->find($id);
        }

        $users = 2;
        //$users = $this->get('setting_repository')->get('max_users');

        //$users = $users === 'NaN' ? _('unlimited')

        return $this->render(
            'stats/stats_info.tpl',
            [
                'client'    => $client,
                'countries' => $countries,
                'instance'  => $instance,
                'users'     => $users
            ]
        );
    }
}
