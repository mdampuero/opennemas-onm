<?php

namespace Framework\Command;

use Common\Core\Command\Command;
use DateTimeZone;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The UpdateCommand class defines a command to update the notifications and active subscribers
 */
class WebpushUpdateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('webpush:update')
            ->setDescription('Updates webpush notifications and active subscribers')
            ->addOption(
                'instances',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'The list of instances to update notifications and subscribers from (e.g. norf quux)'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->start();
        $output->writeln(sprintf(
            str_pad('<options=bold>(1/3) Starting command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $instances = $this->getInstances($input->getOption('instances'));
        $output->writeln(sprintf(
            str_pad('<options=bold>(2/3) Processing instances', 43, '.')
                . '<fg=yellow;options=bold>IN PROGRESS</> '
                . '<fg=blue;options=bold>(%s instances)</></>',
            count($instances)
        ));

        $iteration = 1;
        foreach ($instances as $instance) {
            if ($instance->hasMultilanguage()) {
                continue;
            }

            $this->getContainer()->get('cache.connection.instance')->init();
            $output->write(sprintf(
                '<fg=blue;options=bold>==></><options=bold> (%s/%s) Processing instance %s </>',
                $iteration++,
                count($instances),
                $instance->internal_name
            ));
            try {
                $this->getContainer()->get('core.loader')
                    ->load($instance->internal_name);

                $service = $this->getContainer()->get('orm.manager')
                    ->getDataSet('Settings', 'instance')->get('webpush_service');

                if (empty($service)) {
                    $output->writeln(sprintf(
                        '<fg=red;options=bold>SERVICE NOT FOUND</> <fg=blue;options=bold>(Instance %s skipped)</>',
                        $instance->internal_name
                    ));
                    continue;
                }

                $webpush               = $this->getContainer()->get(sprintf('external.web_push.factory.%s', $service));
                $webpushHelper         = $this->getContainer()->get(sprintf('core.helper.%s', $service));
                $notificationsEndpoint = $webpush->getEndpoint('status');
                $subscribersEndpoint   = $webpush->getEndpoint('subscriber');
                $notificationService   = $this->getContainer()->get('api.service.webpush_notifications');
                $this->getContainer()->get('core.security')->setInstance($instance);
                if ($instance->hasMultilanguage()) {
                    continue;
                }

                $this->getContainer()->get('core.helper.url_generator')
                    ->forceHttp(false)
                    ->setInstance($instance);

                // Set base url from instance information to fix url generation
                $routerContext = $this->getContainer()->get('router')->getContext();

                $routerContext->setHost($instance->getMainDomain());
                $routerContext->setScheme(
                    in_array(
                        'es.openhost.module.frontendSsl',
                        $instance->activated_modules
                    ) ? 'https' : 'http'
                );

                $this->getContainer()->get('core.security')->setInstance($instance);
                $this->getContainer()->get('core.locale')->setContext('backend');

                // Get current timeZone
                $timeZone = $this->getContainer()->get('core.locale')->getTimeZone();
                // Create current local time
                $currentLocalTime = new \DateTime(null, $timeZone);
                // Get the delayed utc time needed for notifications (last 2 days)
                $delayedUtcTime = $currentLocalTime->setTimezone(new \DateTimeZone('UTC'));
                $delayedUtcTime->modify('-2 days');

                // Get the list of notifications to update (sent and within the last 2 days)
                $notifications = $notificationService
                    ->setCount(false)
                    ->getList(sprintf(
                        "status = 1 and send_date >= '%s' and transaction_id !is null",
                        $delayedUtcTime->format('Y-m-d H:i:s')
                    ))['items'];

                foreach ($notifications as $notification) {
                    $this->processNotification(
                        $notification,
                        $notificationsEndpoint,
                        $notificationService,
                        $output,
                        $webpushHelper
                    );
                }

                // If current time is 00:00 save active subcsribers in instance settings (once a day, 32/instance max)
                if (!($delayedUtcTime->format('H:i') >= '00:00' && $delayedUtcTime->format('H:i') < '00:05')) {
                    continue;
                }

                try {
                    $oldActiveSubscribers = $this->getContainer()->get('orm.manager')
                        ->getDataSet('Settings', 'instance')
                        ->get('webpush_active_subscribers');

                    $subscriberData = $subscribersEndpoint->getSubscribers(
                        $webpushHelper->prepareDataForEndpoint('subscriber')
                    );

                    $incomingActiveSubscribers = array_key_exists('active_subscribers', $subscriberData)
                        ? $subscriberData['active_subscribers']
                        : $subscriberData['total_subscribers'];

                    if ($oldActiveSubscribers) {
                        if (count($oldActiveSubscribers) == 32) {
                            array_pop($oldActiveSubscribers);
                        }

                        $oldActiveSubscribers = is_array($oldActiveSubscribers) ?
                            $oldActiveSubscribers :
                            [ $oldActiveSubscribers ];

                        array_unshift($oldActiveSubscribers, $incomingActiveSubscribers);

                        $newActiveSubscribers = [
                            'webpush_active_subscribers' => $oldActiveSubscribers
                        ];
                    } else {
                        $newActiveSubscribers = [
                            'webpush_active_subscribers' => [ $incomingActiveSubscribers ]
                        ];
                    }

                    $this->getContainer()->get('orm.manager')->getDataSet('Settings')->set($newActiveSubscribers);
                    usleep(1100000);
                } catch (\Exception $e) {
                    $this->getContainer()->get('error.log')->error($e->getMessage());
                    $this->writeStatus('error', sprintf('FAIL (%s)', $e->getMessage()), true);
                    usleep(1100000);
                }
            } catch (\Exception $e) {
                $output->writeln(sprintf(
                    '<fg=red;options=bold>FAIL</> <fg=blue;options=bold>(%s)</>',
                    $e->getMessage()
                ));
                continue;
            }
        }

        $this->end();
        $output->writeln(sprintf(
            str_pad('<options=bold>(2/2) Ending command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));
    }

    /**
     * Returns the list of active instances.
     *
     * @return array The list of instances.
     */
    protected function getInstances(?array $names = []) : array
    {
        $oql = sprintf(
            'activated = 1 and activated_modules ~ "%s"',
            'es.openhost.module.webpush_notifications'
        );
        if (!empty($names)) {
            $oql .= sprintf(
                ' and internal_name in ["%s"]',
                implode('","', $names)
            );
        }

        return $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy($oql);
    }

    /**
     * Updates the notifications data
     *
     * @return array The list of instances.
     */
    protected function processNotification($notification, $endpoint, $notificationService, $output, $helper)
    {
        if (!$helper->isValidId($notification->transaction_id)) {
            $output->writeln(sprintf(
                '<fg=red;options=bold>TRANSACTION ID IS NOT VALID</> <fg=blue;options=bold>(%s)</>',
                $notification->transaction_id
            ));
            return;
        }

        try {
            $notificationData = $endpoint->getStatus([ 'id' => $notification->transaction_id ]);
            $parsedData       = $helper->parseNotificationData($notificationData);
        } catch (\Exception $e) {
            $output->writeln(sprintf(
                '<fg=red;options=bold>FAIL</> <fg=blue;options=bold>(%s)</>',
                $e->getMessage()
            ));
            usleep(1100000);
            return;
        }

        $redis = $this->getContainer()->get('cache.connection.instance');
        $redis->init();

        $notificationService->patchItem($notification->id, $parsedData);
        $redis->remove($notification->fk_content);

        // Stop the execution for 1.1 seconds
        usleep(1100000);
    }
}
