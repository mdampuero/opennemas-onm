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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateUserGroupsCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('migrate:user-groups')
            ->setDescription('Migrate user groups.')
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'The database name where execute the script'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = $input->getArgument('database');
        $conn     = $this->getContainer()->get('dbal_connection');
        $logger   = $this->getContainer()->get('error.log');

        $conn->selectDatabase($database);

        $fix         = "select pk_user_group from user_groups";
        $select      = "select id, fk_user_group from users where fk_user_group is not null";
        $count       = "select count(*) as total from users where fk_user_group is not null";
        $validGroups = $conn->fetchAll($fix);
        $total       = $conn->fetchAssoc($count);
        $items       = $conn->fetchAll($select);
        $errors      = 0;
        $progress    = new ProgressBar($output, $total);

        $validGroups = array_map(function ($a) {
            return $a['pk_user_group'];
        }, $validGroups);

        $output->writeln("<options=bold>Items to migrate: {$total['total']}</>");

        foreach ($items as $item) {
            $userId = $item['id'];
            $groups = array_filter(
                explode(',', $item['fk_user_group']),
                function ($a) {
                    return !empty($a);
                }
            );

            foreach ($groups as $group) {
                if (!in_array($group, $validGroups)) {
                    continue;
                }

                try {
                    $conn->insert('user_user_group', [
                        'user_id'       => $userId,
                        'user_group_id' => $group,
                        'status'        => 1,
                        'expires'       => null
                    ]);
                } catch (\Exception $e) {
                    $errors++;
                    $logger->error($e->getMessage());
                }
            }

            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');

        if ($errors > 0) {
            $output->writeln('<fg=red;options=bold>Migration failed!</>');
            $output->writeln('<options=bold>For more information, check the log.</>');
            return;
        }

        $output->writeln('<fg=green;options=bold>Migration completed!</>');
    }
}
