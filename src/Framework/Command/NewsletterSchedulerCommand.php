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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewsletterSchedulerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('newsletter:scheduler')
            ->setDescription('Sends scheduled newsletters')
            ->addOption(
                'instances',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'The list of instances to send newsletter from (e.g. norf quux)'
            );
    }

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

        $i = 1;

        foreach ($instances as $instance) {
            $this->getContainer()->get('cache.connection.instance')
                ->init();

            $output->write(sprintf(
                '<fg=blue;options=bold>==></><options=bold> (%s/%s) Processing instance %s </>',
                $i++,
                count($instances),
                $instance->internal_name
            ));

            try {
                $this->getContainer()->get('core.loader')
                    ->load($instance->internal_name);

                $this->getContainer()->get('core.security')->setInstance($instance);

                $this->getContainer()->get('core.helper.url_generator')->forceHttp(true);
                $this->getContainer()->get('core.helper.url_generator')->setInstance($instance);

                // Set base url from instance information to fix url generation
                $context = $this->getContainer()->get('router')->getContext();

                $context->setHost($instance->getMainDomain());

                $context->setScheme(
                    in_array(
                        'es.openhost.module.frontendSsl',
                        $instance->activated_modules
                    ) ? 'https' : 'http'
                );

                $timezone = $this->getContainer()->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('time_zone');

                $this->getContainer()->get('core.locale')->setTimeZone($timezone);

                $this->newsletterSender   = $this->getContainer()->get('core.helper.newsletter_sender');
                $this->newsletterService  = $this->getContainer()->get('api.service.newsletter');
                $this->newsletterRenderer = $this->getContainer()->get('core.renderer.newsletter');

                $this->instanceName = $instance->internal_name;

                $time = new \DateTime(null, $this->getContainer()->get('core.locale')->getTimeZone());

                $templates = $this->newsletterService->getList(
                    'type=1 and status=1 order by created desc'
                );

                $output->writeln(sprintf(
                    '<fg=blue;options=bold>(%s newsletters)</> ',
                    $templates['total']
                ));

                if ($templates['total'] <= 0) {
                    $output->writeln(str_pad(sprintf(
                        '<fg=yellow;options=bold>====></><options=bold> No newsletters available </>'
                    ), 50, '.'));
                    continue;
                }
            } catch (\Exception $e) {
                $output->writeln(sprintf(
                    '<fg=red;options=bold>FAIL</> <fg=blue;options=bold>(%s)</>',
                    $e->getMessage()
                ));
                continue;
            }

            $j = 1;
            foreach ($templates['items'] as $template) {
                $output->writeln(str_pad(sprintf(
                    '<fg=yellow;options=bold>====></><options=bold> (%s/%s) Newsletter %s - %s</>',
                    $j++,
                    count($templates['items']),
                    $template->id,
                    $template->title
                ), 50, '.'));

                try {
                    $output->write(str_pad(' - Newsletter send and registered ', 50, '.'));

                    if ($this->canWeSendTemplate($template, $time)) {
                        $this->sendScheduledTemplate($template, $output, $time);
                    } else {
                        $output->writeln(sprintf(
                            '<fg=yellow;options=bold>SKIP</> <fg=blue;options=bold>(Nothing to send)</>'
                        ));
                    }
                } catch (\Exception $e) {
                    $output->writeln(sprintf(
                        '<fg=red;options=bold>FAIL</> <fg=blue;options=bold>(%s)</>',
                        $e->getMessage()
                    ));
                    continue;
                }
            }
        }

        $this->end();
        $output->writeln(sprintf(
            str_pad('<options=bold>(3/3) Ending command', 50, '.')
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
    protected function canWeSendTemplate($template, $time)
    {
        // Check if it is the right day of the week to send the newsletter
        $todaysDayWeekNumber = (int) $time->format('N');
        if (!in_array($todaysDayWeekNumber, $template->schedule['days'])) {
            return false;
        }

        // Check if it is the right hour of the week to send the newsletter
        $newDate     = clone $time;
        $currentHour = sprintf('%02d:00', (int) $newDate->format('H'));

        if (!in_array($currentHour, $template->schedule['hours'])) {
            return false;
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

        if (!$canSend) {
            return false;
        }

        return true;
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
    protected function sendScheduledTemplate($template, $output, $time)
    {
        // Render the template
        if ($output->isVerbose()) {
            $output->writeln(sprintf("\t- <info>Rendering newsletter</info>", $template->id));
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
            $this->getContainer()->get('core.locale')->setContext('frontend')->apply();
            $newsletter->html = $this->newsletterRenderer->render($newsletter);
            $this->getContainer()->get('core.locale')->setContext('backend')->apply();
            $data             = $newsletter->getData();

            $data = array_merge($data, [
                'html'       => $newsletter->html,
                'sent_items' => $this->newsletterSender->send($newsletter, $newsletter->recipients)['total']
            ]);

            $this->newsletterService->updateItem($newsletter->id, $data);
        } catch (CreateItemException $e) {
            throw new \Exception(sprintf("Error creating newsletter based on template with id: %s", $template->id));
        } catch (UpdateItemException $e) {
            throw new \Exception(sprintf("Error updating newsletter based on template with id: %s", $template->id));
        }

        if ($output->isVerbose()) {
            $output->writeln(sprintf("\t- <info>Sending newsletter</info>", $template->id));
            $output->writeln(sprintf("\t- <info>Creating newsletter entry</info>", $template->id));
        }

        $output->writeln(sprintf(
            '<fg=green;options=bold>DONE</> <fg=blue;options=bold>(id: %s, sends: %s)</>',
            $newsletter->id,
            $data['sent_items']
        ));

        return $newsletter;
    }

    /**
     * Returns the list of active instances.

     * @return array The list of instances.
     */
    protected function getInstances(?array $names = []) : array
    {
        $oql = sprintf(
            'activated = 1 and activated_modules ~ "%s"',
            'es.openhost.module.newsletter_scheduling'
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
