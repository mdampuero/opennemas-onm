<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Displays and saves system settings.
 */
class SystemSettingsController extends Controller
{
    /**
     * Gets all the settings and displays the form
     *
     * @Security("hasExtension('SETTINGS_MANAGER')
     *     and hasPermission('ONM_SETTINGS')")
     */
    public function defaultAction()
    {
        return $this->render('system_settings/system_settings.tpl');
    }
}
