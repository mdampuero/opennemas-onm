<?php
/**
 * Handles the actions for the letters content
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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the letters content
 *
 * @package Backend_Controllers
 **/
class LettersController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        // Check MODULE
        \Onm\Module\ModuleManager::checkActivatedOrForward('LETTER_MANAGER');

        // Check ACL
        $this->checkAclOrForward('LETTER_ADMIN');
    }

    /**
     * Lists all the letters
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $cm = new \ContentManager();

        $page         = $request->query->getDigits('page', 1);
        $letterStatus = $request->query->getDigits('letterStatus', 0);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        list($countLetters, $letters) =$cm->getCountAndSlice(
            'Letter',
            null,
            "contents.in_litter !=1 AND content_status = ".$letterStatus,
            'ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );
        foreach ($letters as &$letter) {
            $letter->image = $letter->getProperty('image');
        }

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
                'totalItems'  => $countLetters,
                'fileName'    => $this->generateUrl('admin_letters').'?page=%d',
            )
        );

        return $this->render(
            'letter/list.tpl',
            array(
                'pagination'   => $pagination,
                'letters'      => $letters,
                'letterStatus' => $letterStatus,
                'page'         => $page,
            )
        );
    }

    /**
     * Handles the form for create new letters
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('LETTER_CREATE');

        if ('POST' == $request->getMethod()) {
            $page     = $request->request->getDigits('page', 1);
            $category = $request->request->getDigits('category');

            $letter = new \Letter();

            $data = array(
                'title'     => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'metadata'     => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'available' => $request->request->filter('available', '', FILTER_SANITIZE_STRING),
                'author'    => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
                'email'     => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
                'params'    => $request->request->get('params'),
                'image'     => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'url'       => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
                'body'      => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
            );

            if ($letter->create($data)) {
                m::add(_('Letter successfully created.'), m::SUCCESS);
            } else {
                m::add(_('Unable to create the new letter.'), m::ERROR);
            }
            return $this->redirect(
                $this->generateUrl(
                    'admin_letter_show',
                    array('id' => $letter->id)
                )
            );
        } else {
            return $this->render('letter/new.tpl');
        }
    }

    /**
     * Shows the letter information form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('LETTER_UPDATE');

        $id = $request->query->getDigits('id', null);

        $letter = new \Letter($id);

        if (!empty($letter->image)) {
            $photo1 = new \Photo($letter->image);
            $this->view->assign('photo1', $photo1);
        }

        if (is_null($letter->id)) {
            m::add(sprintf(_('Unable to find the letter with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_letters'));
        }

        return $this->render(
            'letter/new.tpl',
            array('letter' => $letter,)
        );
    }

    /**
     * Updates the letter information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('LETTER_UPDATE');

        $id = $request->query->getDigits('id');
        $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
        $letter = new \Letter($id);

        if ($letter->id != null) {
            // Check empty data
            if (count($request->request) < 1) {
                m::add(_("Letter data sent not valid."), m::ERROR);

                return $this->redirect($this->generateUrl('admin_letter_show', array('id' => $id)));
            }

            $data = array(
                'id'        => $id,
                'title'     => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'metadata'     => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'available' => $request->request->filter('available', '', FILTER_SANITIZE_STRING),
                'author'    => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
                'email'     => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
                'params'    => $request->request->get('params'),
                'image'     => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'url'       => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
                'body'      => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
            );

            if ($letter->update($data)) {
                m::add(_('Letter successfully updated.'), m::SUCCESS);
            } else {
                m::add(_('Unable to update the letter.'), m::ERROR);
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_letter_show',
                    array('id' => $letter->id)
                )
            );
        }

        return $this->redirect($this->generateUrl('admin_letters'));
    }

    /**
     * Delete a letter given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('LETTER_DELETE');

        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $letter = new \Letter($id);

            $letter->delete($id, $_SESSION['userid']);
            m::add(_("Letter deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete the letter.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_letters',
                    array(
                        'page' => $page
                    )
                )
            );
        } else {
            return new Response('ok', 200);
        }
    }

    /**
     * Change available status for one video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('LETTER_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);
        $letterStatus     = $request->query->getDigits('letterStatus', 1);

        $letter = new \Letter($id);

        if (is_null($letter->id)) {
            m::add(sprintf(_('Unable to find a letter with the id "%d"'), $id), m::ERROR);
        } else {
            $letter->set_available($status, $_SESSION['userid']);
            m::add(sprintf(_('Successfully changed availability for the letter "%s"'), $letter->title), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_letters',
                array(
                    'letterStatus' => $letterStatus,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Deletes multiple letters at once given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('LETTER_DELETE');

        $selected = $request->query->get('selected_fld', null);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            $changes = 0;
            foreach ($selected as $id) {
                $letter = new \Letter((int) $id);
                if (!is_null($letter->id)) {
                    $letter->delete($id, $_SESSION['userid']);
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find a letter with the id "%d"'), $id), m::ERROR);
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully deleted %d letters'), $changes), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_letters',
                array('letterStatus' => $status,)
            )
        );

    }

    /**
     * Changes the available status for letters given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchPublishAction(Request $request)
    {
        $this->checkAclOrForward('LETTER_AVAILABLE');

        $status   = $request->query->getDigits('status', 0);
        $selected = $request->query->get('selected_fld', null);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            $changes = 0;
            foreach ($selected as $id) {
                $letter = new \Letter((int) $id);
                if (!is_null($letter->id)) {
                    $letter->set_available($status, $_SESSION['userid']);
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find a letter with the id "%d"'), $id), m::ERROR);
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully changed the available status of %d letters'), $changes), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_letters',
                array('letterStatus' => $status,)
            )
        );
    }

     /**
     * Lists the available Letters for the frontpage manager
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

        // Fetching Letters
        $sqlExcludedLetters = '';
        if (count($contentElementsInFrontpage) > 0) {
            $lettersExcluded    = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedLetters = ' AND `pk_letter` NOT IN ('.$lettersExcluded.')';
        }

        list($countLetters, $letters) = $cm->getCountAndSlice(
            'Letter',
            null,
            'contents.available=1 AND in_litter != 1'. $sqlExcludedLetters,
            'ORDER BY created DESC ',
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
                'totalItems'  => $countLetters,
                'fileName'    => $this->generateUrl(
                    'admin_letters_content_provider'
                ).'&page=%d',
            )
        );


        return $this->render(
            'letter/content-provider.tpl',
            array(
                'letters'  => $letters,
                'pager'    => $pagination,
            )
        );
    }
    /**
     * Implementes the content list provider for letters
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentListProviderAction(Request $request)
    {
        $itemsPerPage = s::get('items_per_page') ?: 20;
        $page = $request->query->getDigits('page', 1);
        $cm = new \ContentManager();

        list($countLetters, $letters)= $cm->getCountAndSlice(
            'Letter',
            "content_status=1 AND contents.in_litter !=1",
            "ORDER BY starttime DESC",
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
                'totalItems'  => $countLetters,
                'fileName'    => $this->generateUrl('admin_letters_content_list_provider').'?page=%d',
            )
        );

        return $this->render(
            "common/content_provider/_container-content-list.tpl",
            array(
                'contents'   => $letters,
                'pagination' => $pagination->links
            )
        );
    }
}
