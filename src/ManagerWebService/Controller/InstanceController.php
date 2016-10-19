<?php

namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use Common\ORM\Entity\Instance;
use Onm\Exception\AssetsNotDeletedException;
use Onm\Exception\BackupException;
use Onm\Exception\DatabaseNotDeletedException;
use Onm\Exception\DatabaseNotRestoredException;
use Onm\Exception\InstanceAlreadyExistsException;
use Onm\Exception\InstanceNotFoundException;
use Onm\Framework\Controller\Controller;
use Onm\Instance\InstanceCreator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InstanceController extends Controller
{
    /**
     * Deletes an instance.
     *
     * @param integer $id The instance id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('INSTANCE_DELETE')")
     */
    public function deleteAction($id)
    {
        $em      = $this->get('orm.manager');
        $msg     = $this->get('core.messenger');
        $creator = new InstanceCreator($em->getConnection('manager'));

        try {
            $instance = $em->getRepository('Instance')->find($id);

            if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
                throw new AccessDeniedException();
            }

            $assetFolder = realpath(
                SITE_PATH . DS . 'media' . DS . $instance->internal_name
            );

            $backupPath = $this->getParameter('kernel.root_dir')
                . '/../tmp/backups/' . $instance->internal_name . '/DELETED-'
                . date('YmdHis');

            $database = $instance->getDatabaseName();

            $em->remove($instance);
            $msg->add(_('Instance deleted successfully'), 'success');

            $this->get('core.dispatcher')
                ->dispatch('instance.delete', [ 'instance' => $instance ]);

            $creator->setBackupPath($backupPath);
            $creator->backupAssets($assetFolder);
            $creator->backupDatabase($database);
            $creator->backupInstance($instance->id);
            $creator->deleteDatabase($database);
            $creator->deleteAssets($instance->internal_name);
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
     *
     * @Security("hasPermission('INSTANCE_DELETE')")
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
                if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
                    throw new AccessDeniedException();
                }

                $assetFolder = realpath(
                    SITE_PATH . DS . 'media' . DS . $instance->internal_name
                );

                $backupPath = $this->getParameter('kernel.root_dir')
                    . '/../tmp/backups/' . $instance->internal_name . '/DELETED-'
                    . date('YmdHis');

                $database = $instance->getDatabaseName();

                $em->remove($instance);
                $deleted++;

                $this->get('core.dispatcher')
                    ->dispatch('instance.delete', [ 'instance' => $instance ]);

                $creator->setBackupPath($backupPath);
                $creator->backupAssets($assetFolder);
                $creator->backupDatabase($database);
                $creator->backupInstance($instance->id);

                $creator->deleteAssets($instance->internal_name);
                $creator->deleteDatabase($database);
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
                    sprintf(_('Error while deleting instance with id "%s"'), $instance->id),
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
     *
     * @Security("hasPermission('INSTANCE_REPORT')")
     */
    public function exportAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        if (!$this->get('core.security')->hasPermission('MASTER')
            && $this->get('core.security')->hasPermission('PARTNER')
        ) {
            if (!empty($oql) && !preg_match('/^(order|limit)/', $oql)) {
                $oql = ' and ' . $oql;
            }

            $oql = sprintf('owner_id = "%s"', $this->get('core.user')->id)
                .  $oql;
        }

        $repository = $this->get('orm.manager')->getRepository('Instance');
        $instances  = $repository->findBy($oql);

        $response = $this->render(
            'instance/csv.tpl',
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
     *
     * @Security("hasPermission('INSTANCE_LIST')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        // Fix OQL for Non-MASTER users
        if (!$this->get('core.security')->hasPermission('MASTER')) {
            $condition = sprintf('owner_id = %s ', $this->get('core.user')->id);

            $oql = $this->get('orm.oql.fixer')->fix($oql)
                ->addCondition($condition)->getOql();
        }

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
     *
     * @Security("hasPermission('INSTANCE_CREATE')")
     */
    public function newAction()
    {
        $security = $this->get('core.security');

        if (!$security->hasPermission('MASTER')
            && count($security->getInstances())
                >= $security->getUser()->max_instances
        ) {
            throw new AccessDeniedException(
                '<p>' . _('You have reached the maximum number of instances.') . '</p><p>'
                .sprintf(
                    _('If you need to create more instances, please <a class="bold text-danger" href="mailto:%s">contact us</a>.'),
                    $this->getParameter('manager_webservice')['company_mail'],
                    $this->getParameter('manager_webservice')['company_mail']
                )
                . '</p>'
            );
        }

        return new JsonResponse(
            [
                'data'     => null,
                'template' => $this->getTemplateParams()
            ]
        );
    }

    /**
     * Updates some instance properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('INSTANCE_UPDATE')")
     */
    public function patchAction(Request $request, $id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');
        $data = $em->getConverter('Instance')
            ->objectify($request->request->all());

        $instance = $em->getRepository('Instance')->find($id);

        if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
            throw new AccessDeniedException();
        }

        $old = $instance->activated;
        $instance->merge($data);

        $em->persist($instance);

        if ($old != $instance->activated) {
            $this->get('core.dispatcher')
                ->dispatch('instance.update', [ 'instance' => $instance ]);
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
     *
     * @Security("hasPermission('INSTANCE_UPDATE')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        if (!is_array($ids) || count($ids) === 0) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em   = $this->get('orm.manager');
        $oql  = sprintf('id in [%s]', implode(',', $ids));
        $data = $em->getConverter('Instance')->objectify($params);

        $instances = $em->getRepository('Instance')->findBy($oql);

        $updated = 0;
        foreach ($instances as $instance) {
            try {
                if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
                    throw new AccessDeniedException();
                }

                $old = $instance->activated;
                $instance->merge($data);
                $em->persist($instance);
                $updated++;

                if ($old !== $instance->activated) {
                    $this->get('core.dispatcher')
                        ->dispatch('instance.update', [ 'instance' => $instance ]);
                }
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error', $e->getCode());
            }
        }

        if (count($updated) > 0) {
            $msg->add(
                sprintf(_('%s instances saved successfully'), $updated),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Creates a new instance from the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('INSTANCE_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $security = $this->get('core.security');
        $user     = $security->getUser();

        if (!$security->hasPermission('MASTER')
            && count($security->getInstances()) >= $user->max_instances
        ) {
            throw new AccessDeniedException(
                '<p>' . _('You have reached the maximum number of instances.') . '</p><p>'
                .sprintf(
                    _('If you need to create more instances, please <a class="bold text-danger" href="mailto:%s">contact us</a>.'),
                    $this->getParameter('manager_webservice')['company_mail'],
                    $this->getParameter('manager_webservice')['company_mail']
                )
                . '</p>'
            );
        }

        $em       = $this->get('orm.manager');
        $msg      = $this->get('core.messenger');
        $settings = $request->request->get('settings');
        $data     = $em->getConverter('Instance')
            ->objectify($request->request->get('instance'));

        $instance = new Instance($data);
        $creator  = new InstanceCreator($em->getConnection('manager'));

        $instance->created = new \DateTime('now');

        if (!$security->hasPermission('MASTER')) {
            $instance->owner_id = $user->id;
        }

        try {
            $this->get('core.instance.checker')->check($instance);
            $em->persist($instance);

            if (empty($instance->getDatabaseName())) {
                $instance->refresh();
                $instance->settings['BD_DATABASE'] = $instance->id;
                $em->persist($instance);
            }

            $creator->createDatabase($instance->id);
            $creator->copyDefaultAssets($instance->internal_name);

            $this->get('core.loader')->configureInstance($instance);

            $em->getConnection('instance')
                ->selectDatabase($instance->getDatabaseName());

            $em->getDataSet('Settings', 'instance')->set($settings);

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
     *
     * @Security("hasPermission('INSTANCE_UPDATE')")
     */
    public function showAction($id)
    {
        $em        = $this->get('orm.manager');
        $instance  = $em->getRepository('Instance')->find($id);

        if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
            throw new AccessDeniedException();
        }

        $converter = $em->getConverter('Instance');
        $ds        = $em->getDataSet('Settings', 'instance');

        $instance->settings['TEMPLATE_USER'] = 'es.openhost.theme.'
            . str_replace('es.openhost.theme.', '', $instance->settings['TEMPLATE_USER']);

        $this->get('core.loader')->configureInstance($instance);

        $em->getConnection('instance')
            ->selectDatabase($instance->getDatabaseName());

        $settings = $ds->get([ 'max_mailing', 'pass_level', 'piwik', 'time_zone' ]);
        $template = $this->getTemplateParams();

        if (!empty($instance->getClient())) {
            try {
                $client = $this->get('orm.manager')
                    ->getRepository('Client')
                    ->find($instance->getClient());

                $template['client']    = $client->getData();
                $template['countries'] = Intl::getRegionBundle()->getCountryNames();
            } catch (\Exception $e) {
            }
        }

        return new JsonResponse([
            'template' => $template,
            'instance' => $converter->responsify($instance->getData()),
            'settings' => $settings
        ]);
    }

    /**
     * Updates the instance information gives its id
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The instance id.
     * @return Response          The response object.
     *
     * @Security("hasPermission('INSTANCE_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $em       = $this->get('orm.manager');
        $msg      = $this->get('core.messenger');
        $settings = $request->request->get('settings');
        $data     = $em->getConverter('Instance')
            ->objectify($request->request->get('instance'));

        $instance = $em->getRepository('Instance')->find($id);

        if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
            throw new AccessDeniedException();
        }

        $owners     = [ 'user-' . $instance->owner_id ];
        $oldDomains = $instance->domains;

        $instance->setData($data);
        $owners[] = 'user-' . $instance->owner_id;
        $owners = array_unique(array_filter($owners, function ($a) {
            return !empty($a);
        }));

        $deletedDomains = array_diff($oldDomains, $instance->domains);

        $cache = $this->get('cache.manager')->getConnection('manager');
        if (!empty($deletedDomains)) {
            $cache->delete($deletedDomains);
        }

        if (!empty($owners)) {
            $cache->delete($owners);
        }

        $em->persist($instance);

        // Update settings for instance
        $this->get('core.loader')->configureInstance($instance);
        $em->getDataSet('Settings', 'instance')->set($settings);

        // TODO: Fix clean caches
        foreach ($settings as $key => $setting) {
            $this->get('setting_repository')
                ->invalidate($key, $instance->internal_name);
        }

        $this->get('core.dispatcher')
            ->dispatch('instance.update', [ 'instance' => $instance ]);

        $msg->add(_('Instance saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Ads additional filters when the current user is not a MASTER.
     *
     * @param string $oql The OQL to modify.
     *
     * @return string The OQL with additional filters.
     */
    private function fixOqlForPartners($oql)
    {
        if ($this->get('core.security')->hasPermission('MASTER')) {
            return $oql;
        }

        // Surround current OQL by parenthesis
        if (!empty($oql) && !preg_match('/^\s*(order|limit)/', $oql)) {
            $filters = trim(preg_split('/order|limit|offset/', $oql)[0]);
            $oql = ' and' . str_replace($filters, " ($filters) ", $oql );
        }

    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function getTemplateParams()
    {
        $lang    = $this->get('core.locale')->getLocaleShort();
        $modules = $this->get('orm.manager')->getRepository('extension')
            ->findBy('type = "module" or type = "theme-addon"');
        $themes = $this->get('orm.manager')->getRepository('theme')
            ->findBy('uuid !in ["es.openhost.theme.admin", "es.openhost.theme.manager"]');

        $users = $this->get('orm.manager')->getRepository('User', 'manager')
            ->findBy('order by name asc');

        $users = array_map(function ($a) {
            return [ 'id' => $a->id, 'name' => $a->name ];
        }, $users);

        array_unshift($users, [ 'id' => null, 'name' => _('Select an user...') ]);

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

        $themes = array_map(function (&$a) {
            foreach ([ 'about', 'description', 'name' ] as $key) {
                if (is_array($a->{$key}) && !empty($a->{$key})) {
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
        }, $themes);

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
            'themes'    => $themes,
            'timezones' => \DateTimeZone::listIdentifiers(),
            'modules'   => $modules,
            'users'     => $users
        ];
    }
}
