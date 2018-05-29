<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the newsletters
 *
 * @package Backend_Controllers
 */
class NewslettersController extends Controller
{
    /**
     * Lists nwesletters and perform searches across them
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');
        $nm  = $this->get('newsletter_manager');

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        list($total, $newsletters) =
            $nm->find($criteria, $order, $page, $epp);

        $newsletters = \Onm\StringUtils::convertToUtf8($newsletters);

        // new code
        return new JsonResponse([
            'results' => $newsletters,
            'total'   => $total,
        ]);
    }

    /**
     * Deletes an newsletter given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $errors = $success = [];
        if (!empty($id)) {
            $newsletter = new \Newsletter($id);
            $result     = $newsletter->delete();

            if ($result) {
                $success[] = [
                    'id'      => $id,
                    'message' => _("Newsletter deleted successfully."),
                    'type'    => 'success'
                ];
            } else {
                $errors[] = [
                    'id'      => $id,
                    'message' => sprintf(_('Unable to delete the newsletter "%s"'), $id),
                    'type'    => 'error'
                ];
            }
        } else {
            $errors[] = [
                'message' => _('You must provide an id for delete a newsletter.'),
                'type'    => 'error'
            ];
        }

        return new JsonResponse([ 'messages' => array_merge($success, $errors) ]);
    }
}
