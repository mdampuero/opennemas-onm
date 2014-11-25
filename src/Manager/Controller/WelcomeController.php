<?php
/**
 * Handles the actions for the manager welcome page
 *
 * @package Manager_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controller;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the manager welcome page
 *
 * @package Manager_Controllers
 **/
class WelcomeController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     */
    public function init()
    {
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);
    }

    /**
     * Shows the welcome page of the manager
     *
     * @return void
     */
    public function defaultAction()
    {
        $messages = [
            _("Printing your newspaper..."),
            _("Cloning journalists..."),
            _("Writing articles using Wikipedia..."),
            _("Spinning up the rotary..."),
            _("Reinventing Gutenberg machine..."),
        ];
        $loadingMessage = $messages[array_rand($messages)];

        return $this->render(
            'base/base.tpl',
            ['loading_message' => $loadingMessage,]
        );
    }
}
