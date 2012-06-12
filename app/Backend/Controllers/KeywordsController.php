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

use Onm\Framework\Controller\Controller,
    Onm\Message as m;
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
     * @author
     **/
    public function init()
    {
        \Onm\Module\ModuleManager::checkActivatedOrForward('KEYWORD_MANAGER');

        \Acl::checkOrForward('PCLAVE_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }
    /**
     * Description of the action
     *
     * @return void
     **/
    public function defaultAction()
    {
        $filter = $this->request->query->get('filter', null);
        $page = $this->request->query->getDigits('page', 1);

        if (isset($filter) && !empty($_REQUEST['filter']['pclave'])) {
            $filter = '`pclave` LIKE "%' . $_REQUEST['filter']['pclave'] . '%"';
        }

        $keywordManager = new \PClave();
        $terms = $keywordManager->getList($filter);


        $pager = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => ITEMS_PAGE,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($terms),
        ));

        $terms = array_slice($terms, ($page-1) * ITEMS_PAGE, ITEMS_PAGE);

        return $this->render('keywords/list.tpl', array(
            'pclaves' => $terms,
            'pager'   => $pager,
        ));
    }

    /**
     * Performs the searches over all the keywords
     *
     * @return void
     **/
    public function searchAction()
    {
        $id      = $this->request->query->getDigits('id', null);
        $terms   = $pclave->getList();

        $matches = array();
        $terms = array_filter(
            $terms,
            function($item) use ($id){
                if (($tiem->id != $id) &&
                    preg_match('/^' . preg_quote($_REQUEST['q']) . '/', $item->pclave)
                ) {
                    return true;
                }
            }
        );

        return $this->render('keywords/search.tpl', array(
            'terms' => $matches,
        ));
    }

    /**
     * Shows the keyword information given its id
     *
     * @return Response the response object
     **/
    public function readAction()
    {
        \Acl::checkOrForward('PCLAVE_UPDATE');

        $id = $this->request->query->getDigits('id');
        $pclave->read($id);

        return $this->render('keywords/new.tpl', array(
            'id'     => $id,
            'pclave' => $pclave,
            'tipos'  => array(
            )
        ));
    }

    /**
     * Shows the form for creating a new keyword and handles its form.
     *
     * @return Response the response object
     **/
    public function createAction()
    {
        \Acl::checkOrForward('PCLAVE_CREATE');

        if ('POST' == $this->request->getMethod()) {
            \Acl::checkOrForward('PCLAVE_CREATE');

            $keywordManager = new \PClave();
            $keywordManager->save($_POST);

            m::add(_('Keyword created sucessfully'), m::SUCCESS);

            $this->redirect('admin_keywords');
        } else {
            return $this->render(url('keywords/new.tpl'), array(
                'tipos' => array(
                    'url'       => _('URL'),
                    'intsearch' => _('Internal search'),
                    'email'     => _('Email')
                )
            ));
        }
    }

    /**
     * Deletes a keyword given its id.
     *
     * @return Response the response object
     **/
    public function deleteAction()
    {
        Acl::checkOrForward('PCLAVE_DELETE');

        $id = $this->request->query->getDigits('id');
        $pclave->delete($id);

        return $this->redirect(url('admin_keywords'));
    }

    /**
     *
     *
     * @return Reponse the response object
     **/
    public function autolinkAction()
    {
        $content = json_decode($HTTP_RAW_POST_DATA)->content;
        if (!empty($content)) {
            $terms = $pclave->getList();

            return $pclave->replaceTerms($content, $terms);
        }
    }

} // END class KeywordsController
