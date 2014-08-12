<?php

namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

use Onm\Exception\InstanceNotFoundException;
use Onm\Exception\AssetsNotDeletedException;
use Onm\Exception\BackupException;
use Onm\Exception\DatabaseNotDeletedException;
use Onm\Instance\InstanceCreator;

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
        $errors  = array();
        $success = array();
        $updated = array();

        $selected  = $request->request->get('ids', null);
        $activated = $request->request->getDigits('value', 0);

        if (is_array($selected) && count($selected) > 0) {
            $im      = $this->get('instance_manager');
            $creator = new InstanceCreator($im->getConnection());

            foreach ($selected as $id) {
                try {
                    $instance = $im->find($id);

                    $assetFolder = realpath(
                        SITE_PATH . DS . 'media' . DS . $instance->internal_name
                    );

                    $backupPath = BACKUP_PATH . DS . $instance->id . "-"
                        . $instance->internal_name . DS . "DELETED-" . date("YmdHi");

                    $database = $instance->getDatabaseName();

                    $creator->backupAssets($assetFolder, $backupPath);
                    $creator->backupDatabase($database, $backupPath);
                    $creator->backupInstance($database, $instance->id, $backupPath);

                    $creator->deleteAssets($instance->internal_name);
                    $creator->deleteDatabase($database);
                    $im->remove($instance);

                    $updated[] = $id;
                } catch (InstanceNotFoundException $e) {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                        'type'    => 'error'
                    );
                } catch (BackupException $e) {
                    $message = $e->getMessage();

                    $creator->deleteBackup($backupPath);

                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_($message), $id),
                        'type'    => 'error'
                    );
                } catch (AssetsNotDeletedException $e) {
                    $message = $e->getMessage();

                    $creator->restoreAssets($backupPath);

                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_($message), $id),
                        'type'    => 'error'
                    );
                } catch (DatabaseNotDeletedException $e) {
                    $message = $e->getMessage();

                    $creator->restoreAssets($backupPath);
                    $creator->restoreDatabase($backupPath . DS . 'database.sql');

                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_($message), $id),
                        'type'    => 'error'
                    );
                } catch (\Exception $e) {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Error while deleting instance with id "%s"'), $id),
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
                'messages' => array_merge($success, $errors)
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
                $instance = $im->find($id);
                if ($instance) {
                    try {
                        $instance->activated = $activated;
                        $im->persist($instance);
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
        $im       = $this->get('instance_manager');
        $creator  = new InstanceCreator($im->getConnection());
        $messages = array();

        try {
            $instance = $im->find($id);

            $assetFolder = realpath(
                SITE_PATH . DS . 'media' . DS . $instance->internal_name
            );

            $backupPath = BACKUP_PATH . DS . $instance->id . "-"
                . $instance->internal_name . DS . "DELETED-" . date("YmdHi");

            $database = $instance->getDatabaseName();

            $creator->backupAssets($assetFolder, $backupPath);
            $creator->backupDatabase($database, $backupPath);
            $creator->backupInstance($database, $instance->id, $backupPath);

            $creator->deleteAssets($instance->internal_name);
            $creator->deleteDatabase($database);
            $im->remove($instance);

            $messages[] = array(
                'id'        => $id,
                'message'   => _('Instance deleted successfully.'),
                'type'      => 'success'
            );
        } catch (InstanceNotFoundException $e) {
            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                'type'    => 'error'
            );
        } catch (BackupException $e) {
            $message = $e->getMessage();

            $creator->deleteBackup($backupPath);

            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_($message), $id),
                'type'    => 'error'
            );
        } catch (AssetsNotDeletedException $e) {
            $message = $e->getMessage();

            $creator->restoreAssets($backupPath);

            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_($message), $id),
                'type'    => 'error'
            );
        } catch (DatabaseNotDeletedException $e) {
            $message = $e->getMessage();

            $creator->restoreAssets($backupPath);
            $creator->restoreDatabase($backupPath . DS . 'database.sql');

            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_($message), $id),
                'type'    => 'error'
            );
        } catch (\Exception $e) {
            $messages[] = array(
                'id'      => $id,
                'message' => sprintf(_('Error while deleting instance with id "%s"'), $id),
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

        $criteria = array();
        $order    = array('id' => 'asc');

        if (!empty($name)) {
            $criteria['name'] = array(
                array('value' => "%$name%", 'operator' => 'LIKE')
            );
        }

        if (!empty($email)) {
            $criteria['contact_mail'] = array(
                array('value' => "%$email%", 'operator' => 'LIKE')
            );
        }

        $im = $this->get('instance_manager');
        $instances = $im->findBy($criteria, $order);

        foreach ($instances as &$instance) {
            $im->getExternalInformation($instance);
        }

        $this->view = new \TemplateManager(TEMPLATE_MANAGER);

        $response = $this->render(
            'instances/csv.tpl',
            array(
                'instances' => $instances
            )
        );

        if ($name != '*') {
            $fileNameFilter = '-'.\Onm\StringUtils::get_title($name);
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
        $epp       = $request->request->getDigits('elements_per_page', 10);
        $page      = $request->request->getDigits('page', 1);
        $criteria  = $request->request->filter('search');
        $sortBy    = $request->request->filter('sort_by');
        $sortOrder = $request->request->filter('sort_order');
        $order     = array($sortBy => $sortOrder);

        if (array_key_exists('name', $criteria)) {
            $criteria['domains'] = $criteria['name'];
            $criteria['union'] = 'OR';
        }

        unset($criteria['content_type_name']);

        $im = $this->get('instance_manager');
        $instances = $im->findBy($criteria, $order, $epp, $page);
        $total = $im->countBy($criteria);

        foreach ($instances as &$instance) {
            $instance->show_url = $this->generateUrl(
                'manager_instance_show',
                array('id' => $instance->id)
            );
        }

        return new JsonResponse(array(
            'elements_per_page' => $epp,
            'extra'             => array(),
            'page'              => $page,
            'results'           => $instances,
            'total'             => $total,
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

        $instance = $im->find($id);
        if ($instance) {
            try {
                $instance->activated = $activated;
                $im->persist($instance);

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
