<?php

namespace Framework\Command;

use Common\Core\Command\Command;
use DateTimeZone;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The SendCommand class defines a command to send webpush notifications
 */
class WebpushSendCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('webpush:send')
            ->setDescription('Sends pending webpush notifications')
            ->addOption(
                'instances',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'The list of instances to send newsletter from (e.g. norf quux)'
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

            $redis = $this->getContainer()->get('cache.connection.instance');
            $redis->init();

            $output->write(sprintf(
                '<fg=blue;options=bold>==></><options=bold> (%s/%s) Processing instance %s </>',
                $iteration++,
                count($instances),
                $instance->internal_name
            ));
            try {
                $this->getContainer()->get('core.loader')
                    ->load($instance->internal_name);

                $as                   = $this->getContainer()->get('api.service.content');
                $webpushr             = $this->getContainer()->get('external.web_push.factory');
                $notificationEndpoint = $webpushr->getEndpoint('notification');
                $articleService       = $this->getContainer()->get('api.service.article');
                $notificationService  = $this->getContainer()->get('api.service.webpush_notifications');
                $onCooldown           = false;
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

                $timezone = $this->getContainer()->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('time_zone');

                $this->getContainer()->get('core.locale')->setTimeZone($timezone);
                $photoHelper = $this->getContainer()->get('core.helper.photo');

                $favicoId = $this->getContainer()->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('logo_favico');

                $favico = $photoHelper->getPhotoPath(
                    $as->getItem($favicoId),
                    null,
                    [ 192, 192 ],
                    true
                );

                $delay = $this->getContainer()->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('webpush_delay', '1');

                $restrictedHours = $this->getContainer()->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('webpush_restricted_hours', []);

                $this->getContainer()->get('core.security')->setInstance($instance);
                $context = $this->getContainer()->get('core.locale')->getContext();
                $this->getContainer()->get('core.locale')->setContext('backend');
                // Get current timeZone
                $timeZone = $this->getContainer()->get('core.locale')->getTimeZone();
                // Create current local time
                $currentLocalTime = new \DateTime(null, $timeZone);

                if (in_array($currentLocalTime->format('H:00'), $restrictedHours)) {
                    $diff = $currentLocalTime->diff(
                        new \DateTime($currentLocalTime->modify('next hour')->format('Y-m-d H:00:00'), $timeZone)
                    );
                    $currentLocalTime->add($diff);
                    $onCooldown = true;
                } else {
                    // Add delay time in order to handle delayed notifications
                    $currentLocalTime->add(new \DateInterval('PT' . $delay . 'M'));
                }

                $delayedUtcTime = $currentLocalTime->setTimezone(new \DateTimeZone('UTC'));
                $notifications  = $notificationService
                    ->setCount(false)
                    ->getList(sprintf(
                        "status = 0 and send_date <= '%s'",
                        $delayedUtcTime->format('Y-m-d H:i:s')
                    ))['items'];

                foreach ($notifications as $notification) {
                    if ($onCooldown) {
                        $notificationService->patchItem(
                            $notification->id,
                            ['send_date' => $delayedUtcTime->format('Y-m-d H:i:s')]
                        );
                        $redis->remove($notification->fk_content);
                        continue;
                    }
                    $article  = $articleService->getItem($notification->fk_content);
                    $localNow = new \DateTime(null, $timeZone);
                    $localUTC = $localNow->setTimezone(new \DateTimeZone('UTC'));
                    if ($this->getContainer()->get('core.helper.content')->isReadyForPublish($article)
                        && $notification->send_date <= $localUTC) {
                        $contentPath = $this->getContainer()
                            ->get('core.helper.url_generator')->getUrl($article, ['_absolute' => true]);
                        $image       = $this->getContainer()
                            ->get('core.helper.featured_media')->getFeaturedMedia($article, 'inner');
                        $imagePath   = $photoHelper->getPhotoPath($image, null, [], true);

                        $notificationStatus = 1;

                        try {
                            $sentNotification = $notificationEndpoint->sendNotification([
                                'title'      => $article->title ?? '',
                                'message'    => $article->description ?? '',
                                'target_url' => $contentPath,
                                'image'      => $imagePath,
                                'icon'       => strpos($favico, '.png') ? $favico : '',
                            ]);
                        } catch (\Exception $e) {
                            $notificationStatus = 2;
                        }
                        $notificationService->patchItem(
                            $notification->id,
                            [
                                'status'         => $notificationStatus,
                                'body'           => $article->description ?? '',
                                'title'          => $article->title ?? '',
                                'send_date'      => gmdate('Y-m-d H:i:s'),
                                'image'          => $image->pk_content ?? null,
                                'transaction_id' => $sentNotification['ID'] ?? '',
                            ]
                        );

                        $redis->remove('content-' . $notification->fk_content);

                        $onCooldown = true;
                    }
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
}
