<?php

namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

class InstanceController extends Controller
{
    /**
     * Deletes the selected instances.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     */
    public function batchDeleteAction(Request $request)
    {
        $error   = array();
        $success = array();
        $updated = array();

        $selected  = $request->request->get('ids', null);
        $activated = $request->request->getDigits('value', 0);

        if (is_array($selected) && count($selected) > 0) {
            $im = $this->get('instance_manager');

            foreach ($selected as $id) {
                $instance = $im->read($id);
                if ($instance !== false) {
                    try {
                        $delete = $im->delete($id);
                        if (!$delete) {
                            throw new \Exception();
                        }

                        $updated[] = $id;
                    } catch (Exception $e) {
                        $messages[] = array(
                            'id'      => $id,
                            'message' => sprintf(_('Error while deleting instance with id "%s"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $error[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if (count($updated) > 0) {
            $success[] = array(
                'id'        => $updated,
                'activated' => $activated,
                'message'   => sprintf(_('%s instances deleted successfully.'), count($updated)),
                'type'      => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $error)
            )
        );
    }

    /**
     * Set the activated flag for instances in batch.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function batchSetActivatedAction(Request $request)
    {
        $error   = array();
        $success = array();
        $updated = 0;

        $selected  = $request->request->get('ids', null);
        $activated = $request->request->getDigits('value', 0);

        if (is_array($selected) && count($selected) > 0) {
            $im = $this->get('instance_manager');

            foreach ($selected as $id) {
                $instance = $im->read($id);
                if ($instance !== false) {
                    try {
                        $im->changeActivated($id, $activated);
                        $updated++;
                    } catch (Exception $e) {
                        $error[] = array(
                            'id'      => $id,
                            'message' => sprintf(_('Error while updating instance with id "%s"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $error[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'        => $updated,
                'activated' => $activated,
                'message'   => sprintf(_('%s instances updated successfully.'), $updated),
                'type'      => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $error)
            )
        );
    }

    /**
     * Deletes an instance.
     *
     * @param  integer      $id The instance id.
     * @return JsonResponse     The response object.
     */
    public function deleteAction($id)
    {
        $im        = $this->get('instance_manager');
        $messages  = array();

        $instance = $im->read($id);
        if ($instance !== false) {
            try {
                $delete = $im->delete($id);
                if (!$delete) {
                    throw new \Exception();
                }

                $messages[] = array(
                    'id'        => $id,
                    'activated' => $activated,
                    'message'   => _('Instance deleted successfully.'),
                    'type'      => 'success'
                );
            } catch (Exception $e) {
                $messages[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Error while deleting instance with id "%s"'), $id),
                    'type'    => 'error'
                );
            }
        } else {
            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => $messages
            )
        );
    }

    /**
     * Returns a CSV file with all the instances information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function exportAction(Request $request)
    {
        $name  = $request->query->filter('name', '', FILTER_SANITIZE_STRING);
        $email = $request->query->filter('email', '', FILTER_SANITIZE_STRING);

        $findParams = array(
            'name'  => $name,
            'email' => $email,
        );

        $im = getService('instance_manager');

        $instances = $im->findAll($findParams);

        foreach ($instances as &$instance) {
            list($instance->totals, $instance->configs) = $im->getDBInformation($instance->settings);

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
     * Returns the list of instances as JSON.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     */
    public function listAction(Request $request)
    {
        $epp   = $request->request->getDigits('elements_per_page', 10);
        $page  = $request->request->getDigits('page', 1);
        $name  = $request->request->filter('search[filter_name]', '', FILTER_SANITIZE_STRING);
        $email = $request->request->filter('search[filter_email]', '', FILTER_SANITIZE_STRING);

        $name  = (is_array($name) && array_key_exists(0, $name)) ? $name[0]['value'] : '';
        $email = (is_array($email) && array_key_exists(0, $email)) ? $email[0]['value'] : '';

        $findParams = array(
            'elements_per_page' => $epp,
            'page'              => $page,
            'name'              => $name,
            'email'             => $email,
        );

        $im = getService('instance_manager');
        $instances = $im->findAll($findParams);

        foreach ($instances as &$instance) {
            list($instance->totals, $instance->configs) = $im->getDBInformation($instance->settings);

            // Get real time for last login
            if (isset($instance->configs['last_login'])) {
                $instance->configs['last_login'] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $instance->configs['last_login'],
                    new \DateTimeZone('UTC')
                );
            }

            unset($instance->settings);

            $instance->domains = preg_split("@,@", $instance->domains);
            $instance->show_url = $this->generateUrl('manager_instance_show', array('id' => $instance->id));
        }

        return new JsonResponse(array(
            'elements_per_page' => $epp,
            'extra'             => array(),
            'page'              => $page,
            'results'           => $instances,
            'total'             => count($instances),
        ));
    }

    /**
     * Toggle the availability of an instance given its id.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     */
    public function setActivatedAction(Request $request, $id)
    {
        $activated = $request->request->getDigits('value');
        $im        = $this->get('instance_manager');
        $messages  = array();

        $instance = $im->read($id);
        if ($instance !== false) {
            try {
                $im->changeActivated($instance->id, $activated);

                $messages[] = array(
                    'id'        => $id,
                    'activated' => $activated,
                    'message'   => _('Instance updated successfully.'),
                    'type'      => 'success'
                );
            } catch (Exception $e) {
                $messages[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Error while updating instance with id "%s"'), $id),
                    'type'    => 'error'
                );
            }
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
}
