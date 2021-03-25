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

use Common\Core\Command\Command;
use Api\Exception\CreateItemException;
use Api\Exception\UpdateItemException;
use Common\Core\Component\Exception\Instance\InstanceNotFoundException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewsletterSchedulerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('newsletter:scheduler')
            ->setDescription('Sends scheduled newsletters')
            ->setDefinition([
                new InputArgument('instance', InputArgument::OPTIONAL, 'The instance internal name.'),
            ])
            ->setHelp(
                <<<EOF
The <info>newsletter:scheduler</info> checks for newsletter schedules and if it is the
right time it sends them.

Put this as a cron action:

<info>php app/console newsletter:scheduler</info>

Use this action if you want to launch the newsletter of a specific instance:

<info>php app/console newsletter:scheduler <instance_name></info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->start();
        $output->writeln(sprintf(
            str_pad('<options=bold>Starting command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $instanceName = $input->getArgument('instance');

        $instaceArray = [];

        if (empty($instanceName)) {
            $instances = $this->getInstances();
            foreach ($instances as $instance) {
                if (in_array('es.openhost.module.newsletter_scheduling', $instance->activated_modules)) {
                    array_push($instaceArray, $instance->getData()['internal_name']);
                }
            }
        } else {
            array_push($instaceArray, $instanceName);
        }

        foreach ($instaceArray as $name) {
            $this->getContainer()->get('core.loader')
                ->load($name)
                ->onlyEnabled()
                ->init();

            $instance = $this->getContainer()->get('core.instance');

            // Set base url from instance information to fix url generation
            $context = $this->getContainer()->get('router')->getContext();
            $context->setHost($instance->getMainDomain());
            $context->setScheme(
                in_array(
                    'es.openhost.module.frontendSsl',
                    $instance->activated_modules
                ) ? 'https' : 'http'
            );

            $this->getContainer()->get('core.helper.url_generator')->forceHttp(true);

            $timezone = $this->getContainer()->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('time_zone');

            $this->getContainer()->get('core.locale')->setTimeZone($timezone);
            $this->getContainer()->get('core.security')->setInstance($instance);

            $this->newsletterSender   = $this->getContainer()->get('core.helper.newsletter_sender');
            $this->newsletterService  = $this->getContainer()->get('api.service.newsletter');
            $this->newsletterRenderer = $this->getContainer()->get('core.renderer.newsletter');

            if (!is_object($instance)) {
                throw new InstanceNotFoundException();
            }

            if ($instance->activated != '1') {
                $message = _('Instance not activated');
                throw new \Common\Core\Component\Exception\InstanceNotActivatedException($message);
            }
            $this->instanceName = $name;

            $this->outputLine(sprintf(
                "Sending scheduled newsletters for instance %s",
                $name
            ));

            $time = new \DateTime(null, $this->getContainer()->get('core.locale')->getTimeZone());

            // Fetch the newsletter template configuration
            $templates = $this->newsletterService->getList(
                'type=1 and status=1 order by created desc'
            );

            if ($templates['total'] <= 0) {
                $this->outputLine(sprintf("  - <info>No templates available</info>"));
            }

            foreach ($templates['items'] as $template) {
                $this->outputLine(sprintf(" - Newsletter template %s - %s", $template->id, $template->title));

                list($canWeSend, $errors) = $this->canWeSendTemplate($template, $time);

                if ($canWeSend) {
                    $this->outputLine(sprintf("  + <info>Generating newsletter %s</info>", $template->id));

                    $this->sendScheduledTemplate($template, $output, $time);
                } else {
                    $this->outputLine(sprintf("  + <info>%s</info>", $errors));
                }
            }
        }

        $this->end();
        $output->writeln(sprintf(
            str_pad('<options=bold>Ending command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));
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
     */
    public function canWeSendTemplate($template, $time)
    {
        // Check if it is the right day of the week to send the newsletter
        $todaysDayWeekNumber = (int) $time->format('N');
        if (!in_array($todaysDayWeekNumber, $template->schedule['days'])) {
            return [false, sprintf(
                'Nothing to execute at current day (current:%d, valid: %s)',
                $todaysDayWeekNumber,
                implode(', ', $template->schedule['days'])
            )];
        }

        // Check if it is the right hour of the week to send the newsletter
        $newDate     = clone $time;
        $currentHour = sprintf('%02d:00', (int) $newDate->format('H'));

        if (!in_array($currentHour, $template->schedule['hours'])) {
            return [
                false,
                sprintf(
                    'Nothing to execute at current hour (current:%s, valid: %s)',
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
     */
    public function sendScheduledTemplate($template, $output, $time)
    {
        // Render the template
        if ($output->isVerbose()) {
            $this->outputLine(sprintf("\t- <info>Rendering newsletter</info>", $template->id));
        }

        $data = array_merge($template->getStored(), [
            'type'        => 0,
            'title'       => sprintf('%s [%s]', $template->title, $time->format('d/m/Y')),
            'recipients'  => $template->recipients,
            'sent'        => new \Datetime(null, new \DateTimeZone('UTC')),
            'template_id' => $template->id,
        ]);

        unset($data['id']);

        try {
            $newsletter       = $this->newsletterService->createItem($data);
            $newsletter->html = $this->newsletterRenderer->render($newsletter);
            $data             = $newsletter->getData();

            $data = array_merge($data, [
                'html'       => $newsletter->html,
                'sent_items' => $this->newsletterSender->send($newsletter, $newsletter->recipients)['total']
            ]);

            $this->newsletterService->updateItem($newsletter->id, $data);
        } catch (CreateItemException $e) {
            $this->outputLine(
                sprintf(
                    "<error>Error creating newsletter based on template with id: %s</error>",
                    $template->id
                )
            );
        } catch (UpdateItemException $e) {
            $this->outputLine(
                sprintf(
                    "<error>Error updating newsletter based on template with id: %s</error>",
                    $template->id
                )
            );
        }

        if ($output->isVerbose()) {
            $this->outputLine(sprintf("\t- <info>Sending newsletter</info>", $template->id));
            $this->outputLine(sprintf("\t- <info>Creating newsletter entry</info>", $template->id));
        }

        $this->outputLine(sprintf(
            " + <info>Newsletter send and registered (id: %s, sends: %s)",
            $newsletter->id,
            $data['sent_items']
        ));

        return $newsletter;
    }

    /**
     * Writes the provided line into the output
     *
     * @param string $line The line to write in the output
     */
    private function outputLine($line)
    {
        $fullLine = sprintf('<options=bold>[Instance %s] <fg=green>%s</></>', $this->instanceName, $line);

        $this->output->writeln($fullLine);
    }


    /**
     * Returns the list of active instances.

     * @return array The list of instances.
     */
    protected function getInstances() : array
    {
        $oql = 'activated = 1';

        return $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy($oql);
    }
}
