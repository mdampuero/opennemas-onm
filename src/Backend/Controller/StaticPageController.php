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
use Common\ORM\Entity\Content;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the actions for static pages.
 */
class StaticPageController extends Controller
{
    /**
     * Change slug for one static page given its id
     *
     * @param Request $request the request object
     *
     * @return Ajax Response the response object
     */
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

    /**
     * Shows the form to create a static page.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('STATIC_PAGES_MANAGER')
     *     and hasPermission('STATIC_PAGE_UPDATE')")
     */
    public function createAction()
    {
        return $this->render('static_pages/new.tpl');
    }

    /**
     * Shows a list of the static pages
     *
     * @Security("hasExtension('STATIC_PAGES_MANAGER')
     *     and hasPermission('STATIC_PAGE_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('static_pages/list.tpl');
    }

    /**
     * Create a new static page.
     *
     * @param Request $request the request object
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     *
     * @Security("hasExtension('STATIC_PAGES_MANAGER')
     *     and hasPermission('STATIC_PAGE_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('Content');

        $entity = new Content($converter->objectify($request->request->all()));
        $entity->contentTypeName     = 'static_page';
        $entity->fk_content_type     = 13;
        $entity->fk_author           = $this->get('core.user')->id;
        $entity->fk_publisher        = $entity->fk_author;
        $entity->fk_user_last_editor = $entity->fk_author;

        try {
            $em->persist($entity);

            $this->get('session')->getFlashBag()
                ->add('success', _('Content saved successfully.'));

            $this->get('core.dispatcher')
                ->dispatch('content.create', [ 'content' => $entity ]);

            return $this->redirect(
                $this->generateUrl(
                    'backend_static_page_show',
                    [ 'id' => $entity->pk_content ]
                )
            );
        } catch (\Exception $e) {
            error_log($e->getMessage());

            $this->get('session')->getFlashBag()
                ->add('error', _('There were errors while creating the content.'));

            return $this->redirect($this->generateUrl('backend_static_page_create'));
        }
    }

    /**
     * Shows the form to update a static page.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('STATIC_PAGES_MANAGER')
     *     and hasPermission('STATIC_PAGE_UPDATE')")
     */
    public function showAction($id)
    {
        $entity = $this->get('orm.manager')
            ->getRepository('content')
            ->find($id);

        $entity->id = $entity->pk_content;

        return $this->render('static_pages/new.tpl', [ 'page' => $entity ]);
    }

    /**
     * Updates a static page.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('STATIC_PAGES_MANAGER')
     *     and hasPermission('STATIC_PAGE_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('Content');
        $entity    = $em->getRepository('Content')->find($id);
        $security  = $this->get('core.security');

        $url = $this->generateUrl('backend_static_page_show', [ 'id' => $id ]);

        // TODO: Remove isAdmin after fixing permissions in database
        if (!$this->get('core.user')->isAdmin()
            && !$security->hasPermission('CONTENT_OTHER_UPDATE')
            && $entity->fk_publisher !== $this->get('core.user')->id
        ) {
            $this->get('session')->getFlashBag()
                ->add('error', _('You don\'t have enough privileges to modify this content.'));

            return $this->redirect($url);
        }

        $entity->setData($converter->objectify($request->request->all()));

        // TODO: Remove after fixing database definition
        $entity->category_name = ' ';

        try {
            $em->persist($entity);

            $this->get('session')->getFlashBag()
                ->add('success', _('Content updated successfully.'));

            $this->get('core.dispatcher')
                ->dispatch('content.update', [ 'content' => $entity ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            $this->get('session')->getFlashBag()
                ->add('error', _('There were errors while updating the content'));
        }

        return $this->redirect($url);
    }
}
