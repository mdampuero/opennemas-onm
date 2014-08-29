<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Openhost Developers <onm-dev@openhost.es>
 *
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ClearFailedSpoolCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('swiftmailer:spool:clear-failures')
            ->setDescription('Clears failures from the spool')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Grab the real transport from the service container and check it's started
        $transport = $this->getContainer()->get('swiftmailer.transport.real');
        if (!$transport->isStarted()) {
            $transport->start();
        }

        // Find all the spooled *.sending files
        $spoolPath = $this->getContainer()->getParameter('swiftmailer.spool.default.file.path');
        $finder = Finder::create()->in($spoolPath)->name('*.sending');

        foreach ($finder as $failedFile) {
            // Rename the file, so no other process tries to find it
            $tmpFilename = $failedFile.'.finalretry';
            rename($failedFile, $tmpFilename);

            // Unserialize message
            $message = unserialize(file_get_contents($tmpFilename));
            $output->writeln(sprintf(
                'Retrying <info>%s</info> to <info>%s</info>',
                $message->getSubject(),
                implode(', ', array_keys($message->getTo()))
            ));

            // Try send file and delete it
            try {
                $transport->send($message);
                $output->writeln('Sent!');
            } catch (\Swift_TransportException $e) {
                $output->writeln('<error>Send failed - deleting spooled message</error>');
            }

            unlink($tmpFilename);
        }
    }
}
