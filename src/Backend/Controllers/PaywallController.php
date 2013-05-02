<?php
/**
 * Defines the PaywallController class
 *
 * @package  Backend_Controllers
 **/
/**
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for paywall module
 *
 * @package Backend_Controllers
 **/
class PaywallController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->times = array(
            '1D' => _('1 day'),
            '2D' => sprintf(_('%d days'), '2'),
            '1W' => sprintf(_('1 week')),
            '2W' => sprintf(_('%d week'), '2'),
            '1M' => sprintf(_('1 month')),
            '3M' => sprintf(_('%d months'), '3'),
            '6M' => sprintf(_('6 months'), '3'),
            '1Y' => sprintf(_('1 year')),
            '2Y' => sprintf(_('%d years'), '2'),
            // 'unlimited' => sprintf(_('Unlimited'))
        );

        $this->moneyUnits = array(
            'USD' => '$',
            'EUR' => '€',
        );
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        $settings = s::get('paywall_settings');

        return $this->render(
            'paywall/settings.tpl',
            array(
                'settings'    => $settings,
                'times'       => $this->times,
                'money_units' => $this->moneyUnits,
            )
        );
    }

    /**
     * Saves the paywall settings
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function settingsSaveAction(Request $request)
    {
        $settingsForm = $request->request->get('settings');

        $settings = array('payment_modes' => array());

        // Check values
        $settings['paypal_user_email'] = $request->request->filter('settings[paypal_user_email]', 'test@test.com', FILTER_SANITIZE_STRING);
        $settings['money_unit']        = $request->request->filter('settings[money_unit]', 'dollar', FILTER_SANITIZE_STRING);
        $settings['vat_percentage']    = (int) $settingsForm['vat_percentage'];

        // Check payment modes
        $number = count($settingsForm['payment_modes']['time']);
        if ($number > 0) {
            $paymentModes = array();
            for ($i=0; $i < $number; $i++) {
                $settings['payment_modes'] []= array(
                    'time'              => $settingsForm['payment_modes']['time'][$i],
                    'description'       => $settingsForm['payment_modes']['description'][$i],
                    'price'             => $settingsForm['payment_modes']['price'][$i],
                );
            }
        }

        $this->get('session')->setFlash('success', _("Paywall settings saved."));

        s::set('paywall_settings', $settings);

        return $this->redirect($this->generateUrl('admin_paywall'));
    }
}
