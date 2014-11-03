<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class TrashController extends Controller
{
    /**
     * Lists all the trashed elements
     *
     * @return void
     *
     * @Security("has_role('TRASH_ADMIN')")
     **/
    public function defaultAction()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('TRASH_MANAGER');

        $cm           = new \ContentManager();
        $contentTypes = $cm->getContentTypes();

        return $this->render(
            'trash/list.tpl',
            array(
                'types_content' => $contentTypes,
            )
        );
    }
}
