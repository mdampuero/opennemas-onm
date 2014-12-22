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
                if ($key == 'domain_expire') {
                    $value = new \Datetime($value);
                    $value = $value->format('Y-m-d H:i:s');
                } elseif ($key == 'external' && array_key_exists('last_invoice', $value)) {
                    $value['last_invoice'] = new \Datetime($value['last_invoice']);
                    $value['last_invoice'] = $value['last_invoice']->format('Y-m-d H:i:s');
                }

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
                'text' => _('Instance saved successfully')
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

        if ($updated) {
            $success = true;

            array_unshift(
                $messages,
                array(
                    'text' => sprintf(_('%s instances deleted successfully.'), $updated),
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
        $search = $request->query->filter('search', '', FILTER_SANITIZE_STRING);
        $ids    = $request->query->filter('ids', '', FILTER_SANITIZE_STRING);

        $criteria = array();
        $order    = array('id' => 'asc');

        if (!empty($search)) {
            $criteria = array(
                'name' => array(
                    array('value' => "%$search%", 'operator' => 'LIKE')
                ),
                'contact_mail' => array(
                    array('value' => "%$search%", 'operator' => 'LIKE')
                )
            );
        } elseif (!empty($ids)) {
            $criteria = array(
                'id' => array(
                    array('value' => explode(',', $ids), 'operator' => 'IN')
                ),
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

        if (!empty($search) && $search != '*') {
            $fileNameFilter = '-'.\Onm\StringUtils::getTitle($search);
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
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);
        $criteria = $request->query->filter('criteria') ? : array();
        $orderBy  = $request->query->filter('orderBy') ? : array();

        $order = array();
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        $im = $this->get('instance_manager');
        $instances = $im->findBy($criteria, $order, $epp, $page);
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
     * Updates some instance properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchAction(Request $request, $id)
    {
        $im = $this->get('instance_manager');

        try {
            $instance = $im->find($id);
            $oldActivated = $instance->activated;

            foreach ($request->request->all() as $key => $value) {
                $instance->{$key} =
                    $request->request->filter($key, null, FILTER_SANITIZE_STRING);
            }

            $this->get('onm.validator.instance')->validate($instance);
            $im->persist($instance);

            if ($oldActivated != $instance->activated) {
                dispatchEventWithParams(
                    'instance.disable',
                    array('instance' => $instance->internal_name)
                );
            }

            return new JsonResponse(_('Instance saved successfully'));
        } catch (InstanceNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the instance with id "%s"'), $id),
                404
            );
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Set the activated flag for instances in batch.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchSelectedAction(Request $request)
    {
        $messages   = [ 'errors' => [], 'success' => [] ];
        $selected   = $request->request->get('selected', null);
        $statusCode = 200;
        $updated    = [];

        if (is_array($selected) && count($selected) == 0) {
            return new JsonResponse(
                _('Unable to find the instances for the given criteria'),
                404
            );
        }

        $im = $this->get('instance_manager');

        $criteria = [
            'id' => [
                [ 'value' => $selected, 'operator' => 'IN']
            ]
        ];

        $instances = $im->findBy($criteria);

        foreach ($instances as $instance) {
            try {
                $oldActivated = $instance->activated;

                foreach ($request->request->all() as $key => $value) {
                    $instance->{$key} =
                        $request->request->filter($key, null, FILTER_SANITIZE_STRING);
                }

                $this->get('onm.validator.instance')->validate($instance);
                $im->persist($instance);
                $updated[] = $instance->id;

                if ($oldActivated != $instance->activated) {
                    dispatchEventWithParams(
                        'instance.disable',
                        array('instance' => $instance->internal_name)
                    );
                }
            } catch (\Exception $e) {
                $messages['errors'][] = [
                    'id'    => $instance->id,
                    'error' => $e->getMessage()
                ];

                $statusCode = 207;
            }
        }

        if (count($updated) > 0) {
            $messages['success'] = [
                'ids'     => $updated,
                'message' => sprintf(_('%s instances updated successfully.'), count($updated))
            ];
        }

        return new JsonResponse($messages, $statusCode);
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

        try {
            $instance = $im->find($id);
            $im->getExternalInformation($instance);

            return new JsonResponse(
                array(
                    'instance' => $instance,
                    'template' => $this->templateParams()
                )
            );
        } catch (InstanceNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the instance with id "%s"'), $id),
                404
            );
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 400);
        }
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
        $im = $this->get('instance_manager');

        try {
            $instance = $im->find($id);

            $keys = array_unique(array_merge(
                array_keys($request->request->all()),
                array_keys(get_object_vars($instance))
            ));

            foreach ($keys as $key) {
                if ($request->request->get($key)
                    && !is_null($request->request->get($key))
                ) {
                    $instance->{$key} =
                        $request->request->filter($key, null, FILTER_SANITIZE_STRING);
                } else {
                    $instance->{$key} = null;
                }
            }

            $this->get('onm.validator.instance')->validate($instance);
            $im->persist($instance);
            $im->updateSettings($instance);

            return new JsonResponse(_('Instance saved successfully'));
        } catch (InstanceNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the instance with id "%s"'), $id),
                404
            );
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 400);
        }
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
