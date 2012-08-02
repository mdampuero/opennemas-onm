<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controllers;

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
 * @package Backend_Controllers
 * @author
 **/
class InstancesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        global $onmInstancesConnection;
        $this->instanceManager = new im($onmInstancesConnection);
        $this->view = new \TemplateManager(TEMPLATE_ADMIN);
    }

    /**
     * Description of the action
     *
     * @return void
     **/
    public function listAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);
        $findParams = array(
            'name' => $request->query->filter('filter_name', '*', FILTER_SANITIZE_STRING),
            'per_page' => $request->query->filter('filter_per_page', 20, FILTER_SANITIZE_STRING),
        );

        $instances = $this->instanceManager->findAll($findParams);

        foreach ($instances as &$instance) {
            list($instance->totals, $instance->configs) =
                $this->instanceManager->getDBInformation($instance->settings);

            $instance->domains = preg_split("@, @", $instance->domains);
        }

        $itemsPerPage =  $findParams['per_page'];

        // Pager
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($instances),
        );
        $pager = \Pager::factory($pager_options);

        $instances = array_slice($instances, ($page-1)*$itemsPerPage, $itemsPerPage);

        return $this->render('instances/list.tpl', array(
            'instances'     => $instances,
            'per_page'      => $itemsPerPage,
            'pagination'    =>  $pager,
        ));
    }

    /**
     * Shows the information form of a instance given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $templates = im::getAvailableTemplates();

        $instance = $this->instanceManager->read($id);

        if ($instance === false) {
            m::add(sprintf(_('Unable to find an instance with the id "%d"'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('manager_instances'));
        }

        list($instance->totals, $instance->configs) =
            $this->instanceManager->getDBInformation($instance->settings);

        return $this->render('instances/edit.tpl', array(
            'configs'           => $instance->configs,
            'available_modules' => ModuleManager::getAvailableModules(),
            'timezones'         => \DateTimeZone::listIdentifiers(),
            'languages'         => array('en_US' => _("English"), 'es_ES' => _("Spanish"), 'gl_ES' => _("Galician")),
            'logLevels'         => array('normal' => _('Normal'), 'verbose' => _('Verbose'), 'all' => _('All (Paranoic mode)') ),
            'instance'          => $instance,
            'templates'         => $templates,
        ));
    }

    /**
     * Handles the form for create a new instance
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            m::add(sprintf(_('Instance create action not implemented'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('manager_instances'));
        } else {
            $templates = im::getAvailableTemplates();

            return $this->render('instances/edit.tpl', array(
                'configs' => array( 'activated_modules' => ModuleManager::getAvailableModules()),
                'available_modules' => ModuleManager::getAvailableModules(),
                'timezones' => \DateTimeZone::listIdentifiers(),
                'languages' => array('en_US' => _("English"), 'es_ES' => _("Spanish"), 'gl_ES' => _("Galician")),
                'logLevels' => array('normal' => _('Normal'), 'verbose' => _('Verbose'), 'all' => _('All (Paranoic mode)') ),
                'templates' => $templates,
            ));
        }
    }

    /**
     * Updates the instance information gives its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        m::add(sprintf(_('Instance update action not implemented, %d'), $id), m::ERROR);

        return $this->redirect($this->generateUrl('manager_instances'));

    }

    /**
     * Deletes an instance given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        if (!empty($id)) {
            if ($deletion = $this->instanceManager->delete($id)) {
                m::add(_("Instance deleted successfully."), m::SUCCESS);
            } else {
                m::add(_("Unable to delete the instance."), m::ERROR);
            }

        } else {
            m::add(_('You must provide an id for delete an instance.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('manager_instances'));
        }
    }

    /**
     * Toggle the availability of an instance given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $id = $request->query->getDigits('id');
        $instance = $this->instanceManager->read($id);

        if ($instance === false) {
            m::add(sprintf(_('Unable to find the instance with the id %d'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('manager_instances'));
        }

        $this->instanceManager->changeActivated(
            $instance->id,
            ($instance->activated+1) % 2
        );

        return $this->redirect($this->generateUrl('manager_instances'));
    }

} // END class InstancesController
