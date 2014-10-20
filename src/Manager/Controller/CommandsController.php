<?php
/**
 * Handles the actions for the frameworks commands
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

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the frameworks commands
 *
 * @package Manager_Controllers
 **/
class CommandsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);
    }

    /**
     * Lists all the available framework commands
     *
     * @return void
     **/
    public function listAction()
    {
        $application = $this->getApplication();
        $commands = $application->all();

        unset($commands['help']);
        unset($commands['list']);
        unset($commands['server:run']);

        $instances = glob(APPLICATION_PATH.'/tmp/instances/*');
        foreach ($instances as &$name) {
            $name = basename($name);
        }

        return $this->render(
            'framework/commands/commands.tpl',
            array(
                'commands'  => $commands,
                'instances' => $instances,
            )
        );
    }

    /**
     * Returns the command executer
     *
     * @return Application
     **/
    private function getApplication()
    {
        if (!isset($this->application)) {
            $application = new Application();

            // Iterate over all the available command classes and register them into the
            // console application
            $availableCommandClases = glob(SRC_PATH.'*/Command/*');
            foreach ($availableCommandClases as $file) {
                $commandClass = str_replace(
                    array(SRC_PATH, '.php', '/'),
                    array('', '', '\\'),
                    $file
                );

                if (class_exists($commandClass)) {
                    $application->add(new $commandClass);
                }
            }
            $application->setAutoExit(false);

            $this->application = $application;
        }

        return $this->application;
    }
}
