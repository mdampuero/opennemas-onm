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

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\StringUtils;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class StaticPagesController extends Controller
{
    /**
     * Shows a list of the static pages
     *
     * @return void
     *
     * @Security("hasExtension('STATIC_PAGES_MANAGER')
     *     and hasPermission('STATIC_PAGE_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('static_pages/list.tpl');
    }

    /**
     * Shows the edit form
     *
     * @param Request $request the request object
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     *
     * @Security("hasExtension('STATIC_PAGES_MANAGER')
     *     and hasPermission('STATIC_PAGE_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        if (!is_null($id)) {
            $staticPage = new \StaticPage();
            $staticPage->read($id);

            return $this->render(
                'static_pages/new.tpl',
                array(
                    'id'   => $id,
                    'page' => $staticPage,
                )
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a static page with the id "%d".'), $id)
            );

            return $this->redirect($this->generateUrl('admin_static_pages'));
        }
    }

    /**
     * Handles the creation of a new static page
     *
     * @param Request $request the request object
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     *
     * @Security("hasExtension('STATIC_PAGES_MANAGER')
     *     and hasPermission('STATIC_PAGE_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return $this->render('static_pages/new.tpl');
        } else {
            $staticPage = new \StaticPage();

            $data = array(
                'title'          => $request->request->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'body'           => $request->request->get('body', ''),
                'slug'           => $request->request->filter('slug', null, FILTER_SANITIZE_STRING),
                'metadata'       => $request->request->filter('metadata', null, FILTER_SANITIZE_STRING),
                'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
                'fk_publisher'   => $this->getUser()->id,
                'category'       => 0,
                'id'             => 0,
            );

            $staticPage->create($data);

            $this->get('session')->getFlashBag()->add('success', _('Static page created successfully.'));

            return $this->redirect(
                $this->generateUrl('admin_static_page_show', array('id' => $staticPage->id))
            );
        }
    }

    /**
     * Updates a static page given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('STATIC_PAGES_MANAGER')
     *     and hasPermission('STATIC_PAGE_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id         = $request->query->getDigits('id');
        $staticPage = new \StaticPage($id);

        if (!is_null($staticPage->id)) {
            if (!Acl::isAdmin()
                && !Acl::check('CONTENT_OTHER_UPDATE')
                && !$staticPage->isOwner($this->getUser()->id)
            ) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("You can't modify this static page because you don't have enough privileges.")
                );
            } else {
                // Check empty data
                if (count($request->request) < 1) {
                    $this->get('session')->getFlashBag()->add('error', _("Static Page data sent not valid."));

                    return $this->redirect($this->generateUrl('admin_static_page_show', array('id' => $id)));
                }

                $data = array(
                    'title'          => $request->request->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                    'body'           => $request->request->get('body', ''),
                    'slug'           => $request->request->filter('slug', null, FILTER_SANITIZE_STRING),
                    'metadata'       => $request->request->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
                    'fk_publisher'   => $this->getUser()->id,
                    'id'             => $id,
                );

                $staticPage->update($data);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Static page updated successfully.")
                );
            }

            return $this->redirect(
                $this->generateUrl('admin_static_page_show', array('id' => $staticPage->id))
            );
        }
    }

    /**
     * Change slug for one static page given its id
     *
     * @param Request $request the request object
     *
     * @return Ajax Response the response object
     **/
    public function buildSlugAction(Request $request)
    {
        // If the action is an Ajax request handle it, if not redirect to list
        $data = array(
            'title'    => $request->request->filter('title', null, FILTER_SANITIZE_STRING),
            'slug'     => $request->request->filter('slug', null, FILTER_SANITIZE_STRING),
            'metadata' => $request->request->filter('metadata', null, FILTER_SANITIZE_STRING),
            'id'       => $request->request->filter('id', 0, FILTER_SANITIZE_STRING),
        );

        if ($request->isXmlHttpRequest()) {
            try {
                $page = new \StaticPage();
                $output = $page->buildSlug($data['slug'], $data['id'], $data['title']);
            } catch (\Exception $e) {
                $output = _("Can't get static page title. Check the title");
            }

            return new Response($output);
        }

    }
}
