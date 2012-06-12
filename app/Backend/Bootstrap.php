<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend;

use Onm\Framework\Module\ModuleBootstrap;

/**
 * Initializes the Backend Module
 *
 * @package default
 * @author
 **/
class Bootstrap extends ModuleBootstrap
{
    /**
     * undocumented function
     *
     * @return void
     **/
    public function initContainer()
    {
        // $this->container->setParameter('dispatcher.exceptionhandler', 'Backend:Controllers:ErrorController:default');

        return $this;
    }

    /**
     * Starts the authentication system for the backend
     *
     * @return Boostrap the boostrap instance
     **/
    public function initAuthenticationSystem()
    {
        require_once './session_bootstrap.php';
    }

} // END class Bootstrap