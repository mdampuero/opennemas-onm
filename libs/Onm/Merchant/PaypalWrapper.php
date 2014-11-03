<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Merchant;

/**
 *
 */
class PaypalWrapper
{

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function __construct($settings)
    {
        $defaultSettings = array(
            'mode'                   => 'sandbox',
            'http.ConnectionTimeOut' => 30,
            'http.Retry'             => 5,
            'log.LogEnabled'         => false,
            'log.FileName'           => SYS_LOG_PATH.'/PayPal.log',
            'log.LogLevel'           => 'INFO',
        );
        $settings = array_merge($defaultSettings, $settings);

        $this->settings = $settings;
    }
    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getMerchantService()
    {
        return new \PayPalAPIInterfaceServiceService($this->settings);
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public function getServiceUrl()
    {
        if ($this->settings['mode'] == 'live') {
            return 'https://www.paypal.com/webscr?cmd=_express-checkout';
        } else {
            return 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout';
        }
    }
}
