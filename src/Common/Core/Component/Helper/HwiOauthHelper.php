<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class HwiOauthHelper
{
    /**
     * The dataset for settings.
     *
     * @var DataSet
     */
    protected $router;

    /**
     * The dataset for settings.
     *
     * @var DataSet
     */
    protected $hwiTemplate;

    /**
     * Initializes the AdvertisementHelper.
     *
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router The router service.
     * @param \HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper
     *            $hwiOauthTemplate The template header from the HWI OAuth.
     */
    public function __construct(Router $router, OauthHelper $hwiOauthTemplate)
    {
        $this->router      = $router;
        $this->hwiTemplate = $hwiOauthTemplate;
    }

    /**
     * Returns the authorization url from a service name and the redirection url
     *
     * @return void
     */
    public function getAuthorizationUrl($name, $redirectUrl = null, $extra = null)
    {
        if (!is_null($redirectUrl)) {
            $redirectUrl = $router->generate($redirectUrl, [], true);
        }

        return $this->hwiTemplate->getAuthorizationUrl($name, $redirectUrl, $extra);
    }

    /**
     * Returns the login url given its name
     *
     * @param string $name the name of the service
     *
     * @return string
     */
    public function getLoginUrl($name)
    {
        return $this->hwiTemplate->getLoginUrl($name);
    }


    /**
     * Returns the resourceOwners
     *
     * @return void
     */
    public function getResourceOwners()
    {
        return $this->hwiTemplate->getResourceOwners();
    }
}
