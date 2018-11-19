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
use Common\Core\Controller\Controller;

class TrashController extends Controller
{
    /**
     * Lists all the trashed elements
     *
     * @Security("hasExtension('TRASH_MANAGER')
     *     and hasPermission('TRASH_ADMIN')")
     */
    public function defaultAction()
    {
        $cm           = new \ContentManager();
        $contentTypes = $cm->getContentTypes();

        return $this->render('trash/list.tpl', [
            'types_content' => $contentTypes,
        ]);
    }
}
