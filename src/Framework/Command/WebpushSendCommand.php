<?php

namespace Framework\Command;

use Common\Core\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addArgument(
                'instance',
                InputArgument::REQUIRED,
                'The instance internal name'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->start();
        $output->writeln(sprintf(
            '(1/2) Starting command...<options=bold></><fg=green;options=bold>DONE</> <fg=blue;options=bold>(%s)</>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $instance = $input->getArgument('instance');
        $this->getContainer()->get('core.loader')->load($instance);
        $this->getContainer()->get('core.locale')->setContext('backend');
        $as           = $this->getContainer()->get('api.service.content');
        $pendingItems = $as->getPendingNotifications();
        $date         = new \DateTime(null, new \DateTimeZone('UTC'));
        $utcDate      = $date->format('Y-m-d H:i:s');

        foreach ($pendingItems as $item) {
            if ($item->starttime->format("Y-m-d H:i:s") <= $utcDate) {
                $webpushr    = $this->getContainer()->get('external.web_push.factory');
                $endpoint    = $webpushr->getEndpoint('notification');
                $contentPath = $this->getContainer()
                    ->get('core.helper.url_generator')->getUrl($item, ['_absolute' => true]);
                $image       = $this->getContainer()
                    ->get('core.helper.featured_media')->getFeaturedMedia($item, 'inner');
                $imagePath   = $this->getContainer()
                    ->get('core.helper.photo')->getPhotoPath($image, null, [], true);

                $notification = $endpoint->sendNotification([
                    'title'      => $item->title,
                    'message'    => $item->description,
                    'target_url' => $contentPath,
                    'image'      => $imagePath
                ]);

                $title                = $item->title;
                $description          = $item->description;
                $changedNotifications = array_map(function ($element) use ($title, $description, $image) {
                    if ($element['status'] == 0) {
                        $element['status']    = 1;
                        $element['body']      = $description;
                        $element['title']     = $title;
                        $element['send_date'] = gmdate("Y-m-d H:i:s");
                        $element['image']     = $image->pk_content;
                    }
                    return $element;
                }, $item->webpush_notifications);

                $as->patchItem($item->pk_content, ['webpush_notifications' => $changedNotifications]);
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
}
