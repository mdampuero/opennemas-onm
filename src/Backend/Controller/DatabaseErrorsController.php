<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
class DatabaseErrorsController extends Controller
{

    /**
     * Common actions for all the actions
     *
     * @return void
     **/
    public function init()
    {
    }

    // TODO: refactorize this method to make it simpler
    /**
     * Gets all the settings and displays the form
     *
     * @param Request $request the request object
     *
     * @return string the response
     *
     * @Security("has_role('ROLE_MASTER')")
     **/
    public function defaultAction(Request $request)
    {
        if (!Acl::isMaster()) {
            m::add("You don't have permissions");

            return $this->redirect($this->generateUrl('admin_welcome'));
        }

        $page = $request->query->getDigits('page', 1);
        $search = $request->query->filter('search', '', FILTER_SANITIZE_STRING);

        $sql = "SELECT count(*) FROM adodb_logsql";
        $rsTotalErrors = $GLOBALS['application']->conn->getOne($sql);

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
        $errors = $GLOBALS['application']->conn->Execute($sql, $values);

        $pagerOptions = array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $totalErrors,
        );
        $pager = \Pager::factory($pagerOptions);

        return $this->render(
            'system_information/sql_error_log.tpl',
            array(
                'errors'        => $errors,
                'pagination'    => $pager,
                'total_errors'  => $rsTotalErrors,
                'sql'           => $sql,
                'elements_page' => ($itemsPerPage*($page-1)),
                'search'        => $search,
            )
        );
    }

    /**
     * Performs the action of saving the configuration settings
     *
     * @return string the response
     *
     * @Security("has_role('ROLE_MASTER')")
     **/
    public function purgeAction()
    {
        if (!Acl::isMaster()) {
            m::add("You don't have permissions");

            return $this->redirect($this->generateUrl('admin_welcome'));
        }

        $sql = "TRUNCATE TABLE `adodb_logsql`";
        $GLOBALS['application']->conn->Execute($sql);

        m::add(_('SQL errors registered in database cleaned sucessfully.'). m::SUCCESS);

        return $this->redirect($this->generateUrl('admin_databaseerrors', array(), true));
    }
}
