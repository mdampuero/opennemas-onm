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
        \Onm\Module\ModuleManager::checkActivatedOrForward('PAYWALL');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->times = array(
            '24'    => _('1 day'),
            '48'    => sprintf(_('%d days'), '2'),
            '168'   => sprintf(_('1 week')),
            '336'   => sprintf(_('%d week'), '2'),
            '744'   => sprintf(_('1 month')),
            '2232'  => sprintf(_('%d months'), '3'),
            '4464'  => sprintf(_('6 months'), '3'),
            '8928'  => sprintf(_('1 year')),
            '17856' => sprintf(_('%d years'), '2'),
        );

        $this->moneyUnits = array(
            'EUR' => '€',
            'USD' => '$',
        );
    }

    /**
     * Shows a list of purchases for the paywall module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        $settings = s::get('paywall_settings');

        if (empty($settings)) {
            $session = $this->get('session');
            $session->start();
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please configure your Paywall module before using it.')
            );
            return $this->redirect($this->generateUrl('admin_paywall_settings'));
        }
        $users = \User::getUsersWithSubscription(array('limit' => 10));

        $countUsersPaywall = \User::countUsersWithSubscription();

        // Get the last purchases
        $purchases = \Order::find(
            "type='paywall'",
            array(
                'limit' => 10
            )
        );


        // Count how many purchases were done in the last month
        $time = new \DateTime();
        $time->setTimezone(new \DateTimeZone('UTC'))
             ->modify("-1 month");
        $time = $time->format('Y-m-d H:i:s');

        $purchasesLastMonth = \Order::count(
            "type='paywall' AND created > '$time'",
            array()
        );

        return $this->render(
            'paywall/list.tpl',
            array(
                'users'                      => $users,
                'count_users_paywall'        => $countUsersPaywall,
                'purchases'                  => $purchases,
                'count_purchases_last_month' => $purchasesLastMonth,
                'settings'                   => $settings,
                'money_units'                => $this->moneyUnits,
            )
        );
    }

    /**
     * Description of this action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function usersAction(Request $request)
    {
        $page   = $this->request->query->getDigits('page', 1);
        $type = $this->request->query->filter('type', '', FILTER_SANITIZE_STRING);
        $order  = $this->request->query->filter('order', 'name', FILTER_SANITIZE_STRING);

        $settings = s::get('paywall_settings');

        $users = array();
        if ($type === '0') {
            $users = \User::getUsersWithSubscription();
        } elseif ($type === '1') {
            $users = \User::getUsersOnlyRegistered();
        } else {
            $usersRegistered = \User::getUsersOnlyRegistered();
            $usersWithSubscription = \User::getUsersWithSubscription();

            $users = array_merge($usersRegistered, $usersWithSubscription);
        }

        // Sort array of users by property
        if (!isset($order) || empty($order)) {
            $order = 'name';
        }

        $users = \ContentManager::sortArrayofObjectsByProperty($users, $order);

        $itemsPerPage = s::get('items_per_page') ?: 20;

        $usersPage = array_slice($users, ($page-1)*$itemsPerPage, $itemsPerPage);

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => count($users),
                'fileName'    => $this->generateUrl('admin_paywall_users').'?page=%d',
            )
        );

        return $this->render(
            'paywall/users.tpl',
            array(
                'users'       => $usersPage,
                'settings'    => $settings,
                'money_units' => $this->moneyUnits,
                'pagination'  => $pagination,
            )
        );
    }

    /**
     * Description of this action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function purchasesAction(Request $request)
    {
        $page = $this->request->query->getDigits('page', 1);

        $settings = s::get('paywall_settings');
        $purchases = \Order::find(
            "type='paywall'",
            array(
                'limit' => 0
            )
        );

        $itemsPerPage = s::get('items_per_page') ?: 20;

        $purchasesPage = array_slice($purchases, ($page-1)*$itemsPerPage, $itemsPerPage);

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => count($purchases),
                'fileName'    => $this->generateUrl('admin_paywall_purchases').'?page=%d',
            )
        );

        return $this->render(
            'paywall/purchases.tpl',
            array(
                'purchases'   => $purchasesPage,
                'settings'    => $settings,
                'money_units' => $this->moneyUnits,
                'pagination'  => $pagination,
            )
        );
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function settingsAction(Request $request)
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
        $settings['paypal_username']  = $request->request->filter(
            'settings[paypal_username]',
            '',
            FILTER_SANITIZE_STRING
        );
        $settings['paypal_password']  = $request->request->filter(
            'settings[paypal_password]',
            '',
            FILTER_SANITIZE_STRING
        );
        $settings['paypal_signature'] = $request->request->filter(
            'settings[paypal_signature]',
            '',
            FILTER_SANITIZE_STRING
        );
        $settings['money_unit']       = $request->request->filter(
            'settings[money_unit]',
            'dollar',
            FILTER_SANITIZE_STRING
        );
        $settings['developer_mode']   = (boolean) $settingsForm['developer_mode'];
        $settings['vat_percentage']   = (int) $settingsForm['vat_percentage'];

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

        $this->get('session')->getFlashBag()->add('success', _("Paywall settings saved."));

        s::set('paywall_settings', $settings);

        return $this->redirect($this->generateUrl('admin_paywall_settings'));
    }
}
