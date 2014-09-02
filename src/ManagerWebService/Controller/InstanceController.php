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
use Onm\Instance\Instance;
use Onm\Instance\InstanceManager as im;
use Onm\Module\ModuleManager as mm;

class InstanceController extends Controller
{
    /**
     * Creates a new instance from the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function createAction(Request $request)
    {
        $success = false;
        $message = array();

        $internalName = $request->request->filter('internal_name', null, FILTER_SANITIZE_STRING);
        $domains      = $request->request->filter('domains', null, FILTER_SANITIZE_STRING);

        if (!$domains) {
            return new JsonResponse(
                array(
                    'success' => false,
                    'message' => array(
                        'type' => 'error',
                        'text' => _('Instance domains cannot be empty')
                    )
                )
            );
        }

        $im = $this->get('instance_manager');

        $criteria = array(
            'domains' => array()
        );

        foreach ($domains as $domain) {
            $criteria['domains']['union'] = 'OR';
            $criteria['domains'][] = array(
                'value' => "^$domain|,[ ]*$domain|$domain$",
                'operator' => 'REGEXP'
            );
        }

        $instance = $im->findOneBy($criteria);

        if ($instance) {
            return new JsonResponse(
                array(
                    'success' => false,
                    'message' => array(
                        'type' => 'error',
                        'text' => _('An instance with that domain already exists')
                    )
                )
            );
        }

        // Create internalName from domains
        if (!$internalName) {
            $internalName = explode('.', array_pop($domains));
            $internalName = array_pop($internalName);
        }

        $internalName = strtolower($internalName);

        $instance = new Instance();
        foreach (array_keys($request->request->all()) as $key) {
            $value = $request->request->filter($key, null, FILTER_SANITIZE_STRING);

            if (!is_null($value)) {
                $instance->{$key} = $value;
            }
        }

        $instance->created = date('Y-m-d H:i:s');

        $creator = new InstanceCreator($im->getConnection());

        $im->checkInternalName($instance);

        try {
            $im->persist($instance);
            $creator->createDatabase($instance->id);
            $creator->copyDefaultAssets($instance->internal_name);

            $im->configureInstance($instance);

            $success = true;
            $message = array(
                'id'   => $instance->id,
                'type' => 'success',
                'text' => 'Instance saved successfully'
            );

        } catch (DatabaseNotRestoredException $e) {
            $errors[] = $e->getMessage();

            $creator->deleteDatabase($instance->id);
            $im->remove($instance);

            $message = array(
                'type' => 'error',
                'text' => _('Unable to create the database for the instance')
            );

        } catch (AssetsNotCopiedException $e) {
            $errors[] = $e->getMessage();

            $creator->deleteAssets($instance->internal_name);
            $creator->deleteDatabase($instance->id);
            $im->remove($instance);

            $message = array(
                'type' => 'error',
                'text' => _('Unable to copy default assets for the instance')
            );
        }

        return new JsonResponse(
            array('success' => true, 'message' => $message)
        );
    }

    /**
     * Deletes an instance.
     *
     * @param integer $id The instance id.
     *
     * @return JsonResponse The response object.
     */
    public function deleteAction($id)
    {
        $im      = $this->get('instance_manager');
        $creator = new InstanceCreator($im->getConnection());
        $message = array();
        $success = false;

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

            $success = true;
            $message = array(
                'text' => _('Instance deleted successfully.'),
                'type' => 'success'
            );
        } catch (InstanceNotFoundException $e) {
            $message = array(
                'text' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                'type' => 'error'
            );
        } catch (BackupException $e) {
            $message = $e->getMessage();

            $creator->deleteBackup($backupPath);

            $message = array(
                'text' => sprintf(_($message), $id),
                'type' => 'error'
            );
        } catch (AssetsNotDeletedException $e) {
            $message = $e->getMessage();

            $creator->restoreAssets($backupPath);

            $message = array(
                'text' => sprintf(_($message), $id),
                'type' => 'error'
            );
        } catch (DatabaseNotDeletedException $e) {
            $message = $e->getMessage();

            $creator->restoreAssets($backupPath);
            $creator->restoreDatabase($backupPath . DS . 'database.sql');

            $message = array(
                'text' => sprintf(_($message), $id),
                'type' => 'error'
            );
        } catch (\Exception $e) {
            $message = array(
                'id'   => $id,
                'text' => sprintf(_('Error while deleting instance with id "%s"'), $id),
                'type' => 'error'
            );
        }

