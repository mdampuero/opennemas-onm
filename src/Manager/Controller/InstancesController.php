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
namespace Manager\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Module\ModuleManager as ModuleManager;
use Onm\Framework\Controller\Controller;
use Onm\Instance\Instance;
use Onm\Instance\InstanceManager as im;
use Onm\Instance\InstanceCreator;
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
     * Shows a list of instances
     *
     * @param  Request  $request The request object.
     * @return Response          The response.
     */
    public function listAction(Request $request)
    {
        return $this->render('instances/list.tpl');
    }

    /**
     * Shows the information form of a instance given its id.
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The instance id.
     * @return Response          The response object.
     */
    public function showAction(Request $request, $id)
    {
        $im = $this->get('instance_manager');

        $params = array(
            'available_modules' => ModuleManager::getAvailableModules(),
            'timezones'         => \DateTimeZone::listIdentifiers(),
            'languages'         => array(
                'en_US' => _("English"),
                'es_ES' => _("Spanish"),
                'gl_ES' => _("Galician")
            ),
            'templates'         => im::getAvailableTemplates()
        );

        if ($id) {
            $params['instance'] = $im->find($id);

            if (!$params['instance']) {
                m::add(sprintf(_('Unable to find an instance with the id "%d"'), $id), m::ERROR);
                return $this->redirect($this->generateUrl('manager_instances'));
            }

            $im->getExternalInformation($params['instance']);
        }

        return $this->render('instances/edit.tpl', $params);
    }

    /**
     * Creates a new instance from the request.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function createAction(Request $request)
    {
        $internalName = $request->request->filter('internal_name', null, FILTER_SANITIZE_STRING);
        $domains      = $request->request->filter('domains', null, FILTER_SANITIZE_STRING);

        if (!$domains) {
            m::add('Instance must have one domain.', m::SUCCESS);

            return $this->redirect($this->generateUrl('manager_instances'));
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

            if ($value) {
                $instance->{$key} = $value;
            }
        }

        $instance->created = date('Y-m-d H:i:s');
        $instance->external['activated_modules'] = $request->request
            ->filter('activated_modules', null, FILTER_SANITIZE_STRING);

        $im      = $this->get('instance_manager');
        $creator = new InstanceCreator($im->getConnection());

        $im->checkInternalName($instance);

        try {
            $im->persist($instance);
            $creator->createDatabase($instance->id);
            $creator->copyDefaultAssets($instance->internal_name);

        } catch (DatabaseNotRestoredException $e) {
            $errors[] = $e->getMessage();

            $creator->deleteDatabase($instance->id);
            $im->remove($instance);

        } catch (AssetsNotCopiedException $e) {
            $errors[] = $e->getMessage();

            $creator->deleteAssets($instance->internal_name);
            $creator->deleteDatabase($instance->id);
            $im->remove($instance);
        }


        if (count($errors) > 0) {
            m::add($errors, m::ERROR);
        } else {
            m::add('Instance saved successfully.', m::SUCCESS);
        }

        return $this->redirect($this->generateUrl('manager_instances'));
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
        $sm = $this->get('setting_repository');

        $errors = null;
        try {
            $instance = $im->find($id);

            if (!array_key_exists('activated', $request->request->all())) {
                $request->request->set('activated', 0);
                $_REQUEST['activated'] = 0;
            }

            foreach (array_keys($request->request->all()) as $key) {
                $value = $request->request->filter($key, null, FILTER_SANITIZE_STRING);

                // if ($value) {
                    $instance->{$key} = $value;
                // }
            }

            $instance->external['activated_modules'] = $request->request
                ->filter('activated_modules', null, FILTER_SANITIZE_STRING);

            $im->persist($instance);

            $sm->selectDatabase($instance->getDatabaseName());
            foreach ($instance->external as $key => $value) {
                $sm->set($key, $value);
            }
        } catch (InstanceNotFoundException $e) {
            m::add(sprintf(_('Unable to find an instance with the id "%d"'), $id), m::ERROR);
            return $this->redirect($this->generateUrl('manager_instances'));
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        if (is_array($errors) && count($errors) > 0) {
            m::add($errors, m::ERROR);
        } else {
            m::add('Instance saved successfully.', m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl('manager_instance_show', array('id' => $id))
        );
    }
}
