<?php

namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Onm\Framework\Controller\Controller;
use Onm\Exception\InstanceAlreadyExistsException;
use Onm\Exception\InstanceNotFoundException;
use Onm\Exception\AssetsNotDeletedException;
use Onm\Exception\BackupException;
use Onm\Exception\DatabaseNotDeletedException;
use Onm\Exception\DatabaseNotRestoredException;
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
        $instance = new Instance();
        foreach ($request->request as $key => $value) {
            if (!is_null($value)) {
                $instance->{$key} =
                    $request->request->filter($key, null, FILTER_SANITIZE_STRING);
            }
        }
        $instance->created = date('Y-m-d H:i:s');

        $im      = $this->get('instance_manager');
        $creator = new InstanceCreator($im->getConnection());

        try {
            $this->get('onm.validator.instance')->validate($instance);

            $im->persist($instance);

            $creator->createDatabase($instance->id);
            $creator->copyDefaultAssets($instance->internal_name);

            $im->configureInstance($instance);

            $response = new JsonResponse(_('Instance saved successfully'), 201);

            // Add permanent URL for the current instance
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'manager_ws_instance_show',
                    [ 'id' => $instance->id ]
                )
            );

            return $response;
        } catch (InstanceAlreadyExistsException $e) {
            return new JsonResponse(
                _('The instance already exists'),
                409
            );
        } catch (DatabaseNotRestoredException $e) {
            $creator->deleteDatabase($instance->id);
            $im->remove($instance);

            return new JsonResponse(
                _('Unable to create the database for the instance'),
                409
            );
        } catch (\Exception $e) {
            $creator->deleteAssets($instance->internal_name);
            $creator->deleteDatabase($instance->id);
            $im->remove($instance);

            return new JsonResponse(
                _($e->getMessage()),
                409
            );
        }
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

        try {
            $instance = $im->find($id);

            $assetFolder = realpath(
                SITE_PATH . DS . 'media' . DS . $instance->internal_name
            );

            $backupPath = BACKUP_PATH . DS . $instance->id . "-"
                . $instance->internal_name . DS . "DELETED-" . date("YmdHi");

            $database = $instance->getDatabaseName();

            $creator->setBackupPath($backupPath);
            $creator->backupAssets($assetFolder);
            $creator->backupDatabase($database);
            $creator->backupInstance($instance->id);
            $creator->deleteDatabase($database);
            $creator->deleteAssets($instance->internal_name);

            $im->remove($instance);

            return new JsonResponse(_('Instance deleted successfully.'));
        } catch (InstanceNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the instance with id "%s"'), $id),
                404
            );
        } catch (BackupException $e) {
            $message = $e->getMessage();

            $creator->deleteBackup($backupPath);

            return new JsonResponse(sprintf(_($message), $id), 400);
        } catch (DatabaseNotDeletedException $e) {
            $message = $e->getMessage();

            $creator->deleteBackup($backupPath);

            return new JsonResponse(sprintf(_($message), $id), 400);
        } catch (\Exception $e) {
            $creator->restoreAssets($backupPath);
            $creator->restoreDatabase($backupPath . DS . 'database.sql');
            $creator->deleteBackup($backupPath);

            return new JsonResponse(
                sprintf(_('Error while deleting instance with id "%s"'), $id),
                400
            );
        }
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
        $error      = [];
        $messages   = [];
        $selected   = $request->request->get('selected', null);
        $statusCode = 200;
        $updated    = [];

        if (!is_array($selected)
            || (is_array($selected) && count($selected) == 0)
        ) {
            return new JsonResponse(
                _('Unable to find the instances for the given criteria'),
                404
            );
        }

        $im      = $this->get('instance_manager');
        $creator = new InstanceCreator($im->getConnection());

        $criteria = [
            'id' => [
                [ 'value' => $selected, 'operator' => 'IN']
            ]
        ];

        $instances = $im->findBy($criteria);

        foreach ($instances as $instance) {
            try {
                $assetFolder = realpath(
                    SITE_PATH . DS . 'media' . DS . $instance->internal_name
                );

                $backupPath = BACKUP_PATH . DS . $instance->id . "-"
                    . $instance->internal_name . DS . "DELETED-" . date("YmdHi");

                $database = $instance->getDatabaseName();

                $creator->setBackupPath($backupPath);
                $creator->backupAssets($assetFolder);
                $creator->backupDatabase($database);
                $creator->backupInstance($instance->id);

                $creator->deleteAssets($instance->internal_name);
                $creator->deleteDatabase($database);
                $im->remove($instance);

                $updated[] = $instance->id;
            } catch (InstanceNotFoundException $e) {
                $error[]    = $id;
                $messages[] = [
                    'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                    'type'    => 'error'
                ];
            } catch (BackupException $e) {
                $message = $e->getMessage();

                $creator->deleteBackup($backupPath);

                $error[]    = $id;
                $messages[] = [
                    'message' => sprintf(_($message), $id),
                    'type'    => 'error'
                ];
            } catch (AssetsNotDeletedException $e) {
                $message = $e->getMessage();

                $creator->restoreAssets($backupPath);

                $error[]    = $id;
                $messages[] = [
                    'message' => sprintf(_($message), $id),
                    'type'    => 'error'
                ];
            } catch (DatabaseNotDeletedException $e) {
                $message    = $e->getMessage();

                $creator->restoreAssets($backupPath);
                $creator->restoreDatabase($backupPath . DS . 'database.sql');

                $error[]    = $id;
                $messages[] = [
                    'message' => sprintf(_($message), $id),
                    'type'    => 'error'
                ];
            } catch (\Exception $e) {
                $error[]    = $id;
                $messages[] = [
                    'message' => _($e->getMessage()),
                    'type'    => 'error'
                ];
            }
        }

        if (count($updated) > 0) {
            $messages = [
                'message' => sprintf(_('%s instances deleted successfully.'), count($updated)),
                'type'    => 'success'
            ];
        }

        // Return the proper status code
        if (count($error) > 0 && count($updated) > 0) {
            $statusCode = 207;
        } elseif (count($error) > 0) {
            $statusCode = 409;
        }

        return new JsonResponse(
            [ 'error' => $error, 'messages' => $messages ],
            $statusCode
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

        if (!empty($criteria)) {
            $criteria['union'] = 'OR';
        }

        // Put numbers in english format, avoids problem
        setlocale(LC_NUMERIC, 'C');

        $im = $this->get('instance_manager');
        $instances = $im->findBy($criteria, $order, $epp, $page);
        $total = $im->countBy($criteria);

        return new JsonResponse(
            array(
                'epp'     => $epp,
                'page'    => $page,
                'results' => $instances,
                'total'   => $total,
            )
        );
    }

    /**
     * Returns the data to create a new instance.
     *
     * @return JsonResponse The response object.
     */
    public function newAction()
    {
        return new JsonResponse(
            [
                'data'     => null,
                'template' => $this->templateParams()
            ]
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
                    'instance.update',
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
            return new JsonResponse(_($e->getMessage()), 400);
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
        $error      = [];
        $messages   = [];
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
                        'instance.update',
                        array('instance' => $instance->internal_name)
                    );
                }
            } catch (\Exception $e) {
                $error[]    = $instance->id;
                $messages[] = [
                    'message' => _($e->getMessage()),
                    'type'    => 'error',
                ];
            }
        }

        if (count($updated) > 0) {
            $messages[] = [
                'message' => sprintf(
                    _('%s instances updated successfully.'),
                    count($updated)
                ),
                'type' => 'success'
            ];
        }

        if (count($error) > 0 && count($updated) > 0) {
            $statusCode = 207;
        } elseif (count($error) > 0) {
            $statusCode = 409;
        }

        return new JsonResponse(
            [ 'error' => $error, 'messages' => $messages, 'success' => $updated ],
            $statusCode
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
            return new JsonResponse(_($e->getMessage()), 400);
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
            $oldDomains = $instance->domains;

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

            // Delete instance from cache for deleted domains
            $cache = $this->get('cache_manager');

            $deletedDomains = array_diff($oldDomains, $instance->domains);

            foreach ($deletedDomains as $domain) {
                $cache->delete($domain);
            }

            $this->get('onm.validator.instance')->validate($instance);
            $im->persist($instance);
            $im->updateSettings($instance);

            dispatchEventWithParams(
                'instance.update',
                array('instance' => $instance->internal_name)
            );

            return new JsonResponse(_('Instance saved successfully'));
        } catch (InstanceNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the instance with id "%s"'), $id),
                404
            );
        } catch (\Exception $e) {
            return new JsonResponse(_($e->getMessage()), 400);
        }
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function templateParams()
    {
        $themes = $this->get('orm.loader')->getPlugins();

        foreach ($themes as &$theme) {
            $theme = $theme->getData();
        }

        return [
            'languages' => [
                'en_US' => _("English"),
                'es_ES' => _("Spanish"),
                'gl_ES' => _("Galician")
            ],
            'plans'     => [
                'BASIC',
                'PROFESSIONAL',
                'SILVER',
                'GOLD',
                'OTHER',
            ],
            'templates' => im::getAvailableTemplates(),
            'themes'    => $themes,
            'timezones' => \DateTimeZone::listIdentifiers(),
            'available_modules' => mm::getAvailableModulesGrouped(),
        ];
    }
}
