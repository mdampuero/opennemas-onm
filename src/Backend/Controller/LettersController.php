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
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * Common code for all the actions.
     */
    public function init()
    {
        // Check MODULE
        \Onm\Module\ModuleManager::checkActivatedOrForward('LETTER_MANAGER');
    }

    /**
     * Lists all the letters.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('LETTER_ADMIN')")
     */
    public function listAction(Request $request)
    {
        return $this->render('letter/list.tpl');
    }

    /**
     * Handles the form for create new letters.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('LETTER_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $letter = new \Letter();

            $data = array(
                'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
                'author'         => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
                'email'          => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
                'params'         => $request->request->get('params'),
                'image'          => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'url'            => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
                'body'           => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
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
     * Shows the letter information form.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('LETTER_UPDATE')")
     */
    public function showAction(Request $request)
    {
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
     * Updates the letter information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('LETTER_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        // Check empty data
        if (count($request->request) < 1) {
            m::add(_("Letter data sent not valid."), m::ERROR);

            return $this->redirect($this->generateUrl('admin_letter_show', array('id' => $id)));
        }

        $id = $request->query->getDigits('id');
        $letter = new \Letter($id);
        if ($letter->id == null) {
            return $this->redirect($this->generateUrl('admin_letters'));
        }

        $data = array(
            'id'             => $id,
            'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
            'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
            'content_status' => $request->request->filter('content_status', '', FILTER_SANITIZE_STRING),
            'author'         => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
            'email'          => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
            'params'         => $request->request->get('params'),
            'image'          => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
            'url'            => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'body'           => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
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

    /**
     * Lists the available Letters for the frontpage manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $em  = $this->get('entity_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory();

        $filters = array(
            'content_type_name' => array(array('value' => 'letter')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'pk_content'        => array(array('value' => $ids, 'operator' => 'NOT IN'))
        );

        $letters      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countLetters = $em->countBy($filters);

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
     * Lists all the letters within a category for the related manager.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'letter')),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $letters      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countLetters = $em->countBy($filters);

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
                'fileName'    => $this->generateUrl('admin_letters_content_provider_related').'?page=%d',
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
