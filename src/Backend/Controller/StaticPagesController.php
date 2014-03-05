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
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;
use Onm\StringUtils;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class StaticPagesController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return Response the response object
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('STATIC_PAGES_MANAGER');
    }

    /**
     * Shows a list of the static pages
     *
     * @param Request $request the request object
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     *
     * @Security("has_role('STATIC_ADMIN')")
     **/
    public function listAction(Request $request)
    {
        $requestFilter = $request->query->get('filter');
        $page          = $request->query->getDigits('page', 1);
        $itemsPerPage  = s::get('items_per_page', 20);

        $filter        = 'contents.in_litter != 1';
        if (is_array($requestFilter)
            && array_key_exists('title', $requestFilter)) {
            $filter .= ' AND `title` LIKE "%' . $requestFilter['title'] . '%"';
        }

        $cm = new \ContentManager();
        list($countPages, $pages) = $cm->getCountAndSlice(
            'StaticPage',
            null,
            $filter,
            'ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countPages,
                'fileName'    => $this->generateUrl('admin_staticpages').'?page=%d',
            )
        );

        return $this->render(
            'static_pages/list.tpl',
            array(
                'pages' => $pages,
                'pager' => $pagination,
            )
        );
    }

    /**
     * Shows the edit form
     *
     * @param Request $request the request object
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     *
     * @Security("has_role('STATIC_UPDATE')")
     **/
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
            m::add(sprintf(_('Unable to find a static page with the id "%d".'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_staticpages'));
        }
    }

    /**
     * Handles the creation of a new static page
     *
     * @param Request $request the request object
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     *
     * @Security("has_role('STATIC_CREATE')")
     **/
    public function createAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return $this->render('static_pages/new.tpl');
        } else {

            $staticPage = new \StaticPage();

            $data = array(
                    'title'        => $request->request->filter('title', null, FILTER_SANITIZE_STRING),
                    'body'         => $request->request->filter('body', null, FILTER_SANITIZE_STRING),
                    'slug'         => $request->request->filter('slug', null, FILTER_SANITIZE_STRING),
                    'metadata'     => $request->request->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'available'    => $request->request->filter('available', null, FILTER_SANITIZE_STRING),
                    'fk_publisher' => $_SESSION['userid'],
                    'category'     => 0,
                    'id'           => 0,
                );
            $data = array_merge(
                $data,
                array(
                    'slug'     => $staticPage->buildSlug($data['slug'], $data['id'], $data['title']),
                    'metadata' => \Onm\StringUtils::normalize_metadata($data['metadata']),
                )
            );

            $staticPage->create($data);

            m::add(_('Static page created successfully.'), m::SUCCESS);

            return $this->redirect(
                $this->generateUrl('admin_staticpage_show', array('id' => $staticPage->id))
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
     * @Security("has_role('STATIC_UPDATE')")
     **/
    public function updateAction(Request $request)
    {
        $id         = $request->query->getDigits('id');
        $staticPage = new \StaticPage($id);

        if (!is_null($staticPage->id)) {
            if (!Acl::isAdmin()
                && !Acl::check('CONTENT_OTHER_UPDATE')
                && !$staticPage->isOwner($_SESSION['userid'])
            ) {
                m::add(_("You can't modify this static page because you don't have enough privileges."));
            } else {
                // Check empty data
                if (count($request->request) < 1) {
                    m::add(_("Static Page data sent not valid."), m::ERROR);

                    return $this->redirect($this->generateUrl('admin_staticpage_show', array('id' => $id)));
                }

                $data = array(
                    'title'        => $request->request->filter('title', null, FILTER_SANITIZE_STRING),
                    'body'         => $request->request->filter('body', null, FILTER_SANITIZE_STRING),
                    'slug'         => $request->request->filter('slug', null, FILTER_SANITIZE_STRING),
                    'metadata'     => $request->request->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'available'    => $request->request->filter('available', null, FILTER_SANITIZE_STRING),
                    'fk_publisher' => $_SESSION['userid'],
                    'id'           => $id,
                );
                $data = array_merge(
                    $data,
                    array(
                        'slug'     => $staticPage->buildSlug($data['slug'], 0, $data['title']),
                        'metadata' => \Onm\StringUtils::normalize_metadata($data['metadata']),
                    )
                );

                $staticPage->update($data);
                m::add(_("Static page updated successfully."), m::SUCCESS);
            }

            return $this->redirect(
                $this->generateUrl('admin_staticpage_show', array('id' => $staticPage->id))
            );
        }
    }

    /**
     * Deletes an static page given its id
     *
     * @param Request $request the request object
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     *
     * @Security("has_role('STATIC_DELETE')")
     **/
    public function deleteAction(Request $request)
    {
        $id      = $request->query->getDigits('id');
        $page    = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $staticPage = new \StaticPage($id);
            $staticPage->delete($id);
            m::add(sprintf(_("Static Page '%s' deleted successfully."), $staticPage->title), m::SUCCESS);
        } else {
            m::add(_('You must provide a valid an id for delete the static page.'), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl('admin_staticpages', array('page' => $page))
        );
    }

    /**
     * Change availability for one page given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('STATIC_AVAILABLE')")
     **/
    public function toggleAvailableAction(Request $request)
    {
        $request  = $this->get('request');
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);

        $staticPage = new \StaticPage($id);

        if (is_null($staticPage->id)) {
            m::add(sprintf(_('Unable to find page with id "%d"'), $id), m::ERROR);
        } else {
            $staticPage->toggleAvailable($staticPage->id);
            if ($status == 0) {
                $staticPage->set_favorite($status);
            }
            m::add(
                sprintf(
                    _('Successfully changed availability for page with id "%s"'),
                    $staticPage->title
                ),
                m::SUCCESS
            );
        }

        return $this->redirect(
            $this->generateUrl('admin_staticpages', array('page' => $page))
        );
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

    /**
     * Change metadata for one static page given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function cleanMetadataAction(Request $request)
    {
        $metadata = $request->request->filter('metadata', null, FILTER_SANITIZE_STRING);
        // If the action is an Ajax request handle it, if not redirect to list
        if ($request->isXmlHttpRequest()) {
            try {
                $output  = StringUtils::normalize_metadata($metadata);
            } catch (\Exception $e) {
                $output = _("Can't get static page metadata.");
            }
        }

        return new Response($output);
    }
}
