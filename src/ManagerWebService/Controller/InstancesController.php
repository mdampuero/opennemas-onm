<?php
/**
 * Handles the actions for the system information
 *
 * @package Manager_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Module\ModuleManager as ModuleManager;
use Onm\Framework\Controller\Controller;
use Onm\Instance\InstanceManager as im;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Manager_Controllers
 **/
class InstancesController extends Controller
{
    /**
     * Returns a list of instances in JSON format.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     */
    public function listAction(Request $request)
    {
        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $name  = $request->request->filter('search[filter_name]', '', FILTER_SANITIZE_STRING);
        $email = $request->request->filter('search[filter_email]', '', FILTER_SANITIZE_STRING);

        $name  = (is_array($name) && array_key_exists(0, $name)) ? $name[0]['value'] : '';
        $email = (is_array($email) && array_key_exists(0, $email)) ? $email[0]['value'] : '';

        $findParams = array(
            'elements_per_page' => $elementsPerPage,
            'page'              => $page,
            'name'              => $name,
            'email'             => $email,
        );

        $instanceManager = getService('instance_manager');
        $instances = $instanceManager->findAll($findParams);

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        foreach ($instances as &$instance) {
            list($instance->totals, $instance->configs) = $instanceManager->getDBInformation($instance->settings);

            // Get real time for last login
            if (isset($instance->configs['last_login'])) {
                $instance->configs['last_login'] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $instance->configs['last_login'],
                    new \DateTimeZone('UTC')
                );
                $instance->configs['last_login_timezone'] =
                    $timezones[$instance->configs['time_zone']];
            }

            unset($instance->settings);

            $instance->domains = preg_split("@,@", $instance->domains);
            $instance->show_url = $this->generateUrl('manager_instance_show', array('id' => $instance->id));
        }

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'extra'             => array(),
                'page'              => $page,
                'results'           => $instances,
                'total'             => count($instances),
            )
        );
    }

    /**
     * Returns a CSV file with all the instances information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function listExportAction(Request $request)
    {
        $findParams = array(
            'name' => $request->query->filter('filter_name', '', FILTER_SANITIZE_STRING),
            'email' => $request->query->filter('filter_email', '', FILTER_SANITIZE_STRING),
        );

        $instanceManager = $this->get('instance_manager');

        $instances = $instanceManager->findAll($findParams);

        foreach ($instances as &$instance) {
            list($instance->totals, $instance->configs) = $instanceManager->getDBInformation($instance->settings);

            // Get real time for last login
            if (isset($instance->configs['last_login'])) {
                $instance->configs['last_login'] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $instance->configs['last_login'],
                    new \DateTimeZone('UTC')
                );
            }

            $instance->domains = preg_split("@, @", $instance->domains);
        }

        $this->view = new \TemplateManager(TEMPLATE_MANAGER);

        $response = $this->render(
            'instances/csv.tpl',
            array(
                'instances'   => $instances,
                'filter_name' => $findParams['name'],
            )
        );

        if ($findParams['name'] != '*') {
            $fileNameFilter = '-'.\Onm\StringUtils::get_title($findParams['name']);
        } else {
            $fileNameFilter = '-complete';
        }
        $fileName = 'opennemas-instances'.$fileNameFilter.'-'.date("Y_m_d_His").'.csv';

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Deletes an instance given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function deleteAction(Request $request, $id)
    {
        $success = array();
        $errors = array();

        if (!empty($id)) {
            $instanceManager = getService('instance_manager');

            $delete = $instanceManager->delete($id);
            if (!$delete) {
                $errors[] = array(
                    'id'      => $id,
                    'message' => sprintf(_("Unable to delete the instance.")),
                    'type'    => 'error'
                );
            } else {
                $success[] = array(
                    'id'      => $id,
                    'message' => _("Instance deleted successfully."),
                    'type'    => 'success'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => _('You must provide an id for delete an instance.'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $errors)
            )
        );
    }

    /**
     * Batch Delete instances given its ids.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function batchDeleteAction(Request $request)
    {
        $updated = array();
        $success = array();
        $errors = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            $im = $this->get('instance_manager');
            foreach ($ids as $id) {
                $delete = $im->delete($id);

                if ($delete) {
                    $updated[] = $id;
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_("Unable to delete instance %d."), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if (count($updated) > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(_("%d instances deleted successfully."), count($updated)),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $errors)
            )
        );
    }

    /**
     * Toggle the availability of an instance given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function toggleAvailableAction(Request $request, $id)
    {
        $activated = $request->request->getDigits('value');

        $im = getService('instance_manager', 1);
        $instance = $im->read($id);

        $messages = array();

        if ($instance !== false) {
            $im->changeActivated($instance->id, $activated);
            $messages[] = array(
                'id'      => $id,
                'message' => _('Instance activated successfully.'),
                'type'    => 'success'
            );
        } else {
            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'activated' => $activated,
                'messages'  => $messages
            )
        );
    }

    /**
     * Set the activated flag for instances in batch.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function batchAvailableAction(Request $request)
    {
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $activated = $request->request->get('value');
        $ids       = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            $im = $this->get('instance_manager');
            foreach ($ids as $id) {
                $instance = $im->read($id);

                if ($instance !== false) {
                    $im->changeActivated($instance->id, $activated);
                    $updated[] = $id;
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(_('%d item(s) updated successfully'), count($updated)),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors)
            )
        );
    }
}
