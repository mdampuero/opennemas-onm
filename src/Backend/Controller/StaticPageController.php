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
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Entity\Content;
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
        $req = $request->request;

        // If the action is an Ajax request handle it, if not redirect to list
        $data = [
            'title'    => $req->filter('title', null, FILTER_SANITIZE_STRING),
            'slug'     => $req->filter('slug', null, FILTER_SANITIZE_STRING),
            'id'       => $req->filter('id', 0, FILTER_SANITIZE_STRING),
        ];

        if ($request->isXmlHttpRequest()) {
            try {
                $page   = new \StaticPage();
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
        $ls = $this->get('core.locale');
        return $this->render('static_pages/new.tpl', [
            'locale' => $ls->getRequestLocale('frontend'),
            'tags'   => []
        ]);
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
        $tagIds = json_decode($request->request->get('tag_ids', ''), true);

        $entity->contentTypeName     = 'static_page';
        $entity->fk_content_type     = 13;
        $entity->created             = new \DateTime();
        $entity->fk_author           = $this->get('core.user')->id;
        $entity->fk_publisher        = $entity->fk_author;
        $entity->fk_user_last_editor = $entity->fk_author;

        try {
            $em->persist($entity);
            $contentAux     = new \Content();
            $contentAux->id = $entity->pk_content;
            $contentAux->addTags($tagIds);

            $this->get('session')->getFlashBag()
                ->add('success', _('Content saved successfully'));

            $this->get('core.dispatcher')
                ->dispatch('content.create', [ 'content' => $entity ]);

            return $this->redirect(
                $this->generateUrl(
                    'backend_static_page_show',
                    [ 'id' => $entity->pk_content ]
                )
            );
        } catch (\Exception $e) {
            $this->get('error.log')->error($e->getMessage());

            $this->get('session')->getFlashBag()
                ->add('error', _('There were errors while creating the content'));

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
    public function showAction(Request $request)
    {
        $id = $request->query->getInt('id');
        try {
            $entity = $this->get('orm.manager')
                ->getRepository('Content')
                ->find($id);

            $entity->id      = $entity->pk_content;
            $contentAux      = new \Content();
            $contentAux->id  = $entity->id;
            $auxTagIds       = $contentAux->getContentTags($entity->id);
            $entity->tag_ids = array_key_exists($entity->id, $auxTagIds) ?
                $auxTagIds[$entity->id] :
                [];
        } catch (EntityNotFoundException $e) {
            $request->getSession()->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the static page with the id "%d"'), $id)
            );
            return $this->redirect($this->generateUrl('backend_static_pages_list'));
        }

        $ls = $this->get('core.locale');
        return $this->render('static_pages/new.tpl', [
            'page'   => $entity,
            'locale' => $ls->getRequestLocale('frontend'),
            'tags'   => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($entity->tag_ids)['items']
        ]);
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
                ->add('error', _('You don\'t have enough privileges to execute this action'));

            return $this->redirect($url);
        }

        $entity->setData($converter->objectify($request->request->all()));
        $tagIds = json_decode($request->request->get('tag_ids', ''), true);

        $entity->changed = new \DateTime();

        // TODO: Remove after fixing database definition
        $entity->category_name = ' ';

        // TODO:Remove when data supports empty values (when using SPA)
        $status = $request->request->filter('content_status', '', FILTER_SANITIZE_STRING);

        $entity->content_status = (empty($status)) ? 0 : 1;

        try {
            $contentAux              = new \Content();
            $contentAux->id          = $id;
            $entity->data['tag_ids'] = $contentAux->addTags($tagIds);
            $entity->tag_ids         = $entity->data['tag_ids'];

            $em->persist($entity);

            // TODO: Remove when static pages list ported to the new ORM
            $this->get('cache')->delete('static_page-' . $id);

            $this->get('session')->getFlashBag()
                ->add('success', _('Content updated successfully'));

            $this->get('core.dispatcher')
                ->dispatch('content.update', [ 'content' => $entity ]);
        } catch (\Exception $e) {
            $this->get('error.log')->error($e->getMessage());

            $this->get('session')->getFlashBag()
                ->add('error', _('There were errors while updating the content'));
        }

        return $this->redirect($url);
    }
}
