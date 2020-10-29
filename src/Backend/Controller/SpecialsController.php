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

use Api\Exception\GetItemException;
use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecialsController extends Controller
{
    /**
     * List all the specials in a category
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('special/list.tpl');
    }

    /**
     * Handles the form for create new specials
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            $ls = $this->get('core.locale');
            return $this->render('special/new.tpl', [
                'locale' => $ls->getLocale('frontend'),
            ]);
        }

        $special = new \Special();

        $data = [
            'title'          => $request->request
                ->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'pretitle'       => $request->request
                ->filter('pretitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'    => $request->request->get('description', ''),
            'slug'           => $request->request->filter('slug', '', FILTER_SANITIZE_STRING),
            'category_id'    => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
            'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'in_home'        => $request->request->filter('in_home', 0, FILTER_SANITIZE_STRING),
            'favorite'       => $request->request->filter('favorite', 0, FILTER_SANITIZE_STRING),
            'img1'           => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
            'category_imag'  => $request->request->filter('category_imag', '', FILTER_SANITIZE_STRING),
            'noticias_right' => json_decode($request->request->get('noticias_right_input')),
            'noticias_left'  => json_decode($request->request->get('noticias_left_input')),
            'tags'           => json_decode($request->request->get('tags', ''), true)
        ];

        if ($special->create($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Special successfully created.')
            );

            return $this->redirect(
                $this->generateUrl('admin_special_show', ['id' => $special->id])
            );
        } else {
            $this->get('session')->getFlashBag()->add('error', _('Unable to create the new special.'));

            return $this->redirect($this->generateUrl('admin_special_create'));
        }
    }

    /**
     * Shows the special information form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id      = $request->query->getDigits('id', null);
        $special = $this->get('entity_repository')->find('Special', $id);

        if (is_null($special->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the special with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_specials'));
        }

        $contents = $special->getContents($id);
        $service  = $this->get('api.service.photo');
        try {
            $photo1 = $service->getItem($special->img1);

            $this->view->assign('photo1', $service->responsify($photo1));
        } catch (GetItemException $e) {
        }

        $contentsLeft  = [];
        $contentsRight = [];

        if (!empty($contents)) {
            foreach ($contents as $content) {
                if (($content['position'] % 2) == 0) {
                    $contentsRight[] = new \Content($content['fk_content']);
                } else {
                    $contentsLeft[] = new \Content($content['fk_content']);
                }
            }

            $this->view->assign([
                'contentsRight' => $contentsRight,
                'contentsLeft'  => $contentsLeft,
            ]);
        }

        $ls = $this->get('core.locale');
        return $this->render('special/new.tpl', [
            'special'  => $special,
            'category' => $special->category_id,
            'locale'   => $ls->getRequestLocale('frontend'),
        ]);
    }

    /**
     * Updates the special information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $special = new \Special($id);

        if ($special->id != null) {
            // Check empty data
            if (empty($request->request)) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("Special data sent not valid.")
                );

                return $this->redirect($this->generateUrl('admin_special_show', [ 'id' => $id ]));
            }

            $data = [
                'id'             => $id,
                'title'          => $request->request
                    ->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'pretitle'       => $request->request
                    ->filter('pretitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'description'    => $request->request->get('description', ''),
                'slug'           => $request->request->filter('slug', '', FILTER_SANITIZE_STRING),
                'category_id'    => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
                'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
                'in_home'        => $request->request->filter('in_home', 0, FILTER_SANITIZE_STRING),
                'favorite'       => $request->request->filter('favorite', 0, FILTER_SANITIZE_STRING),
                'img1'           => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'category_imag'  => $request->request->filter('category_imag', '', FILTER_SANITIZE_STRING),
                'noticias_left'  => json_decode($request->request->get('noticias_left_input')),
                'noticias_right' => json_decode($request->request->get('noticias_right_input')),
                'tags'           => json_decode($request->request->get('tags', ''), true)
            ];

            if ($special->update($data)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Special successfully updated.')
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to update the special.')
                );
            }

            return $this->redirect($this->generateUrl('admin_special_show', [
                'id' => $special->id
            ]));
        }
    }

    /**
     * Delete a special given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $id       = $request->query->getDigits('id');
        $category = $request->query->filter('category_id', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $special = new \Special($id);

            $special->delete($id);
            $this->get('session')->getFlashBag()->add(
                'success',
                _("Special deleted successfully.")
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must give an id for delete a special.')
            );
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('admin_specials', [
                'category' => $category,
                'page'     => $page
            ]));
        } else {
            return new Response('Ok', 200);
        }
    }
}
