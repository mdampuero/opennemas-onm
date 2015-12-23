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
use Backend\Annotation\CheckModuleAccess;
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
class DatabaseErrorsController extends Controller
{

    // TODO: refactorize this method to make it simpler
    /**
     * Gets all the settings and displays the form
     *
     * @param Request $request the request object
     *
     * @return string the response
     *
     * @Security("has_role('ROLE_MASTER')")
     *
     * @CheckModuleAccess(module="LOG_SQL")
     **/
    public function defaultAction(Request $request)
    {
        $where        = "";
        $itemsPerPage = 10;
        // $totalErrors  = (int) $rsTotalErrors;
        $totalErrors = 0;
        $page         = $request->query->getDigits('page', 1);
        $search       = $request->query->filter('search', '', FILTER_SANITIZE_STRING);

        $sql = "SELECT count(*) FROM adodb_logsql";
        $rsTotalErrors = $GLOBALS['application']->conn->getOne($sql);

        $values = array();
        if (!empty($search)) {
            $where = "WHERE `sql1` LIKE '%{$search}%' OR `tracer` LIKE '%{$search}%'";
        }

        $sql = "SELECT * FROM adodb_logsql ".$where
               ." ORDER BY created DESC"
               .' LIMIT '.($page-1)*$itemsPerPage.', '.($itemsPerPage);

        $errors = $GLOBALS['application']->conn->GetArray($sql, $values);
        if ($errors === false) {
            $errors = [];
        }

        $pagination = $this->get('paginator')->get([
            'epp'   => $itemsPerPage,
            'page'  => $page,
            'total' => $totalErrors,
            'route' => 'admin_databaseerrors',
        ]);

        return $this->render(
            'system_information/sql_error_log.tpl',
            array(
                'errors'        => $errors,
                'pagination'    => $pagination,
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
     *
     * @CheckModuleAccess(module="LOG_SQL")
     **/
    public function purgeAction()
    {
        $sql = "TRUNCATE TABLE `adodb_logsql`";
        $GLOBALS['application']->conn->Execute($sql);

        $this->get('session')->getFlashBag()->add(
            'success',
            _('SQL errors registered in database cleaned sucessfully.')
        );

        return $this->redirect($this->generateUrl('admin_databaseerrors', array(), true));
    }
}
