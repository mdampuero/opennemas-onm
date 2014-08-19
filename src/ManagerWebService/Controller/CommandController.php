<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommandController extends Controller
{
    /**
     * Returns the list of available commands.
     *
     * @return JsonResponse The response object.
     */
    public function listAction()
    {
        $application = $this->getApplication();
        $commands    = $application->all();

        unset($commands['help']);
        unset($commands['list']);
        unset($commands['server:run']);

        foreach ($commands as &$command) {
            $command = array(
                'name'        => $command->getName(),
                'description' => $command->getDescription()
            );
        }

        $instances = glob(APPLICATION_PATH.'/tmp/instances/*');
        foreach ($instances as &$name) {
            $name = basename($name);
        }

        return new JsonResponse(
            array(
                'results'  => $commands,
                'template' => array(
                    'instances' => $instances
                ),
            )
        );
    }

    /**
     * Returns the command executer.
     *
     * @return Application
     */
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
