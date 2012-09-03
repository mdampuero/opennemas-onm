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
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class KeywordsController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        \Onm\Module\ModuleManager::checkActivatedOrForward('KEYWORD_MANAGER');

        $this->checkAclOrForward('PCLAVE_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }
    /**
     * Description of the action
     *
     * @return void
     **/
    public function listAction(Request $request)
    {
        $filter = $this->request->query->get('filter', null);
        $page   = $this->request->query->getDigits('page', 1);

        if (isset($filter) && !empty($filter['pclave'])) {
            $filter = '`pclave` LIKE "%' . $filter['pclave'] . '%"';
        }

        $keywordManager = new \PClave();
        $keywords = $keywordManager->find($filter);

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => ITEMS_PAGE,
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => count($keywords),
            )
        );

        $keywords = array_slice($keywords, ($page-1) * ITEMS_PAGE, ITEMS_PAGE);

        return $this->render(
            'keywords/list.tpl',
            array(
                'keywords'   => $keywords,
                'pagination' => $pagination,
            )
        );
    }

    /**
     * Shows the keyword information given its id
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('PCLAVE_UPDATE');

        $id = $this->request->query->getDigits('id');

        $keyword = new \PClave();
        $keyword->read($id);

        return $this->render(
            'keywords/new.tpl',
            array(
                'keyword' => $keyword,
                'tipos'  => array(
                    'url'       => _('URL'),
                    'intsearch' => _('Internal search'),
                    'email'     => _('Email')
                ),
            )
        );
    }

    /**
     * Shows the form for creating a new keyword and handles its form.
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('PCLAVE_CREATE');

        if ('POST' == $this->request->getMethod()) {
            $this->checkAclOrForward('PCLAVE_CREATE');

            $data = array(
                'pclave' => $request->request->filter('pclave', '', FILTER_SANITIZE_STRING),
                'tipo'   => $request->request->filter('tipo', '', FILTER_SANITIZE_STRING),
                'value'  => $request->request->filter('value', '', FILTER_SANITIZE_STRING),
            );

            $keyword = new \PClave();
            $keyword->create($data);

            m::add(_('Keyword created sucessfully'), m::SUCCESS);

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
                    'tipos' => array(
                        'url'       => _('URL'),
                        'intsearch' => _('Internal search'),
                        'email'     => _('Email')
                    )
                )
            );
        }
    }

    /**
     * Updates the Pclave information given its new data
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('PCLAVE_UPDATE');

        $data = array(
            'id'     => $request->query->getDigits('id'),
            'pclave' => $request->request->filter('pclave', '', FILTER_SANITIZE_STRING),
            'tipo'   => $request->request->filter('tipo', '', FILTER_SANITIZE_STRING),
            'value'  => $request->request->filter('value', '', FILTER_SANITIZE_STRING),
        );

        $keyword = new \PClave();
        $keyword->update($data);

        m::add(_('Keyword updated sucessfully'), m::SUCCESS);

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
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        \Acl::checkOrForward('PCLAVE_DELETE');

        $id = $this->request->query->getDigits('id');

        $keyword = new \PClave();
        $keyword->delete($id);

        return $this->redirect($this->generateUrl('admin_keywords'));
    }

    /**
     *
     *
     * @return Reponse the response object
     **/
    public function autolinkAction(Request $request)
    {
        $content = json_decode($HTTP_RAW_POST_DATA)->content;
        if (!empty($content)) {
            $terms = $pclave->getList();

            return $pclave->replaceTerms($content, $terms);
        }
    }
}

