<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use Framework\Import\Synchronizer\Synchronizer;

class NewsletterSchedulerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('newsletter:scheduler')
            ->setDescription('Sends scheduled newsletters')
            ->setDefinition([
                new InputArgument('instance', InputArgument::REQUIRED, 'The instance internal name.'),
            ])
            ->setHelp(
                <<<EOF
The <info>newsletter:scheduler</info> checks for newsletter schedules and if it is the
right time it sends them.

Put this as a cron action:

<info>php app/console newsletter:scheduler <instance_name></info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO: Remove ASAP
        $this->getContainer()->get('core.security')->setCliUser();

        $loader = $this->getContainer()->get('core.loader');
        $logger = $this->getContainer()->get('logger');

        $instanceName = $input->getArgument('instance');
        $instance     = $loader->loadInstanceFromInternalName($instanceName);

        $loader->init();

        $this->getContainer()->get('core.helper.url_generator')->forceHttp(true);

        $timezone = $this->getContainer()->get('setting_repository')->get('time_zone');
        $this->getContainer()->get('core.locale')->setTimeZone($timezone);
        $this->getContainer()->get('core.security')->setInstance($instance);

        $this->newsletterSender   = $this->getContainer()->get('core.helper.newsletter_sender');
        $this->newsletterService  = $this->getContainer()->get('api.service.newsletter');
        $this->newsletterRenderer = $this->getContainer()->get('core.renderer.newsletter');

        if (!is_object($instance)) {
            throw new \Onm\Exception\InstanceNotFoundException(_('Instance not found'));
        }

        if ($instance->activated != '1') {
            $message = _('Instance not activated');
            throw new \Common\Core\Component\Exception\InstanceNotActivatedException($message);
        }

        $output->writeln(sprintf(
            "Sending scheduled newsletters for instance %s",
            $instance->internal_name
        ));

        $time = new \DateTime(null, new \DateTimeZone('UTC'));

        // Fetch the newsletter template configuration
        $templates = $this->newsletterService->getList(
            'type=1 and status=1 order by created desc'
        );

        foreach ($templates['items'] as $template) {
            $output->writeln(sprintf(" - Newsletter template %s - %s", $template->id, $template->title));

            list($canWeSend, $errors) = $this->canWeSendTemplate($template, $time);

            if ($canWeSend) {
                $output->writeln(sprintf("  + <info>Generating newsletter %s</info>", $template->id));

                $this->sendScheduledTemplate($template, $output, $time);
            } else {
                $output->writeln(sprintf("  + <info>%s</info>", $errors));
            }
        }
    }

    /**
     * Checks if the template was arlready sent or if this is not the right time to send it.
     *
     * @param Newsletter $template the newsletter template we want to check
     * @param DateTime   $time     current time
     *
     * @return array an array with two keys:
     *                  the first with a boolean: true, we can send, false, we cannot
     *                  the second is the reason why we cant send
     **/
    public function canWeSendTemplate($template, $time)
    {
        // Check if it is the right day of the week to send the newsletter
        $todaysDayWeekNumber = (int) $time->format('N');
        if (!in_array($todaysDayWeekNumber, $template->schedule['days'])) {
            return [false, sprintf(
                _('Nothing to execute at current day (current:%d, valid: %s)'),
                $todaysDayWeekNumber,
                implode(', ', $template->schedule['days'])
            )];
        }

        // Check if it is the right hour of the week to send the newsletter
        $newDate = clone $time;
        $newDate->setTimeZone(getService('core.locale')->getTimeZone());
        $currentHour = sprintf('%02d:00', (int) $newDate->format('H'));

        if (!in_array($currentHour, $template->schedule['hours'])) {
            return [
                false,
                sprintf(
                    _('Nothing to execute at current hour (current:%s, valid: %s)'),
                    $currentHour,
                    implode(', ', $template->schedule['hours'])
                )
            ];
        }

        // check that there are no other newsletters sent for this hour and day
        $sentNewsletters = $this->newsletterService->getList(sprintf(
            'type=0 and template_id=%d order by sent desc limit 1',
            $template->id
        ));

        if ($sentNewsletters['total'] == 0) {
            return [ true, '' ];
        }

        $newDate->setTimeZone($sentNewsletters['items'][0]->sent->getTimeZone());

        // We can send the shedule if the last newsletter was sent in the next conditions:
        //  - a year ago
        //  - a day ago
        //  - o in the same day but in different hour.
        $canSend =
            ((int) $newDate->format('y') !== (int) $sentNewsletters['items'][0]->sent->format('y'))
            || ((int) $newDate->format('d') !== (int) $sentNewsletters['items'][0]->sent->format('d'))
            || (
                (int) $newDate->format('d') === (int) $sentNewsletters['items'][0]->sent->format('d')
                && (int) $newDate->format('h') !== (int) $sentNewsletters['items'][0]->sent->format('h')
            );

        $message = 'Go ahead';
        if (!$canSend) {
            $message = sprintf(
                'Unable to send the newsletter, latest sent collissions (id: %s, sent: %s UTC)',
                $sentNewsletters['items'][0]->id,
                $sentNewsletters['items'][0]->sent->format('Y-m-d H:i:s')
            );
        }

        return [$canSend, $message];
    }

    /**
     * Sends a scheduled newsletter and stores the sending information into the database
     *
     * @param Newsletter $template the scheduled newsletter to send,
     * @param Output $output the symfony output object to interact with the cli
     * @param DateTime $time the current DateTime
     *
     * @return Newsletter the created newsletter
     **/
    public function sendScheduledTemplate($template, $output, $time)
    {
        // Render the template
        if ($output->isVerbose()) {
            $output->writeln(sprintf("\t- <info>Rendering newsletter</info>", $template->id));
        }

        $template->title = sprintf('%s [%s]', $template->title, $time->format('d/m/Y'));
        $template->html  = $this->newsletterRenderer->render($template->contents);

        if ($output->isVerbose()) {
            $output->writeln(sprintf("\t- <info>Sending newsletter</info>", $template->id));
        }

        // Send the schedule
        $report = $this->newsletterSender->send($template, $template->recipients);

        if ($output->isVerbose()) {
            $output->writeln(sprintf("\t- <info>Creating newsletter entry</info>", $template->id));
        }
        // Store the newsletter info
        $data = array_merge($template->getStored(), [
            'type'        => 0,
            'title'       => $template->title,
            'html'        => $template->html,
            'recipients'  => $template->recipients,
            'sent'        => new \Datetime(),
            'sent_items'  => $report['total'],
            'updated'     => new \Datetime(),
            'template_id' => $template->id,
        ]);

        unset($data['id']);

        $newItem = $this->newsletterService->createItem($data);

        $output->writeln(sprintf(
            " + <info>Newsletter send and registered (id: %s, sends: %s)",
            $newItem->id,
            $report['total']
        ));

        return $newItem;
    }
}
