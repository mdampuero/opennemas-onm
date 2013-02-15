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
 * @package Backend_Controllers
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
     * Description of the action
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
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
     * Executes a particular command given its name
     *
     * @return void
     **/
    public function executeCommandAction(Request $request)
    {
        $commandName = $request->query->filter('command', null, FILTER_SANITIZE_STRING);
        $params = $request->query->get('params', null, FILTER_SANITIZE_STRING);

        if (is_array($params)) {
            foreach ($params as &$param) {
                $param = filter_var($param, FILTER_SANITIZE_STRING);
            }
            $params = implode(' ', $params);
        } else {
            $params = '';
        }

        chdir(APPLICATION_PATH);

        $output = shell_exec('bin/console '.$commandName.' ' .$params.' 2>&1');

        $application = $this->getApplication();
        try {
            $command = $application->find($commandName);
        } catch (\InvalidArgumentException $e) {
            $output = 'Command not valid';
        }

        // $arguments = array(
        //     'command' => $commandName,
        //     // 'name'    => 'Fabien',
        //     // '--yell'  => true,
        // );

        // $input = new ArrayInput($arguments);
        // $returnCode = $command->run($input, $output);

        return $this->render(
            'framework/commands/execute.tpl',
            array(
                'output' => $output,
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
            $basePath = APPLICATION_PATH.'/app/';
            $availableCommandClases = glob($basePath.'*/Command/*');
            foreach ($availableCommandClases as $file) {
                $commandClass = str_replace(
                    array($basePath, '.php', '/'),
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
