<?php
use Onm\Message as m;

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

 
if(!Acl::isMaster()) {
    m::add("You don't have permissions");
    Application::forward('/admin/');
}

$cm = new ContentManager();

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$action = filter_input ( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array( 'options' => array('default' => 'list')) );
$page = filter_input ( INPUT_GET, 'page' , FILTER_SANITIZE_STRING, array( 'options' => array('default' => 1)) );
$search = filter_input ( INPUT_GET, 'search' , FILTER_SANITIZE_STRING, array( 'options' => array('default' => "")) );


switch($action) {

    case 'list':
        $cm = new ContentManager();

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
        $pager = Pager::factory($pagerOptions);

        $tpl->assign( array(
            'errors' => $errors,
            'pagination' => $pager,
            'total_errors' => $rsTotalErrors,
            'sql' => $sql,
            'elements_page' => ($itemsPerPage*($page-1)),
            'search' => $search,
        ));

        $tpl->display('system_information/sql_error_log.tpl');

        break;

    case 'purge':

        $sql = "TRUNCATE TABLE `adodb_logsql`";

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
        }

        m::add(_('SQL errors registered in database cleaned sucessfully.'). m::SUCCESS);

        Application::forward($_SERVER['PHP_SELF']);

        break;

}
