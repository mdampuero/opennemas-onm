<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Onm\Settings as s;

/**
 * Start up and setup the app
*/
require_once '../bootstrap.php';
require_once 'session_bootstrap.php';


if (isset($_SESSION['userid'])) {
    $output =
        '<nav>
            <ul>
                <li class="user-menu">
                    <a href="/logout/'.$_SESSION['csrf'].'/" id="logout" class="logout" title="Pechar sesión">
                        Saír
                    </a>
                </li>
                <li class="user-menu">
                    <a href="/user/edit/'.$_SESSION['userid'].'/" id="edit-user" title="Editar perfil">
                        Editar perfil
                    </a>
                </li>
                <li class="user-menu">
                    <a href="#" class="user-window">
                        '.$_SESSION['username'].', '.sprintf("%0.2f", $_SESSION['deposit']).' €
                    </a>
                </li>
            </ul>
        </nav>';
} else {
    $output =
        '<nav>
            <ul>
                <li><a href="'.SITE_URL.'register/">Rexistrate</a></li>
                <li><a class="login-form" href="#">Inicia sesión</a></li>
            </ul>
        </nav>';
}

Application::ajaxOut($output);

