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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Common\ORM\Entity\Newsletter;

/**
 * Handles the actions for the newsletter
 */
class NewsletterTemplateController extends Controller
{
    /**
     * List the form for create or load contents in a newsletter.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function createAction()
    {
        return $this->render('newsletter/templates/item.tpl');
    }

    /**
     * Shows the newsletter template information given its id
     *
     * @param integer $id The user id.
     *
     * @return Response The response object.
     */
    public function showAction($id)
    {
        return $this->render('newsletter/templates/item.tpl', [ 'id' => $id ]);
    }
}
