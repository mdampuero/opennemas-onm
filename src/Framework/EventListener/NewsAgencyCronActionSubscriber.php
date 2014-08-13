<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Onm\Settings as s;

/**
 * Handles all the events after content updates.
 */
class NewsAgencyCronActionSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return array(
            'cron.actions' => array(
                array('updateNewsAgency', 5),
            ),
        );
    }

    /**
     * Synchronizes all instances basing on news agencies configuration.
     *
     * @param Event $event The event to handle.
     */
    public function updateNewsAgency(Event $event)
    {
        $output = $event->output;
        $input  = $event->input;
        $sc     = $event->container;

        $output->writeln(' - Executing news agency actions');

        $_SERVER['SERVER_NAME'] = 'cron';
        define('CACHE_PREFIX', 'cron');

        require APPLICATION_PATH.'/config/config.inc.php';

        $instancesConnection = \ADONewConnection('mysql');
        $instancesConnection->Connect(
            $onmInstancesConnection['BD_HOST'],
            $onmInstancesConnection['BD_USER'],
            $onmInstancesConnection['BD_PASS'],
            $onmInstancesConnection['BD_DATABASE']
        );

        $instancesConnection->bulkBind = true;
        $instancesConnection->SetFetchMode(ADODB_FETCH_ASSOC);

        $rs = $instancesConnection->Execute('SELECT * FROM instances');
        $instances = $rs->GetArray();

        foreach ($instances as $instanceData) {

            $instance = new \Onm\Instance\Instance();
            foreach ($instanceData as $key => $value) {
                $instance->{$key} = $value;
            }
            $instance->settings = unserialize($instance->settings);

            $sc->setParameter('instance', $instance);
            $sc->setParameter('cache_prefix', $instance->internal_name);

            $GLOBALS['application'] = new \stdClass();
            $GLOBALS['application']->conn = \ADONewConnection('mysql');
            $GLOBALS['application']->conn->Connect(
                $onmInstancesConnection['BD_HOST'],
                $onmInstancesConnection['BD_USER'],
                $onmInstancesConnection['BD_PASS'],
                $instance->settings['BD_DATABASE']
            );
            $GLOBALS['application']->conn->bulkBind = true;
            $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);

            $settings = $GLOBALS['application']->conn->GetOne(
                'SELECT value FROM settings WHERE `name`="news_agency_config"'
            );
            $servers = @unserialize($settings);

            if (is_array($servers)) {
                $output->writeln("  . Synchying news for instance ".$instance->name);

                $syncparams = array(
                    'cache_path' => APPLICATION_PATH.DS.'tmp'.DS.'instances'.DS.$instance->internal_name
                );

                $synchronizer = new \Onm\Import\Synchronizer\Synchronizer($syncparams);

                try {
                    $messages = $synchronizer->syncMultiple($servers);
                    foreach ($messages as $message) {
                        $output->writeln("\t ".$message);
                    }
                } catch (\Exception $e) {
                    $output->writeln("\t<fg=White;bg=red>Error synchying: ".$e->getMessage()."</fg=white;bg=red>");
                }
            }
        }

        return false;
    }
}
