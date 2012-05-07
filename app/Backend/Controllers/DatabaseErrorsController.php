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
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 * @author
 **/
class DatabaseErrorsController extends Controller
{

    /**
     * Common actions for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        //Setup app
        require_once './session_bootstrap.php';

        if(!\Acl::isMaster()) {
            m::add("You don't have permissions");
            Application::forward('/admin/');
        }

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    // TODO: refactorize this method to make it simpler
    /**
     * Gets all the settings and displays the form
     *
     * @return string the response
     **/
    public function defaultAction()
    {
        $cm = new \ContentManager();

        $page = filter_input(
            INPUT_GET, 'page' , FILTER_SANITIZE_STRING,
            array( 'options' => array('default' => 1))
        );
        $search = filter_input(
            INPUT_GET, 'search' , FILTER_SANITIZE_STRING,
            array( 'options' => array('default' => ""))
        );


        $filters = (isset($_REQUEST['filter']))? $_REQUEST['filter']: null;

        $sql = "SELECT count(*) FROM adodb_logsql";
        $rsTotalErrors = $GLOBALS['application']->conn->getOne($sql);
        if (is_null($rsTotalErrors) ) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
        }


        $where = "";
        $values = array();
        if (!empty($search)) {
            $where = "WHERE `sql1` LIKE '%{$search}%' OR `tracer` LIKE '%{$search}%'";
        }

        $itemsPerPage = 10;
        $totalErrors = (int) $rsTotalErrors;

        $limit = ' LIMIT '.($page-1)*$itemsPerPage.', '.($itemsPerPage);
        $orderBy = " ORDER BY created DESC";

        $sql = "SELECT * FROM adodb_logsql ".$where.$orderBy.$limit;


        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
        }

        $errors = $rs;

        $pagerOptions = array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $totalErrors,
        );
        $pager = \Pager::factory($pagerOptions);

        return $this->render('system_information/sql_error_log.tpl',  array(
            'errors' => $errors,
            'pagination' => $pager,
            'total_errors' => $rsTotalErrors,
            'sql' => $sql,
            'elements_page' => ($itemsPerPage*($page-1)),
            'search' => $search,
        ));
    }

    /**
     * Performs the action of saving the configuration settings
     *
     * @return string the response
     **/
    public function purgeAction()
    {
        $sql = "TRUNCATE TABLE `adodb_logsql`";

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            \Application::logDatabaseError();
        }

        m::add(_('SQL errors registered in database cleaned sucessfully.'). m::SUCCESS);

        return $this->redirect(url('admin_databaseerrors', array(), true));
    }

} // END class SystemSettigns