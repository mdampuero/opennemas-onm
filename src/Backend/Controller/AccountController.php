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

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;
use Common\Core\Annotation\Security;

class AccountController extends Controller
{
    /**
     * Returns account information.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('ADMIN')")
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

        $nws = $this->get('api.service.newsletter');

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get(['last_invoice']);

        $lastInvoice = new \DateTime($settings['last_invoice']);
        $total       = $nws->getSentNewslettersSinceLastInvoice($lastInvoice);

        return $this->render('account/account.tpl', [
            'client'      => $client,
            'countries'   => $countries,
            'instance'    => $instance,
            'lastInvoice' => $lastInvoice->format(_('Y-m-d')),
            'total'       => $total
        ]);
    }
}
