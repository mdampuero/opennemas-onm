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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the letters content
 *
 * @package Backend_Controllers
 */
class LettersController extends Controller
{
    /**
     * Lists all the letters.
     *
     * @return Response          The response object.
     *
     * @Security("hasExtension('LETTER_MANAGER')
     *     and hasPermission('LETTER_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('letter/list.tpl');
    }

    /**
     * Handles the form for create new letters.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('LETTER_MANAGER')
     *     and hasPermission('LETTER_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return $this->render('letter/new.tpl', [
                'locale'         => $this->get('core.locale')->getLocale('frontend'),
                'enableComments' => $this->get('core.helper.comment')->enableCommentsByDefault(),
            ]);
        }

        $letter = new \Letter();

        $data = [
            'title'          => $request->request
                ->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'with_comment'   => $request->request->filter('with_comment', 0, FILTER_SANITIZE_STRING),
            'author'         => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
            'email'          => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
            'params'         => $request->request->get('params'),
            'image'          => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
            'url'            => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'body'           => $request->request->get('body', ''),
            'tag_ids'        => json_decode($request->request->get('tag_ids', ''), true)
        ];

        if ($letter->create($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Letter successfully created.')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to create the new letter.')
            );
        }

        return $this->redirect($this->generateUrl(
            'admin_letter_show',
            [ 'id' => $letter->id]
        ));
    }

    /**
     * Shows the letter information form.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('LETTER_MANAGER')
     *     and hasPermission('LETTER_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id     = $request->query->getDigits('id', null);
        $letter = $this->get('entity_repository')->find('Letter', $id);

        if (!empty($letter->image)) {
            $photo1 = new \Photo($letter->image);
            $this->view->assign('photo1', $photo1);
        }

        if (is_null($letter->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the letter with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_letters'));
        }

        $ls = $this->get('core.locale');
        return $this->render('letter/new.tpl', [
            'letter'         => $letter,
            'enableComments' => $this->get('core.helper.comment')
                ->enableCommentsByDefault(),
            'locale'         => $ls->getRequestLocale('frontend'),
        ]);
    }

    /**
     * Updates the letter information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('LETTER_MANAGER')
     *     and hasPermission('LETTER_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add('error', _("Letter data sent not valid."));

            return $this->redirect($this->generateUrl('admin_letter_show', [ 'id' => $id ]));
        }

        $letter = new \Letter($id);
        if ($letter->id == null) {
            return $this->redirect($this->generateUrl('admin_letters'));
        }

        $data = [
            'id'             => $id,
            'title'          => $request->request
                ->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'content_status' => $request->request->filter('content_status', '', FILTER_SANITIZE_STRING),
            'with_comment'   => $request->request->filter('with_comment', 0, FILTER_SANITIZE_STRING),
            'author'         => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
            'email'          => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
            'params'         => $request->request->get('params'),
            'image'          => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
            'url'            => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'body'           => $request->request->filter('body', ''),
            'tag_ids'        => json_decode($request->request->get('tag_ids', ''), true)
        ];

        if ($letter->update($data)) {
            $this->get('session')->getFlashBag()->add('success', _('Letter successfully updated.'));
        } else {
            $this->get('session')->getFlashBag()->add('error', _('Unable to update the letter.'));
        }

        return $this->redirect($this->generateUrl('admin_letter_show', [
            'id' => $letter->id
        ]));
    }

    /**
     * Lists the available Letters for the frontpage manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('LETTER_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId         = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $itemsPerPage       = 8;
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds((int) $categoryId, $frontpageVersionId, 'Letter');

        $filters = [
            'content_type_name' => [ ['value' => 'letter'] ],
            'content_status'    => [ ['value' => 1] ],
            'in_litter'         => [ ['value' => 1, 'operator' => '!='] ],
            'pk_content'        => [ ['value' => $ids, 'operator' => 'NOT IN'] ]
        ];

        $countLetters = true;
        $letters      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page, 0, $countLetters);

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'boundary'    => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countLetters,
            'route'       => [
                'name'   => 'admin_letters_content_provider',
                'params' => [ 'category' => $categoryId ]
            ]
        ]);

        return $this->render('letter/content-provider.tpl', [
            'letters'    => $letters,
            'pagination' => $pagination,
        ]);
    }
}
