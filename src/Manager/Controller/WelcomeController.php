<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Manager\Controller;

use Common\Core\Controller\Controller;

/**
 * Handles the actions for the manager welcome page
 *
 * @package Manager_Controllers
 */
class WelcomeController extends Controller
{
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
            [ 'loading_message' => $loadingMessage ]
        );
    }
}