        return new JsonResponse(
            array(
                'success' => $success,
                'message' => $message
            )
        );
    }

    /**
     * Deletes the selected instances.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function deleteSelectedAction(Request $request)
    {
        $messages = array();
        $success  = false;
        $updated  = 0;

        $selected  = $request->request->get('selected', null);

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

                    $updated++;
                } catch (InstanceNotFoundException $e) {
                    $errors[] = array(
                        'text' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                        'type' => 'error'
                    );
                } catch (BackupException $e) {
                    $message = $e->getMessage();

                    $creator->deleteBackup($backupPath);

                    $errors[] = array(
                        'text' => sprintf(_($message), $id),
                        'type' => 'error'
                    );
                } catch (AssetsNotDeletedException $e) {
                    $message = $e->getMessage();

                    $creator->restoreAssets($backupPath);

                    $errors[] = array(
                        'text' => sprintf(_($message), $id),
                        'type' => 'error'
                    );
                } catch (DatabaseNotDeletedException $e) {
                    $message = $e->getMessage();

                    $creator->restoreAssets($backupPath);
                    $creator->restoreDatabase($backupPath . DS . 'database.sql');

                    $errors[] = array(
                        'text' => sprintf(_($message), $id),
                        'type' => 'error'
                    );
                } catch (\Exception $e) {
                    $errors[] = array(
                        'text' => sprintf(_('Error while deleting instance with id "%s"'), $id),
                        'type' => 'error'
                    );
                }
            }
        }

        if (count($updated) > 0) {
            $success = true;

            array_unshift(
                $messages,
                array(
                    'text' => sprintf(_('%s instances deleted successfully.'), count($updated)),
                    'type' => 'success'
                )
            );
        }

        return new JsonResponse(
            array(
                'success'  => $success,
                'messages' => $messages
            )
        );
    }

    /**
     * Returns a CSV file with all the instances information.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
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
     * Returns the data to create a new instance.
     *
     * @return JsonResponse The response object.
     */
    public function newAction()
    {
        return new JsonResponse(
            array(
                'data'     => null,
                'template' => $this->templateParams()
            )
        );
    }

    /**
     * Returns the list of instances as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $epp      = $request->request->getDigits('epp', 10);
        $page     = $request->request->getDigits('page', 1);
        $criteria = $request->request->filter('criteria') ? : array();
        $orderBy  = $request->request->filter('orderBy') ? : array();

        $im = $this->get('instance_manager');
        $instances = $im->findBy($criteria, $orderBy, $epp, $page);
        $total = $im->countBy($criteria);

        return new JsonResponse(
            array(
                'epp'     => $epp,
                'extra'   => array(),
                'page'    => $page,
                'results' => $instances,
                'total'   => $total,
            )
        );
    }

    /**
     * Toggle the availability of an instance given its id.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function setEnabledAction(Request $request, $id)
    {
        $success  = false;
        $activated = $request->request->getDigits('enabled');
        $im        = $this->get('instance_manager');
        $message   = array();

        $instance = $im->find($id);
        if ($instance) {
            try {
                $instance->activated = $activated;
                $im->persist($instance);

                $success = true;
                $message = array(
                    'text'      => _('Instance updated successfully.'),
                    'type'      => 'success'
                );
            } catch (Exception $e) {
                $message = array(
                    'text' => sprintf(_('Error while updating instance with id "%s"'), $id),
                    'type' => 'error'
                );
            }
        } else {
            $messages = array(
                'text' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                'type' => 'error'
            );
        }

        return new JsonResponse(
            array(
                'success'   => $success,
                'activated' => $activated,
                'message'   => $message
            )
        );
    }

    /**
     * Set the activated flag for instances in batch.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function setEnabledSelectedAction(Request $request)
    {
        $messages = array();
        $success  = false;
        $updated  = 0;

        $selected  = $request->request->get('selected', null);
        $activated = $request->request->getDigits('enabled', 0);

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
                        $messages[] = array(
                            'text' => sprintf(_('Error while updating instance with id "%s"'), $id),
                            'type' => 'error'
                        );
                    }
                } else {
                    $messages[] = array(
                        'text' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                        'type' => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success = true;

            array_unshift(
                $messages,
                array(
                    'text' => sprintf(_('%s instances updated successfully.'), $updated),
                    'type' => 'success'
                )
            );
        }

        return new JsonResponse(
            array(
                'success'  => $success,
                'messages' => $messages
            )
        );
    }

    /**
     * Returns an instance as JSON.
     *
     * @param integer  $id The instance id.
     *
     * @return Response The response object.
     */
    public function showAction($id)
    {
        $im = $this->get('instance_manager');

        $instance = $im->find($id);
        $im->getExternalInformation($instance);

        return new JsonResponse(
            array(
                'instance' => $instance,
                'template' => $this->templateParams()
            )
        );
    }

    /**
     * Updates the instance information gives its id
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The instance id.
     * @return Response          The response object.
     */
    public function updateAction(Request $request, $id)
    {
        $success = false;
        $message = array();

        $im = $this->get('instance_manager');
        $sm = $this->get('setting_repository');

        $internalName = $request->request->filter('internal_name', null, FILTER_SANITIZE_STRING);
        $domains      = $request->request->filter('domains', null, FILTER_SANITIZE_STRING);

        if (!$domains) {
            return new JsonResponse(
                array(
                    'success' => false,
                    'message' => array(
                        'type' => 'error',
                        'text' => 'Instance domains cannot be empty'
                    )
                )
            );
        }

        $criteria = array(
            'domains' => array()
        );

        foreach ($domains as $domain) {
            $criteria['domains']['union'] = 'OR';
            $criteria['domains'][] = array(
                'value' => "^$domain|,[ ]*$domain|$domain$",
                'operator' => 'REGEXP'
            );
        }

        $instance = $im->findOneBy($criteria);

        if ($instance && $instance->id != $id) {
            return new JsonResponse(
                array(
                    'success' => false,
                    'message' => array(
                        'type' => 'error',
                        'text' => _('An instance with that domain already exists')
                    )
                )
            );
        }

        // Create internalName from domains
        if (!$internalName) {
            $internalName = explode('.', array_pop($domains));
            $internalName = array_pop($internalName);
        }

        $internalName = strtolower($internalName);

        try {
            $instance = $im->find($id);

            $im->checkInternalName($instance);

            foreach (array_keys($request->request->all()) as $key) {
                $value = $request->request->filter($key, null, FILTER_SANITIZE_STRING);

                if (!is_null($value)) {
                    $instance->{$key} = $value;
                }
            }

            $instance->created = date('Y-m-d H:i:s');

            $im->persist($instance);
            $im->configureInstance($instance);

            $success = true;
            $message = array(
                'type' => 'success',
                'text' => 'Instance saved successfully'
            );

        } catch (InstanceNotFoundException $e) {
            $message = array(
                'text' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                'type' => 'error'
            );
        } catch (\Exception $e) {
            $message = array(
                'text' => sprintf(_('Error while deleting instance with id "%s"'), $id),
                'type' => 'error'
            );
        }

        return new JsonResponse(
            array('success' => $success, 'message' => $message)
        );
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function templateParams()
    {
        return [
            'languages' => [
                'en_US' => _("English"),
                'es_ES' => _("Spanish"),
                'gl_ES' => _("Galician")
            ],
            'plans'     => [
                'Base',
                'Profesional',
                'Silver',
                'Gold',
                'Other',
            ],
            'templates' => im::getAvailableTemplates(),
            'timezones' => \DateTimeZone::listIdentifiers(),
            'available_modules' => mm::getAvailableModulesGrouped(),
        ];
    }
}
