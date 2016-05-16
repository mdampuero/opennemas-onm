<?php

namespace ManagerWebService\Controller;

use Onm\Framework\Controller\Controller;
use Onm\Exception\InstanceAlreadyExistsException;
use Onm\Exception\InstanceNotFoundException;
use Onm\Exception\AssetsNotDeletedException;
use Onm\Exception\BackupException;
use Onm\Exception\DatabaseNotDeletedException;
use Onm\Exception\DatabaseNotRestoredException;
use Onm\Instance\InstanceCreator;
use Common\ORM\Entity\Instance;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

class InstanceController extends Controller
{
    /**
     * Deletes an instance.
     *
     * @param integer $id The instance id.
     *
     * @return JsonResponse The response object.
     */
    public function deleteAction($id)
    {
        $em      = $this->get('orm.manager');
        $msg     = $this->get('core.messenger');
        $creator = new InstanceCreator($em->getConnection('manager'));

        try {
            $instance = $em->getRepository('Instance')->find($id);

            $assetFolder = realpath(
                SITE_PATH . DS . 'media' . DS . $instance->internal_name
            );

            $backupPath = $this->getParameter('kernel.root_dir')
                . '/../tmp/backups/' . $instance->internal_name . '/DELETED-'
                . date('YmdHis');

            $database = $instance->getDatabaseName();

            $creator->setBackupPath($backupPath);
            $creator->backupAssets($assetFolder);
            $creator->backupDatabase($database);
            $creator->backupInstance($instance->id);
            $creator->deleteDatabase($database);
            $creator->deleteAssets($instance->internal_name);

            $em->remove($instance);

            $msg->add(_('Instance deleted successfully'), 'success');
        } catch (BackupException $e) {
            $creator->deleteBackup($backupPath);
            $msg->add($e->getMessage(), 'error', 400);
        } catch (DatabaseNotDeletedException $e) {
            $creator->deleteBackup($backupPath);
            $msg->add($e->getMessage(), 'error', 400);
        } catch (\Exception $e) {
            $creator->restoreAssets($backupPath);
            $creator->restoreDatabase($backupPath . DS . 'database.sql');
            $creator->deleteBackup($backupPath);

            $msg->add(
                sprintf(_('Error while deleting instance with id "%s"'), $id),
                'error',
                400
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
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
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || empty($ids)) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em      = $this->get('orm.manager');
        $creator = new InstanceCreator($em->getConnection('manager'));
        $oql     = sprintf('id in [%s]', implode(',', $ids));

        $instances = $em->getRepository('Instance')->findBy($oql);

        $deleted = 0;
        foreach ($instances as $instance) {
            try {
                $assetFolder = realpath(
                    SITE_PATH . DS . 'media' . DS . $instance->internal_name
                );

                $backupPath = $this->getParameter('kernel.root_dir')
                    . '/../tmp/backups/' . $instance->internal_name . '/DELETED-'
                    . date('YmdHis');

                $database = $instance->getDatabaseName();

                $creator->setBackupPath($backupPath);
                $creator->backupAssets($assetFolder);
                $creator->backupDatabase($database);
                $creator->backupInstance($instance->id);

                $creator->deleteAssets($instance->internal_name);
                $creator->deleteDatabase($database);
                $em->remove($instance);

                $deleted++;
            } catch (BackupException $e) {
                $creator->deleteBackup($backupPath);
                $msg->add($e->getMessage(), 'error', 400);
            } catch (DatabaseNotDeletedException $e) {
                $creator->deleteBackup($backupPath);
                $msg->add($e->getMessage(), 'error', 400);
            } catch (\Exception $e) {
                $creator->restoreAssets($backupPath);
                $creator->restoreDatabase($backupPath . DS . 'database.sql');
                $creator->deleteBackup($backupPath);

                $msg->add(
                    sprintf(_('Error while deleting instance with id "%s"'), $id),
                    'error',
                    400
                );
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s instances deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
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
        $oql = $request->query->get('oql', '');
        $ids = $request->query->filter('ids');

        $repository = $this->get('orm.manager')->getRepository('Instance');
        $converter  = $this->get('orm.manager')->getConverter('Instance');
        $instances  = $repository->findBy($oql);

        $this->view = new \TemplateManager(TEMPLATE_MANAGER);

        $response = $this->render(
            'instances/csv.tpl',
            [ 'instances' => $instances ]
        );

        $name = '';
        if (!empty($oql)) {
            $name = '-' . \Onm\StringUtils::getTitle($oql);
        }

        $filename = 'opennemas-instances' . $name . '.' . date("YmdHis") . '.csv';

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Returns the list of instances.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('Instance');
        $converter  = $this->get('orm.manager')->getConverter('Instance');

        $instances = $repository->findBy($oql);
        $total     = $repository->countBy($oql);

        $instances = array_map(function ($a) use ($converter) {
            return $converter->responsify($a->getData());
        }, $instances);

        return new JsonResponse([
            'total'   => $total,
            'results' => $instances,
        ]);
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
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');
        $data = $em->getConverter('Instance')
            ->objectify($request->request->all());

        $instance = $em->getRepository('Instance')->find($id);

        $old = $instance->activated;
        $instance->merge($data);

        $em->persist($instance);

        if ($old != $instance->activated) {
            dispatchEventWithParams(
                'instance.update',
                [ 'instance' => $instance->internal_name ]
            );
        }

        $msg->add(_('Instance saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
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
     * Creates a new instance from the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function saveAction(Request $request)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('Instance')
            ->objectify($request->request->all());

        $instance = new Instance($data);
        $creator  = new InstanceCreator($em->getConnection('manager'));

        $instance->created = new \DateTime('now');

        try {
            $this->get('core.instance.checker')->check($instance);
            $em->persist($instance);

            $creator->createDatabase($instance->id);
            $creator->copyDefaultAssets($instance->internal_name);

            //$im->configureInstance($instance);

            $msg->add(_('Instance saved successfully'), 'success', 201);

            // Add permanent URL for the current instance
            $response = new JsonResponse($msg->getMessages(), $msg->getCode());
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
            $em->remove($instance);

            return new JsonResponse(
                _('Unable to create the database for the instance'),
                409
            );
        } catch (\Exception $e) {
            $creator->deleteAssets($instance->internal_name);
            $creator->deleteDatabase($instance->id);
            $em->remove($instance);

            return new JsonResponse(
                _($e->getMessage()),
                409
            );
        }
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
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('Instance');
        $instance  = $em->getRepository('Instance')->find($id);

        $instance->settings['TEMPLATE_USER'] = 'es.openhost.theme.'
            . str_replace('es.openhost.theme.', '', $instance->settings['TEMPLATE_USER']);

        $extra = $this->getExtraData();

        //$im->getExternalInformation($instance);
        if (!empty($instance->getClient())) {
            try {
                $client = $this->get('orm.manager')
                    ->getRepository('Client')
                    ->find($instance->getClient());

                $extra['client']    = $client->getData();
                $extra['countries'] = Intl::getRegionBundle()->getCountryNames();
            } catch (\Exception $e) {
            }
        }

        return new JsonResponse([
            'extra'    => $extra,
            'instance' => $converter->responsify($instance->getData())
        ]);
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
    private function getExtraData()
    {
        $lang    = $this->get('core.locale')->getLocaleShort();
        //$themes  = $this->get('orm.loader')->getPlugins();
        $modules = $this->get('orm.manager')->getRepository('Extension')
            ->findBy('type = "module" or type = "theme-addon" limit 10');

        //$modules = [];
        $themes = [];
        $modules = array_map(function (&$a) {
            foreach ([ 'about', 'description', 'name' ] as $key) {
                if (!empty($a->{$key})) {
                    $lang = $a->{$key}['en'];

                    if (array_key_exists($lang, $a->{$key})
                        && !empty($a->{$key}[$lang])
                    ) {
                        $lang = $a->{$key}[$lang];
                    }

                    $a->{$key} = $lang;
                }
            }

            return $a->getData();
        }, $modules);

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
                'ADVANCED',
                'EXPERT',
                'OTHER',
            ],
            //'templates' => im::getAvailableTemplates(),
            'themes'    => $themes,
            'timezones' => \DateTimeZone::listIdentifiers(),
            'modules'   => $modules,
        ];
    }
}
