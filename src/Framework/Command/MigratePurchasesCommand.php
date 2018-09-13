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

class MigratePurchasesCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('migrate:purchases')
            ->setDescription('Migrate purchases.');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn    = $this->getContainer()->get('dbal_connection');
        $sql     = 'select id, client from purchase';
        $rs      = $conn->fetchAll($sql);
        $updated = 0;
        $errors  = 0;

        foreach ($rs as $value) {
            if (!empty($value['client']
                && strrpos($value['client'], "s:34:\"\x00Framework"))
            ) {
                $client = substr($value['client'], strpos($value['client'], 'a:'));
                $client = substr($client, 0, strrpos($client, "s:34:\"\x00Framework"));

                try {
                    $conn->update('purchase', [ 'client' => $client ], [ 'id' => $value['id'] ]);
                    $updated++;
                } catch (\Exception $e) {
                    $errors++;
                    error_log($e->getMessage());
                }
            }
        }

        $output->writeln(sprintf('<info>[OK]</info>   %s purchases updated successfully', $updated));

        if ($errors > 0) {
            $output->writeln(sprintf('<fg=red>[FAIL]</fg> There were %s errors while updating. '
                . 'For more information, check error log.', $updated));
        }
    }
}
