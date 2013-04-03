<?php
/**
 * Handles the actions for managing opinions
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
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;
use \Onm\Module\ModuleManager;

/**
 * Handles the actions for managing opinions
 *
 * @package Backend_Controllers
 **/
class OpinionsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        ModuleManager::checkActivatedOrForward('OPINION_MANAGER');

        $this->checkAclOrForward('OPINION_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->ccm  = \ContentCategoryManager::get_instance();

        list($this->parentCategories, $this->subcat, $this->categoryData)
            = $this->ccm->getArraysMenu();

        $this->view->assign(
            array(
                'allcategorys' => $this->parentCategories,
            )
        );

    }

    /**
     * Lists all the opinions
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $page   =  $request->query->getDigits('page', 1);
        $author =  (int) $request->query->filter('author', 0, FILTER_VALIDATE_INT);
        $status =  (int) $request->query->filter('status', -1);

        $itemsPerPage = s::get('items_per_page');

        $filterSQL = array('in_litter != 1');
        $filterStatus = $filterAuthor = '';
        if ($status >= 0) {
            $filterSQL []= ' content_status='.$status;
        }

        if ($author != 0) {
            if ($author > 0) {
                $filterSQL []= 'opinions.fk_author='.$author;
            } elseif ($author == -1) {
                $filterSQL []= 'opinions.type_opinion=2';
            } elseif ($author == -2) {
                $filterSQL []= 'opinions.type_opinion=1';
            }
        }

        $filterSQL = implode(' AND ', $filterSQL);

        $cm      = new \ContentManager();
        $rating  = new \Rating();
        $comment = new \Comment();

        list($countOpinions, $opinions)= $cm->getCountAndSlice(
            'Opinion',
            null,
            $filterSQL,
            'ORDER BY content_status, available, created DESC',
            $page,
            $itemsPerPage
        );

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countOpinions,
                'fileName'    => $this->generateUrl(
                    'admin_opinions',
                    array(
                        'status' => $status,
                        'author' => $author,
                    )
                ).'&page=%d',
            )
        );

        if (isset($opinions) && is_array($opinions)) {
            foreach ($opinions as &$opinion) {
                $opinion->author = new \Author($opinion->fk_author);
                $opinion->comments = count($comment->get_comments($opinion->id));
                $opinion->ratings = $rating->getValue($opinion->id);
            }
        } else {
            $opinions = array();
        }

        $aut     = new \Author();
        $authors = $aut->all_authors(null, 'ORDER BY name');

        return $this->render(
            'opinion/list.tpl',
            array(
                'autores'    => $authors,
                'opinions'   => $opinions,
                'page'       => $page,
                'status'     => $status,
                'author'     => $author,
                'home'       => false,
                'pagination' => $pagination,
                'total'      => $countOpinions,
            )
        );
    }

    /**
     * Manages the frontpage of opinion
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        $page =  $request->query->getDigits('page', 1);
        $configurations = s::get('opinion_settings');
        $itemsPerPage = s::get('items_per_page');

        $numEditorial = $configurations['total_editorial'];
        $numDirector  = $configurations['total_director'];

        $cm = new \ContentManager();
        $rating = new \Rating();
        $comment = new \Comment();

        $opinions = $cm->find(
            'Opinion',
            'in_home=1 and available=1 and type_opinion=0',
            'ORDER BY position ASC , created DESC'
        );

        if ($numEditorial > 0) {
            $editorial = $cm->find(
                'Opinion',
                'in_home=1 and available=1 and type_opinion=1',
                'ORDER BY position ASC, created DESC LIMIT 0,'.$numEditorial
            );
        }
        if ($numDirector >0) {
            $director = $cm->find(
                'Opinion',
                'in_home=1 and available=1 and type_opinion=2',
                'ORDER BY position ASC , created DESC LIMIT 0,'.$numDirector
            );
        }
        if (($numEditorial > 0) && (count($editorial) != $numEditorial)) {
            $type = 'editorial';
            m::add(sprintf(_("You must put %d opinions %s in the home widget"), $numEditorial, $type));
        }
        if (($numDirector>0) && (count($director) != $numDirector)) {
             $type = 'opinion del director';
             m::add(sprintf(_("You must put %d opinions %s in the home widget"), $numDirector, $type));
        }

        if (isset($editorial) && is_array($editorial)) {
            foreach ($editorial as &$opin) {
                $todos = $comment->get_comments($opin->id);
                $opin->comments = count($todos);
                $opin->ratings = $rating->getValue($opin->id);
            }
        } else {
            $editorial = array();
        }

        if (isset($director) && is_array($director)) {
            foreach ($director as &$opin) {
                $todos = $comment->get_comments($opin->id);
                $opin->comments = count($todos);
                $opin->ratings = $rating->getValue($opin->id);
            }
        } else {
            $director = array();
        }

        if (isset($opinions) && is_array($opinions)) {
            foreach ($opinions as &$opinion) {
                $opinion->author = new \Author($opinion->fk_author);
                $opinion->comments = count($comment->get_comments($opinion->id));
                $opinion->ratings = $rating->getValue($opinion->id);
            }
        } else {
            $opinions = array();
        }

        $aut     = new \Author();
        $authors = $aut->all_authors(null, 'ORDER BY name');

        return $this->render(
            'opinion/list.tpl',
            array(
                'autores'    => $authors,
                'opinions'   => $opinions,
                'director'   => $director,
                'editorial'  => $editorial,
                'type'       => 'frontpage',
                'page'       => $page,
                'home'       => true,
            )
        );
    }

    /**
     * Shows the information form for a opinion given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_UPDATE');

        $id = $request->query->getDigits('id', null);

        $opinion = new \Opinion($id);

        if (is_null($opinion->id)) {
            m::add(sprintf(_('Unable to find the opinion with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_opinions'));
        }

        if (!empty($opinion->image)) {
            $image = new \Photo($opinion->image);
            $this->view->assign('image', $image);
        }

        $author      = new \Author();
        $allAuthors  = $author->all_authors(null, 'ORDER BY name');
        $author      = new \Author($opinion->fk_author);

        $photo       = $author->get_photo($opinion->fk_author_img);
        $photoWidget = $author->get_photo($opinion->fk_author_img_widget);
        $photos      = $author->get_author_photos($opinion->fk_author);

        // Photos de noticia
        if (!empty($opinion->img1)) {
            $photo1 = new \Photo($opinion->img1);
            $this->view->assign('photo1', $photo1);
        }

        if (!empty($opinion->img2)) {
            $photo2 = new \Photo($opinion->img2);
            $this->view->assign('photo2', $photo2);
        }

        return $this->render(
            'opinion/new.tpl',
            array(
                'opinion'      => $opinion,
                'all_authors'  => $allAuthors,
                'author'       => $author->name,
                'photo'        => $photo,
                'photo_widget' => $photoWidget,
                'photos'       => $photos,
            )
        );
    }

    /**
     * Handles the form for creating a new opinion
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_CREATE');

        if ('POST' == $request->getMethod()) {
            $opinion = new \Opinion();

            $available   = $request->request->filter('available', '', FILTER_SANITIZE_STRING);
            $inhome      = $request->request->filter('in_home', '', FILTER_SANITIZE_STRING);
            $withComment = $request->request->filter('with_comment', '', FILTER_SANITIZE_STRING);

            $data = array(
                'title'                => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'category'             => 'opinion',
                'available'            => (empty($available)) ? 0 : 1,
                'in_home'              => (empty($inhome)) ? 0 : 1,
                'with_comment'         => (empty($withComment)) ? 0 : 1,
                'summary'              => $request->request->filter('summary', '', FILTER_SANITIZE_STRING),
                'img1'                 => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'img1_footer'          => $request->request->filter('img1_footer', '', FILTER_SANITIZE_STRING),
                'img2'                 => $request->request->filter('img2', '', FILTER_SANITIZE_STRING),
                'img2_footer'          => $request->request->filter('img2_footer', '', FILTER_SANITIZE_STRING),
                'type_opinion'         => $request->request->filter('type_opinion', '', FILTER_SANITIZE_STRING),
                'fk_author'            => $request->request->getDigits('fk_author'),
                'fk_user_last_editor'  => $request->request->getDigits('fk_user_last_editor'),
                'metadata'             => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'body'                 => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
                'fk_author_img'        => $request->request->getDigits('fk_author_img'),
                'fk_author_img_widget' => $request->request->getDigits('fk_author_img_widget'),
                'publisher'            => $_SESSION['userid'],
                'starttime'         => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
                'endtime'           => $request->request->filter('endtime', '', FILTER_SANITIZE_STRING),
            );

            if ($opinion->create($data)) {
                m::add(_('Opinion successfully created.'), m::SUCCESS);
            } else {
                m::add(_('Unable to create the new opinion.'), m::ERROR);
            }

            $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
            if (isset($continue) && $continue==1) {
                return $this->redirect(
                    $this->generateUrl('admin_opinion_show', array('id' => $opinion->id))
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('admin_opinions', array('type_opinion' => $data['category']))
                );
            }
        } else {
            $author   = new \Author();
            $authors = $author->all_authors(null, 'ORDER BY name');

            return $this->render(
                'opinion/new.tpl',
                array('all_authors' => $authors,)
            );
        }
    }

    /**
     * Updates the opinion information sent by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_UPDATE');

        $id = $request->query->getDigits('id');

        $opinion = new \Opinion($id);

        if ($opinion->id != null) {
            if (!\Acl::isAdmin()
                && !\Acl::check('CONTENT_OTHER_UPDATE')
                && $opinionCheck->fk_user != $_SESSION['userid']
            ) {
                m::add(_("You can't modify this opinion because you don't have enought privileges."));

                return $this->redirect($this->generateUrl('admin_opinions'));
            }

            $available   = $request->request->filter('available', '', FILTER_SANITIZE_STRING);
            $inhome      = $request->request->filter('in_home', '', FILTER_SANITIZE_STRING);
            $withComment = $request->request->filter('with_comment', '', FILTER_SANITIZE_STRING);

            // Check empty data
            if (count($request->request) < 1) {
                m::add(_("Opinion data sent not valid."), m::ERROR);

                return $this->redirect($this->generateUrl('admin_opinion_show', array('id' => $id)));
            }

            $data = array(
                'id'                   => $id,
                'title'                => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'category'             => 'opinion',
                'available'            => (empty($available)) ? 0 : 1,
                'in_home'              => (empty($inhome)) ? 0 : 1,
                'with_comment'         => (empty($withComment)) ? 0 : 1,
                'summary'              => $request->request->filter('summary', '', FILTER_SANITIZE_STRING),
                'img1'                 => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'img1_footer'          => $request->request->filter('img1_footer', '', FILTER_SANITIZE_STRING),
                'img2'                 => $request->request->filter('img2', '', FILTER_SANITIZE_STRING),
                'img2_footer'          => $request->request->filter('img2_footer', '', FILTER_SANITIZE_STRING),
                'type_opinion'         => $request->request->filter('type_opinion', '', FILTER_SANITIZE_STRING),
                'fk_author'            => $request->request->getDigits('fk_author'),
                'fk_user_last_editor'  => $request->request->getDigits('fk_user_last_editor'),
                'metadata'             => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'body'                 => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
                'fk_author_img'        => $request->request->getDigits('fk_author_img'),
                'fk_author_img_widget' => $request->request->getDigits('fk_author_img_widget'),
                'publisher'            => $_SESSION['userid'],
                'starttime'            => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
                'endtime'              => $request->request->filter('endtime', '', FILTER_SANITIZE_STRING),
            );

            if ($opinion->update($data)) {
                m::add(_('Opinion successfully updated.'), m::SUCCESS);
            } else {
                m::add(_('Unable to update the opinion.'), m::ERROR);
            }

            // TODO: Move this to a post update hook
            $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete('opinion|1');
            $tplManager->delete('opinion|'.$opinion->id);

            $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
            if (isset($continue) && $continue==1) {
                return $this->redirect(
                    $this->generateUrl('admin_opinion_show', array('id' => $opinion->id))
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('admin_opinions', array('type_opinion' => $data['category']))
                );
            }
        }

    }

    /**
     * Deletes an opinion given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_DELETE');

        $id     = $request->query->getDigits('id');
        $page   = $request->query->getDigits('status', 1);
        $author = $request->query->filter('author', 0, FILTER_VALIDATE_INT);
        $status = (int) $request->query->getDigits('status');

        if (!empty($id)) {
            $opinion = new \Opinion($id);

            $opinion->delete($id, $_SESSION['userid']);
            m::add(_("Opinion deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete an opinion.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_opinions',
                    array(
                        'page'     => $page,
                        'author'   => $author,
                        'status'   => $status,
                    )
                )
            );
        } else {
            return new Response('ok');
        }
    }

    /**
     * Change available status for one opinion given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_AVAILABLE');

        $id     = $request->query->getDigits('id', 0);
        $status = $request->query->getDigits('status', 0);
        $type   = $request->query->filter('type', 0, FILTER_SANITIZE_STRING);
        $page   = $request->query->getDigits('page', 1);

        $opinion = new \Opinion($id);

        if (is_null($opinion->id)) {
            m::add(sprintf(_('Unable to find an opinion with the id "%d"'), $id), m::ERROR);
        } else {
            if ($status == 0) {
                $opinion->setDraft();
                $opinion->set_inhome($status, $_SESSION['userid']);
            } else {
                $opinion->setAvailable();
            }
            m::add(
                sprintf(_('Successfully changed availability for the opinion "%s"'), $opinion->title),
                m::SUCCESS
            );
        }

        if ($type != 'frontpage') {
            $url = $this->generateUrl(
                'admin_opinions',
                array(
                    'type' => $type,
                    'page' => $page
                )
            );
        } else {
            $url = $this->generateUrl(
                'admin_opinions_frontpage',
                array(
                    'page' => $page
                )
            );
        }

         return $this->redirect($url);
    }

    /**
     * Change in_home status for one opinion given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleInHomeAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_AVAILABLE');

        $id     = $request->query->getDigits('id', 0);
        $status = $request->query->getDigits('status', 0);
        $type   = $request->query->filter('type', 0, FILTER_SANITIZE_STRING);
        $page   = $request->query->getDigits('page', 1);

        $opinion = new \Opinion($id);

        if (is_null($opinion->id)) {
            m::add(sprintf(_('Unable to find an opinion with the id "%d"'), $id), m::ERROR);
        } else {
            $opinion->set_inhome($status, $_SESSION['userid']);
            m::add(sprintf(_('Successfully changed in home state for the opinion "%s"'), $opinion->title), m::SUCCESS);
        }

        if ($type != 'frontpage') {
            $url = $this->generateUrl(
                'admin_opinions',
                array('type' => $type, 'page' => $page)
            );
        } else {
            $url = $this->generateUrl(
                'admin_opinions_frontpage',
                array('page' => $page)
            );
        }

         return $this->redirect($url);
    }

    /**
     * Change favorite flag for one opinion given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleFavoriteAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_AVAILABLE');

        $id     = $request->query->getDigits('id', 0);
        $status = $request->query->getDigits('status', 0);
        $type   = $request->query->filter('type', 0, FILTER_SANITIZE_STRING);
        $page   = $request->query->getDigits('page', 1);

        $opinion = new \Opinion($id);

        if (is_null($opinion->id)) {
            m::add(sprintf(_('Unable to find an opinion with the id "%d"'), $id), m::ERROR);
        } else {
            $opinion->set_favorite($status, $_SESSION['userid']);
            m::add(
                sprintf(
                    _('Successfully changed favorite state for the opinion "%s"'),
                    $opinion->title
                ),
                m::SUCCESS
            );
        }

        if ($type != 'frontpage') {
            $url = $this->generateUrl(
                'admin_opinions',
                array(
                    'type' => $type,
                    'page' => $page
                )
            );
        } else {
            $url = $this->generateUrl(
                'admin_opinions_frontpage',
                array(
                    'page' => $page
                )
            );
        }

         return $this->redirect($url);
    }

    /**
     * Saves the widget opinions content positions
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function savePositionsAction(Request $request)
    {
        $containers = json_decode($request->get('positions'));

        if (isset($containers)
            && is_array($containers)
            && count($containers) > 0
        ) {
            $positionValues = array();

            foreach ($containers as $elements) {
                $pos = 1;
                foreach ($elements as $id) {
                    $positionValues[] = array($pos, '1', $id);
                    $pos++;
                }
            }

            $opinion = new \Opinion();
            $msg = $opinion->set_position($positionValues, $_SESSION['userid']);

            // FIXME: buscar otra forma de hacerlo
            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
            $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete('home|0');
        }

        if (!empty($msg) && $msg == true) {
            $message = _('Positions saved successfully.');
            $output = sprintf(
                '<div class="alert alert-success">%s<button data-dismiss="alert" class="close">×</button></div>',
                $message
            );
        } else {
            $output = _('Unable to save positions for the opinions widget.');
            $output = sprintf(
                '<div class="alert alert-error">%s<button data-dismiss="alert" class="close">×</button></div>',
                $message
            );
        }

        return new Response($output);
    }

    /**
     * Deletes multiple opinions at once given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_DELETE');

        $selected       = $request->query->get('selected_fld', null);
        $redirectStatus = $request->query->filter('status', '-1', FILTER_SANITIZE_STRING);
        $author         = $request->query->getDigits('author');
        $page           = $request->query->getDigits('page', 1);

        $changes = 0;
        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $opinion = new \Opinion((int) $id);
                if (!is_null($opinion->id)) {
                    $opinion->delete($id, $_SESSION['userid']);
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find a opinion with the id "%d"'), $id));
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully deleted %d opinions'), $changes));
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_opinions',
                    array(
                        'author' => $author,
                        'status' => $redirectStatus,
                        'page' => $page,
                    )
                )
            );
        }

    }

    /**
     * Changes the available status for opinions given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchPublishAction(Request $request)
    {
        $this->checkAclOrForward('OPINIONS_AVAILABLE');

        $selected       = $request->query->get('selected_fld', null);
        $status         = $request->query->getDigits('new_status', 0);
        $redirectStatus = $request->query->filter('status', '-1', FILTER_SANITIZE_STRING);
        $author         = $request->query->getDigits('author');
        $page           = $request->query->getDigits('page', 1);

        $changes = 0;
        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $opinion = new \Opinion((int) $id);
                if (!is_null($opinion->id)) {
                    if ($status == 0) {
                        $opinion->setDraft();
                        $opinion->set_favorite($status);
                        $opinion->set_inhome($status);
                    } else {
                        $opinion->setAvailable();
                    }
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find a opinion with the id "%d"'), $id), m::ERROR);
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully changed the available status of %d opinions'), $changes), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_opinions',
                array(
                    'author' => $author,
                    'status' => $redirectStatus,
                    'page' => $page,
                )
            )
        );
    }

      /**
     * Changes home status for opinions given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchInHomeAction(Request $request)
    {
        $this->checkAclOrForward('OPINIONS_AVAILABLE');

        $selected       = $request->query->get('selected_fld', null);
        $status         = $request->query->getDigits('new_status', 0);
        $redirectStatus = $request->query->filter('status', '-1', FILTER_SANITIZE_STRING);
        $author         = $request->query->getDigits('author');
        $page           = $request->query->getDigits('page', 1);

        $changes = 0;
        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $opinion = new \Opinion((int) $id);
                if (!is_null($opinion->id)) {
                    if ($status == 0) {
                        $opinion->set_inhome($status);
                    } else {
                        $opinion->set_inhome($status);
                    }
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find a opinion with the id "%d"'), $id), m::ERROR);
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully changed the home status of %d opinions'), $changes), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_opinions',
                array(
                    'author' => $author,
                    'status' => $redirectStatus,
                    'page' => $page,
                )
            )
        );
    }
    /**
     * Lists the available opinions for the frontpage manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderAction(Request $request)
    {
        $category = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $page = $request->query->getDigits('page', 1);
        if ($category == 'home') {
            $category = 0;
        }
        $itemsPerPage = 8;

        $cm = new \ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

        // Fetching opinions
        $sqlExcludedOpinions = '';
        if (count($contentElementsInFrontpage) > 0) {
            $opinionsExcluded    = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedOpinions = ' AND `pk_opinion` NOT IN ('.$opinionsExcluded.')';
        }

        list($countOpinions, $opinions) = $cm->getCountAndSlice(
            'Opinion',
            null,
            'contents.available=1 AND in_litter != 1'. $sqlExcludedOpinions,
            'ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

        foreach ($opinions as &$opinion) {
            $opinion->author = new \Author($opinion->fk_author);
        }

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countOpinions,
                'fileName'    => $this->generateUrl(
                    'admin_opinions_content_provider',
                    array('category' => $category,)
                ).'&page=%d',
            )
        );

        return $this->render(
            'opinion/content-provider.tpl',
            array(
                'opinions' => $opinions,
                'pager'    => $pagination,
            )
        );
    }

    /**
     * Lists the latest opinions for the related manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderRelatedAction(Request $request)
    {
        $page     = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $cm = new  \ContentManager();

        list($countOpinions, $opinions) = $cm->getCountAndSlice(
            'Opinion',
            null,
            '',
            ' ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 1,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countOpinions,
                'fileName'    => $this->generateUrl('admin_opinions_content_provider_related').'?page=%d',
            )
        );

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Opinion',
                'contents'              => $opinions,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_opinions_content_provider_related'),
            )
        );
    }

    /**
     * Handles the configuration for the opinion manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_SETTINGS');

        if ('POST' == $request->getMethod()) {

            $configsRAW = $request->request->get('opinion_settings');

            $configs = array(
                'opinion_settings' => array(
                    'total_director'        => filter_var($configsRAW['total_director'], FILTER_VALIDATE_INT),
                    'total_editorial'       => filter_var($configsRAW['total_editorial'], FILTER_VALIDATE_INT),
                    'total_opinion_authors' => filter_var($configsRAW['total_opinion_authors'], FILTER_VALIDATE_INT),
                )
            );

            foreach ($configs as $key => $value) {
                s::set($key, $value);
            }

            m::add(_('Settings saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_opinions_config'));
        } else {
            $configurations = s::get(array('opinion_settings',));

            return $this->render(
                'opinion/config.tpl',
                array('configs'   => $configurations,)
            );
        }
    }
}
