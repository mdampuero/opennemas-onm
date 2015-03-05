<?php
/**
 * Handles the actions for the keywords
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
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the keywords
 *
 * @package Backend_Controllers
 **/
class KeywordsController extends Controller
{
    /**
     * Lists all the keywords
     *
     * @param Request $request the request object
     *
     * @return Response
     *
     * @Security("has_role('PCLAVE_ADMIN')")
     *
     * @CheckModuleAccess(module="KEYWORD_MANAGER")
     **/
    public function listAction(Request $request)
    {
        $name = $request->query->filter('name', null, FILTER_SANITIZE_STRING);
        $page = $request->query->getDigits('page', 1);

        $filter = '';
        if (!empty($name)) {
            $filter = '`pclave` LIKE "%' . $name . '%"';
        }

        $keywordManager = new \PClave();
        $keywords = $keywordManager->find($filter);
        $itemsPerPage = s::get('items_per_page');

        $pagination = $this->get('paginator')->create([
            'elements_per_page' => $itemsPerPage,
            'total_items'       => count($keywords),
            'base_url'          => $this->generateUrl('admin_keywords'),
        ]);

        $keywords = array_slice($keywords, ($page-1)*$itemsPerPage, $itemsPerPage);

        return $this->render(
            'keywords/list.tpl',
            array(
                'keywords'   => $keywords,
                'pagination' => $pagination,
                'name'       => $name,
                'types'      => \PClave::getTypes(),
            )
        );
    }

    /**
     * Shows the keyword information given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PCLAVE_UPDATE')")
     *
     * @CheckModuleAccess(module="KEYWORD_MANAGER")
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $keyword = new \PClave();
        $keyword->read($id);

        return $this->render(
            'keywords/new.tpl',
            array(
                'keyword' => $keyword,
                'tipos'   => \PClave::getTypes(),
            )
        );
    }

    /**
     * Shows the form for creating a new keyword and handles its form.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PCLAVE_CREATE')")
     *
     * @CheckModuleAccess(module="KEYWORD_MANAGER")
     **/
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $data = array(
                'pclave' => $request->request->filter('pclave', '', FILTER_SANITIZE_STRING),
                'tipo'   => $request->request->filter('tipo', '', FILTER_SANITIZE_STRING),
                'value'  => $request->request->filter('value', '', FILTER_SANITIZE_STRING),
            );

            $keyword = new \PClave();
            $keyword->create($data);

            $this->get('session')->getFlashBag()->add('success', _('Keyword created sucessfully'));

            return $this->redirect(
                $this->generateUrl(
                    'admin_keyword_show',
                    array('id' => $keyword->id)
                )
            );
        } else {
            return $this->render(
                'keywords/new.tpl',
                array(
                    'tipos' => \PClave::getTypes(),
                )
            );
        }
    }

    /**
     * Updates the Pclave information given its new data
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PCLAVE_UPDATE')")
     *
     * @CheckModuleAccess(module="KEYWORD_MANAGER")
     **/
    public function updateAction(Request $request)
    {
        $data = array(
            'id'     => $request->query->getDigits('id'),
            'pclave' => $request->request->filter('pclave', '', FILTER_SANITIZE_STRING),
            'tipo'   => $request->request->filter('tipo', '', FILTER_SANITIZE_STRING),
            'value'  => $request->request->filter('value', '', FILTER_SANITIZE_STRING),
        );

        $keyword = new \PClave();
        $keyword->update($data);

        $this->get('session')->getFlashBag()->add('success', _('Keyword updated sucessfully'));

        return $this->redirect(
            $this->generateUrl(
                'admin_keyword_show',
                array('id' => $data['id'])
            )
        );
    }

    /**
     * Deletes a keyword given its id.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('PCLAVE_DELETE')")
     *
     * @CheckModuleAccess(module="KEYWORD_MANAGER")
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $keyword = new \PClave();
        $keyword->delete($id);

        return $this->redirect($this->generateUrl('admin_keywords'));
    }

    /**
     * Given a text, this action replaces all the registered keywords with the
     * valid keyword link
     *
     * @param Request $request the request object
     *
     * @return Reponse the response object
     *
     * @CheckModuleAccess(module="KEYWORD_MANAGER")
     **/
    public function autolinkAction(Request $request)
    {
        $content = $request->request->filter('text', null, FILTER_SANITIZE_STRING);

        $newContent = '';
        if (!empty($content)) {
            $keyword = new \PClave();
            $terms = $keyword->find();

            $newContent = $keyword->replaceTerms($content, $terms);
        }

        return new Response($newContent);
    }
}
