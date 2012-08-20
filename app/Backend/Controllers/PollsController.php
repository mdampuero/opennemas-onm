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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the poll manager
 *
 * @package Backend_Controllers
 **/
class PollsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        \Onm\Module\ModuleManager::checkActivatedOrForward('POLL_MANAGER');

        $this->checkAclOrForward('POLL_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $contentType = \Content::getIDContentType('poll');

        $category = $this->request->query->filter(INPUT_GET, 0, FILTER_SANITIZE_STRING);

        $ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $ccm->getArraysMenu($category, $contentType);

        if (empty($category)) {
            $category ='home';
        }

        $this->view->assign(array(
            'category'     => $category,
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->categoryData
        ));
    }

    /**
     * Lists all the available polls
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $page         = $request->query->getDigits('page', 1);
        $category     = $request->query->getDigits('category', 'all');
        $itemsPerPage = s::get('items_per_page') ?: 20;

        if (empty($category)) {
            $category = 'all';
            $categoryFilter = null;
        } else {
            $categoryFilter = $category;
        }

        $cm = new \ContentManager();
        $ccm = new \ContentCategoryManager();

        list($countPolls, $polls) =$cm->getCountAndSlice(
            'Poll',
            $categoryFilter,
            'in_litter != 1',
            'ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

        foreach ($polls as &$poll) {
            $poll->category_name = $ccm->get_name($poll->category);
            $poll->category_title = $ccm->get_title($poll->category_name);
        }

        // Build the pager
        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $countPolls,
            'fileName'    => $this->generateUrl('admin_polls').'?page=%d',
        ));

        return $this->render('polls/list.tpl', array(
            'polls'      => $polls,
            'pagination' => $pagination,
            'category'   => $category,
            'page'       => $page,
        ));
    }

    /**
     * Lists all the polls in the widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function widgetAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $configurations = s::get('poll_settings');
        if (array_key_exists('total_widget', $configurations)) {
            $totalWidget = $configurations['total_widget'];
        } else {
            $totalWidget = 0;
        }

        $cm = new \ContentManager();
        $ccm = new \ContentCategoryManager();

        $page         = $request->query->getDigits('page', 1);
        $category     = $request->query->getDigits('category', 'all');
        $itemsPerPage = s::get('items_per_page') ?: 20;

        list($countPolls, $polls) =$cm->getCountAndSlice(
            'Poll',
            null,
            'in_litter != 1 AND in_home=1',
            'ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

        if (count($polls) != $totalWidget ) {
            m::add( sprintf(_("You must put %d polls in the HOME"), $totalWidget));
        }

        foreach ($polls as &$poll) {
            $poll->category_name = $ccm->get_name($poll->category);
            $poll->category_title = $ccm->get_title($poll->category_name);
        }

        // Build the pager
        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $countPolls,
            'fileName'    => $this->generateUrl('admin_polls').'?page=%d',
        ));

        return $this->render('polls/list.tpl', array(
            'polls'      => $polls,
            'pagination' => $pagination,
            'category'   => 'widget',
            'page'       => $page,
        ));
    }

    /**
     * Handles the form for create new polls
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('POLL_CREATE');

        if ('POST' == $request->getMethod()) {
            $poll = new \Poll();

            $data = array(
                'title'        => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'subtitle'     => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING),
                'description'  => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'metadata'     => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'favorite'     => $request->request->getDigits('favorite', 0),
                'with_comment' => $request->request->getDigits('with_comment', 0),
                'category'     => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
                'available'    => $request->request->filter('available', 0, FILTER_SANITIZE_STRING),
                'item'         => $request->request->get('item'),
            );

            if ($poll->create($data)) {
                m::add(_('Special successfully created.'), m::SUCCESS);
            } else {
                m::add(_('Unable to create the new v.'), m::ERROR);
            }

            return $this->redirect($this->generateUrl(
                'admin_polls',
                array('category' => $data['category'])
                )
            );
        } else {
            return $this->render('polls/new.tpl');
        }
    }

    /**
     * Shows the poll information form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('POLL_UPDATE');

        $id = $request->query->getDigits('id', null);

        $poll = new \Poll($id);
        if (is_null($poll->id)) {
            m::add(sprintf(_('Unable to find the poll with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_polls'));
        }

        $items = $poll->get_items($id);

        return $this->render('polls/new.tpl', array(
            'poll'  => $poll,
            'items' => $items,
        ));
    }

    /**
     * Updates the special information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('POLL_UPDATE');

        $id = $request->query->getDigits('id');
        $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);

        $poll = new \Poll($id);
        if ($poll->id != null) {

            $data = array(
                'id'            => $id,
                'title'         => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'subtitle'      => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING),
                'description'   => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'visualization' => $request->request->filter('visualization', '', FILTER_SANITIZE_STRING),
                'metadata'      => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'favorite'      => $request->request->getDigits('favorite', 0),
                'with_comment'  => $request->request->getDigits('with_comment', 0),
                'category'      => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
                'available'     => $request->request->filter('available', 0, FILTER_SANITIZE_STRING),
                'item'          => $request->request->get('item'),
                'votes'         => $request->request->get('votes'),
            );

            if ($poll->update($data)) {
                m::add(_('Poll successfully updated.'), m::SUCCESS);
            } else {
                m::add(_('Unable to update the poll.'), m::ERROR);
            }

            if ($continue) {
                return $this->redirect($this->generateUrl(
                    'admin_poll_show',
                    array('id' => $poll->id)
                ));
            } else {
                return $this->redirect($this->generateUrl(
                    'admin_polls',
                    array('category' => $data['category'])
                ));
            }
        } else {
            m::add(sprintf(_('Unable to find a poll with the id "%s".'), $id), m::ERROR);

            return $this->redirect($this->generateUrl(
                'admin_polls',
                array('category' => $data['category'])
            ));
        }
    }

    /**
     * Delete a poll given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('POLL_DELETE');

        $id       = $request->query->getDigits('id');
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $special = new \Poll($id);
            $special->delete($id);
            m::add(_("Poll deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete a poll.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl(
                'admin_polls',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            ));
        }
    }

    /**
     * Change available status for one poll given its id
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('POLL_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        $poll = new \Poll($id);

        if (is_null($poll->id)) {
            m::add(sprintf(_('Unable to find a poll with the id "%d"'), $id), m::ERROR);
        } else {
            $poll->set_available($status, $_SESSION['user_id']);
            if ($status == 0) {
                $poll->set_favorite($status);
            }
            m::add(sprintf(_('Successfully changed availability for the poll "%s"'), $poll->title), m::SUCCESS);
        }

        return $this->redirect($this->generateUrl(
            'admin_polls',
            array(
                'category' => $category,
                'page'     => $page
            )
        ));
    }

    /**
     * Change available status for one poll given its id
     *
     * @return Response the response object
     **/
    public function toggleFavoriteAction(Request $request)
    {
        $this->checkAclOrForward('POLL_FAVORITE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        $poll = new \Poll($id);

        if (is_null($poll->id)) {
            m::add(sprintf(_('Unable to find a poll with the id "%d"'), $id), m::ERROR);
        } else {
            $poll->set_favorite($status);
            m::add(sprintf(_('Successfully changed suggested flag for the poll "%s"'), $poll->title), m::SUCCESS);
        }

        return $this->redirect($this->generateUrl(
            'admin_polls',
            array(
                'category' => $category,
                'page'     => $page
            )
        ));
    }

    /**
     * Change available status for one poll given its id
     *
     * @return Response the response object
     **/
    public function toggleInHomeAction(Request $request)
    {
        $this->checkAclOrForward('POLL_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        $poll = new \Poll($id);

        if (is_null($poll->id)) {
            m::add(sprintf(_('Unable to find a poll with the id "%d"'), $id), m::ERROR);
        } else {
            $poll->set_inhome($status);
            m::add(sprintf(_('Successfully changed suggested flag for the poll "%s"'), $poll->title), m::SUCCESS);
        }

        return $this->redirect($this->generateUrl(
            'admin_polls',
            array(
                'category' => $category,
                'page'     => $page
            )
        ));
    }

    /**
     * Deletes multiple polls at once given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('POLL_DELETE');

        $selected = $request->query->get('selected_fld', null);
        $category = $request->query->getDigits('category', 'all');
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            $changes = 0;
            foreach ($selected as $id) {
                $poll = new \Poll((int) $id);
                if (!is_null($poll->id)) {
                    $poll->delete($id, $_SESSION['userid']);
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find a poll with the id "%d"'), $id), m::ERROR);
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully deleted %d polls.'), $changes), m::SUCCESS);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl(
                'admin_polls',
                array(
                    'category' => $category,
                    'page'     => $page,
                )
            ));
        }

    }

    /**
     * Changes the available status for polls given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchPublishAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_DELETE');

        $status   = $request->query->getDigits('status', 0);
        $selected = $request->query->get('selected_fld', null);
        $category = $request->query->getDigits('category', 'all');
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            $changes = 0;
            foreach ($selected as $id) {
                $poll = new \Poll((int) $id);
                if (!is_null($poll->id)) {
                    $poll->set_available($status, $_SESSION['userid']);
                    if ($status == 0) {
                        $poll->set_favorite($status);
                    }
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find a poll with the id "%d"'), $id), m::ERROR);
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully changed the available status of %d polls.'), $changes), m::SUCCESS);
        }

        return $this->redirect($this->generateUrl(
            'admin_polls',
            array(
                'category' => $category,
                'page'     => $page,
            )
        ));
    }

    /**
     * Lists all the polls withing a category for the related manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderRelatedAction(Request $request)
    {
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        if ($category == 0) {
            $categoryFilter = null;
        } else {
            $categoryFilter = $category;
        }
        $cm = new  \ContentManager();

        list($countPolls, $polls) = $cm->getCountAndSlice(
            'Poll',
            $categoryFilter,
            'contents.available=1',
            ' ORDER BY starttime DESC, contents.title ASC ',
            $page,
            $itemsPerPage
        );

        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $countPolls,
            'fileName'    => $this->generateUrl('admin_polls_content_provider_related', array(
                'category' => $category,
            )).'&page=%d',
        ));

        return $this->render('common/content_provider/_container-content-list.tpl', array(
            'contentType'           => 'Poll',
            'contents'              => $polls,
            'contentTypeCategories' => $this->parentCategories,
            'category'              => $category,
            'pagination'            => $pagination->links,
            'contentProviderUrl'    => $this->generateUrl('admin_polls_content_provider_related'),
        ));
    }

    /**
     * Handles the configuration for the polls module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        $this->checkAclOrForward('POLLS_SETTINGS');

        if ('POST' == $request->getMethod()) {
            $settingsRAW = $request->request->get('poll_settings');
            $data = array(
                'poll_settings' => array(
                    'typeValue'  => $settingsRAW['typeValue'] ?: 0,
                    'heightPoll' => $settingsRAW['heightPoll'] ?: 0,
                    'widthPoll'  => $settingsRAW['widthPoll'] ?: 0,
                    'total_widget' => $settingsRAW['total_widget'] ?: 0,
                    'widthWidget'  => $settingsRAW['widthWidget'] ?: 0,
                    'heightWidget' => $settingsRAW['heightWidget'] ?: 0,
                )
            );

            foreach ($data as $key => $value) {
                s::set($key, $value);
            }
            m::add(_('Settings saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_polls'));
        } else {
            $configurations = s::get(array('poll_settings',));

            return $this->render('polls/config.tpl', array(
                'configs'   => $configurations,
            ));
        }
    }

} // END class PollsController
