<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Onm\StringUtils;
use Onm\Message as m;
use Onm\Settings as s;

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
        $this->checkAclOrForward('STATIC_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Description of the action
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function listAction()
    {
        $filter        = null;
        $request       = $this->get('request');
        $requestFilter = $request->query->get('filter');
        if (is_array($requestFilter)
            && array_key_exists('title', $requestFilter)) {
            $filter = '`title` LIKE "%' . $requestFilter['title'] . '%"';
        }

        $page       = $request->query->filter('page', 0, FILTER_VALIDATE_INT);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $cm = new \ContentManager();
        list($pages, $pager) = $cm->find_pages(
            'StaticPage', $filter, 'ORDER BY created DESC ', $page, $itemsPerPage
        );

        return $this->render('static_pages/list.tpl', array(
            'pages' => $pages,
            'pager' => $pager,
        ));
    }

    /**
     * Shows the edit form
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function showAction()
    {
        $request = $this->get('request');
        $id      = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        if (!is_null($id)) {
            $staticPage = new \StaticPage();
            $staticPage->read($id);

            return $this->render('static_pages/read.tpl', array(
                'id'   => $id,
                'page' => $staticPage,
            ));
        } else {
            m::add(sprintf(_('Static page with id "%d" doesn\'t exists.'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_staticpages'));
        }
    }

    /**
     * Handles the creation of a new static page
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function createAction()
    {
        if ('POST' != $this->request->getMethod()) {
            return $this->render('static_pages/read.tpl');
        } else {

            $request    = $this->get('request');
            $staticPage = new \StaticPage();

            $data = array(
                    'title'        => $request->request->filter('title', null, FILTER_SANITIZE_STRING),
                    'body'         => $request->request->filter('body', null, FILTER_SANITIZE_STRING),
                    'slug'         => $request->request->filter('slug', null, FILTER_SANITIZE_STRING),
                    'description'  => $request->request->filter('description', null, FILTER_SANITIZE_STRING),
                    'metadata'     => $request->request->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'fk_publisher' => $_SESSION['userid'],
                    'category'     => 0,
                    'id'           => 0,
                );
            $data = array_merge(
                $data,
                array(
                    'slug'     => $staticPage->buildSlug($data['slug'], $data['id'], $data['title']),
                    'metadata' => \StringUtils::normalize_metadata($data['metadata']),
                )
            );

            $staticPage->save($data);

            return $this->redirect($this->generateUrl('admin_staticpages'));
        }
    }

    /**
     * Updates a static page given its id
     *
     * @return Response the response object
     **/
    public function updateAction()
    {
        $this->checkAclOrForward('STATIC_UPDATE');

        $request    = $this->get('request');
        $id         = $request->query->getDigits('id');
        $continue   = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
        $staticPage = new \StaticPage($id);

        if ($staticPage->id != null) {

            if (!\Acl::isAdmin()
                && !\Acl::check('CONTENT_OTHER_UPDATE')
                && $staticPage->pk_user != $_SESSION['userid']) {
                m::add(_("You can't modify this static page because you don't have enought privileges."));
            } else {

                $data = array(
                    'title'        => $request->request->filter('title', null, FILTER_SANITIZE_STRING),
                    'body'         => $request->request->filter('body', null, FILTER_SANITIZE_STRING),
                    'slug'         => $request->request->filter('slug', null, FILTER_SANITIZE_STRING),
                    'description'  => $request->request->filter('description', null, FILTER_SANITIZE_STRING),
                    'metadata'     => $request->request->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'fk_publisher' => $_SESSION['userid'],
                    'id'           => $id,
                );
                $data = array_merge(
                    $data,
                    array(
                        'slug'     => $staticPage->buildSlug($data['slug'], 0, $data['title']),
                        'metadata' => \StringUtils::normalize_metadata($data['metadata']),
                    )
                );

                $staticPage->update($data);
                m::add(_("Static page updated successfully."), m::SUCCESS);
            }

            if ($continue) {
                return $this->redirect($this->generateUrl(
                    'admin_staticpage_show',
                    array('id' => $staticPage->id)
                ));
            } else {
                $page = $request->request->getDigits('page', 1);

                return $this->redirect($this->generateUrl(
                    'admin_staticpages',
                    array(
                        'page'     => $page,
                    )
                ));
            }
        }
    }

    /**
     * Deletes an static page given its id
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function deleteAction()
    {
        $this->checkAclOrForward('STATIC_DELETE');

        $request = $request = $this->get('request');
        $id      = $request->getDigits('id');
        $page    = $request->getDigits('page', 1);
var_dump($id );
        if (!empty($id)) {
            $staticPage = new \StaticPage($id);
            $staticPage->delete($id, $_SESSION['userid']);
            m::add(_("Static Page '{$page->title}' deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete the static page.'), m::ERROR);
        }

        return $this->redirect($this->generateUrl(
            'admin_staticpages',
            array(
                'page' => $staticPage
            )
        ));
    }

    /**
     * Change availability for one page given its id
     *
     * @return Response the response object
     **/
    public function toggleAvailabilityAction()
    {
        \Acl::checkOrForward('STATIC_AVAILABLE');

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
            m::add(sprintf(_('Successfully changed availability for page with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect($this->generateUrl(
            'admin_staticpages',
            array(
                'page'     => $page
            )
        ));
    }

    /**
     * Change slug for one static page given its id
     *
     * @return Ajax Response the response object
     **/

    public function  buildSlugAction()
    {
        $request  = $this->get('request');
        /**
         * If the action is an Ajax request handle it, if not redirect to list
         */
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
                $output = _( "Can't get static page title. Check the title");
            }
             return new Response($output);
        }


    }

    /**
     * Change metadata for one static page given its id
     *
     * @return Response the response object
     **/
    public function  cleanMetadataAction()
    {
        $request  = $this->get('request');
        $metadata = $request->request->filter('metadata', null, FILTER_SANITIZE_STRING);
        /**
         * If the action is an Ajax request handle it, if not redirect to list
         */
        if ($request->isXmlHttpRequest()) {
            try {
                $output  = StringUtils::normalize_metadata($metadata);

            } catch (\Exception $e) {
                $output = _( "Can't get static page metadata.");
            }

        }

        return new Response($output);

    }

} // END class StaticPagesController