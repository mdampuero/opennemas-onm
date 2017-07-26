<?php
/**
 * Handles the actions for the newsletters
 *
 * @package Backend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
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
        // Initialization of the newsletter provider
        $nm = $this->get('newsletter_manager');

        $elementsPerPage = $request->query->getDigits('elements_per_page', 10);
        $page            = $request->query->getDigits('page', 1);
        $search          = $request->query->get('search', '');

        if (is_array($search) && array_key_exists('title', $search)) {
            $titleFilter = 'title LIKE \''.(string) $search['title'][0]['value'].'\'';
        } else {
            $titleFilter = '1 = 1';
        }
        list($total, $newsletters) = $nm->find($titleFilter, 'created DESC', $page, $elementsPerPage);

        // new code
        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'page'              => $page,
                'results'           => $newsletters,
                'total'             => $total,
            )
        );
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
            $result = $newsletter->delete();

            if ($result) {
                $success[] = array(
                    'id'      => $id,
                    'message' => _("Newsletter deleted successfully."),
                    'type'    => 'success'
                );
            } else {
                $errors[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Unable to delete the newsletter "%s"'), $id),
                    'type'    => 'error'
                );
            }
        } else {
            $errors[] = array(
                'message' => _('You must provide an id for delete a newsletter.'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors),
            )
        );
    }
}
