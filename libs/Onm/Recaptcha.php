<?php
/**
 * Defines the Onm\Recaptcha class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Onm
 **/
namespace Onm;

/**
* Class for using the Google Recaptcha
*
* @package  Onm
*/
class Recaptcha
{
    /**
     * Initializes Recaptcha.
     *
     * @param SettingManager $sm       The setting service.
     * @param string         $key      The default private key.
     */
    public function __construct($sm, $key)
    {
        $this->key = $key;
        $this->sm  = $sm;
    }


    /**
     * Returns a recaptcha instance for internal onm use
     *
     * @return Recaptcha The Recaptcha instance
     */
    public function getOnmRecaptcha()
    {
        return new \ReCaptcha\ReCaptcha($this->key);
    }

    /**
     * Returns a recaptcha instance for clients/public use
     *
     * @return Recaptcha The Recaptcha instance
     */
    public function getPublicRecaptcha()
    {
        $recaptcha = $this->sm->get('recaptcha');

        if (!is_array($recaptcha) || !array_key_exists('private_key', $recaptcha)) {
            return false;
        }

        return new \ReCaptcha\ReCaptcha($recaptcha['private_key']);
    }
}
