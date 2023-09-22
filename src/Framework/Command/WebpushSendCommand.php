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

                $this->getContainer()->get('core.security')->setInstance($instance);
                $context = $this->getContainer()->get('core.locale')->getContext();
                $this->getContainer()->get('core.locale')->setContext('backend');
                $as           = $this->getContainer()->get('api.service.content');
                $pendingItems = $as->getPendingNotifications();
                $timeZone     = $this->getContainer()->get('core.locale')->getTimeZone();
                $date         = new \DateTime(null, new \DateTimeZone('UTC'));
                $utcDate      = $date->format('Y-m-d H:i:s');

                foreach ($pendingItems as $item) {
                    $itemDate          = new \DateTime($item->starttime->format('Y-m-d H:i:s'), $timeZone);
                    $utcItemDate       = $itemDate->setTimezone(new \DateTimeZone('UTC'));
                    $utcItemDateFormat = $utcItemDate->format('Y-m-d H:i:s');
                    if ($utcItemDateFormat > $utcDate) {
                        continue;
                    }
                    $webpushr    = $this->getContainer()->get('external.web_push.factory');
                    $endpoint    = $webpushr->getEndpoint('notification');
                    $contentPath = $this->getContainer()
                        ->get('core.helper.url_generator')->getUrl($item, ['_absolute' => true]);
                    $image       = $this->getContainer()
                        ->get('core.helper.featured_media')->getFeaturedMedia($item, 'inner');
                    $imagePath   = $this->getContainer()
                        ->get('core.helper.photo')->getPhotoPath($image, null, [], true);

                    $notificationStatus = 1;
                    try {
                        $endpoint->sendNotification([
                            'title'      => $item->title,
                            'message'    => $item->description,
                            'target_url' => $contentPath,
                            'image'      => $imagePath
                        ]);
                    } catch (\Exception $e) {
                        $notificationStatus = 2;
                    }

                    $title                = $item->title;
                    $description          = $item->description;
                    $changedNotifications = array_map(function ($element) use (
                        $title,
                        $description,
                        $image,
                        $notificationStatus
                    ) {
                        if ($element['status'] == 0) {
                            $element['status']    = $notificationStatus;
                            $element['body']      = $description;
                            $element['title']     = $title;
                            $element['send_date'] = gmdate("Y-m-d H:i:s");
                            $element['image']     = $image->pk_content ?? '';
                        }
                        return $element;
                    }, $item->webpush_notifications);
                    $as->patchItem($item->pk_content, ['webpush_notifications' => $changedNotifications]);
                }
                $this->getContainer()->get('core.locale')->setContext($context);
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
