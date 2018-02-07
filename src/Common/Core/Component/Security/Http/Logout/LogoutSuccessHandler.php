<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Security\Http\Logout;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /**
     * Initializes the LogoutSuccessHandler.
     *
     * @param Router $router The router service.
     */
    public function __construct($router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        $referer = $request->headers->get('referer');
        $target  = $this->router->generate('frontend_authentication_login');

        if (preg_match('/\/admin.*/', $referer)) {
            $target = $this->router->generate('backend_authentication_login');
        }

        return new RedirectResponse($target);
    }
}
