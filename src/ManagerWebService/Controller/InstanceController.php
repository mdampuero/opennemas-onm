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
                $old = $instance->activated;
                $instance->merge($data);
                $updated++;

                if ($old !== $instance->activated) {
                    dispatchEventWithParams(
                        'instance.update',
                        [ 'instance' => $instance->internal_name ]
                    );
                }
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error', 409);
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
     */
    public function saveAction(Request $request)
    {
        $em       = $this->get('orm.manager');
        $msg      = $this->get('core.messenger');
        $settings = $request->request->get('settings');
        $data     = $em->getConverter('Instance')
            ->objectify($request->request->get('core.instance'));

        $instance = new Instance($data);
        $creator  = new InstanceCreator($em->getConnection('manager'));

        $instance->created = new \DateTime('now');

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
     */
    public function showAction($id)
    {
        $em        = $this->get('orm.manager');
        $instance  = $em->getRepository('Instance')->find($id);
        $converter = $em->getConverter('Instance');
        $ds        = $em->getDataSet('Settings', 'instance');

        $instance->settings['TEMPLATE_USER'] = 'es.openhost.theme.'
            . str_replace('es.openhost.theme.', '', $instance->settings['TEMPLATE_USER']);

        $em->getConnection('instance')
            ->selectDatabase($instance->getDatabaseName());

        $settings = $ds->get([ 'max_mailing', 'pass_level', 'piwik' ]);
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
     */
    public function updateAction(Request $request, $id)
    {
        $em       = $this->get('orm.manager');
        $msg      = $this->get('core.messenger');
        $settings = $request->request->get('settings');
        $data     = $em->getConverter('Instance')
            ->objectify($request->request->get('core.instance'));

        $instance   = $em->getRepository('Instance')->find($id);
        $oldDomains = $instance->domains;

        $instance->setData($data);

        $deletedDomains = array_diff($oldDomains, $instance->domains);

        if (!empty($deletedDomains)) {
            $cache = $this->get('cache.manager')->getConnection('manager');

            foreach ($deletedDomains as $domain) {
                $cache->delete($domain);
            }
        }

        $em->persist($instance);

        // Update settings for instance
        $em->getConnection('instance')
            ->selectDatabase($instance->getDatabaseName());
        $em->getDataSet('Settings', 'instance')->set($settings);

        dispatchEventWithParams(
            'instance.update',
            [ 'instance' => $instance->internal_name ]
        );

        $msg->add(_('Instance saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
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
            ->findBy('type = "module" or type = "theme-addon" limit 10');
        $themes = $this->get('orm.manager')->getRepository('theme')
            ->findBy();

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
            //'templates' => im::getAvailableTemplates(),
            'themes'    => $themes,
            'timezones' => \DateTimeZone::listIdentifiers(),
            'modules'   => $modules,
        ];
    }
}
