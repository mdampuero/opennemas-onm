<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Common\Core\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The SendCommand class defines a command to send mails
 */
class NewsletterSendCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('newsletter:send')
            ->setDescription('Send specific newsletter')
            ->addArgument(
                'instance',
                InputArgument::REQUIRED,
                'The instance internal name'
            )->addArgument(
                'newsletter',
                InputArgument::REQUIRED,
                'The newsletter id'
            )->addArgument(
                'recipients',
                InputArgument::REQUIRED,
                'The mail list to send'
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

        $instance   = $input->getArgument('instance');
        $id         = $input->getArgument('newsletter');
        $recipients = $input->getArgument('recipients');

        $this->getContainer()->get('core.loader')->load($instance);

        $ns = $this->getContainer()->get('api.service.newsletter');

        $newsletter = $ns->getItem($id);

        $this->getContainer()->get('core.helper.newsletter_sender')
            ->send($newsletter, $recipients);

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
