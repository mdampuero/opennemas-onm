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

use Framework\ORM\Entity\UserNotification;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The orm:migrate:user_notification command migrates UserNotifications from
 * instance to manager.
 */
class MigrateUserNotificationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('orm:migrate:user_notification')
            ->setDescription('Migrates UserNotifications from instance to manager.')
            ->addArgument(
                'instance',
                InputArgument::REQUIRED,
                'Instance internal name.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name     = $input->getArgument('instance');
        $instance = $this->getContainer()->get('instance_manager')->findOneBy([
            'internal_name' => [ [ 'value' => $name ] ]
        ]);

        $instance->boot();

        $output->writeln('<info>Migrating UserNotifications from ' . $name . '</info>');

        $conn = $this->getContainer()->get('db_conn');
        $conn->selectDAtabase($instance->getDatabaseName());

        \Application::load();
        \Application::initDatabase($conn);

        $conn = $this->getContainer()->get('dbal_connection');
        $conn->selectDatabase($instance->getDatabaseName());

        $ids = [];
        $un  = $conn->fetchAll('SELECT * FROM user_notification');

        foreach ($un as $u) {
            $ids[] = $u['user_id'];
        }

        $output->writeln('To migrate: ' . count($un));

        $rs = $this->getContainer()->get('user_repository')->findBy(
            [ 'id' => [ [ 'value' => array_unique($ids), 'operator' => 'in' ] ] ],
            []
        );

        $users = [];
        foreach ($rs as $r) {
            $users[$r->id] = $r;
        }

        $em       = $this->getContainer()->get('orm.manager');
        $migrated = 0;
        foreach ($un as $u) {
            if ($output->isVerbose()) {
                $output->writeln(
                    '==> Migrating UserNotification (notification_id: '
                    . $u['notification_id'] . ', user_id: ' . $u['user_id']
                    . ')'
                );
            }

            try {
                // Migrate only when users from instance (ignore master users)
                if (array_key_exists($u['user_id'], $users)) {
                    $user = $users[$u['user_id']];
                    $user->eraseCredentials();

                    $userNotification = new UserNotification();
                    $userNotification->user_id         = $u['user_id'];
                    $userNotification->read_time       = $u['read_time'];
                    $userNotification->instance_id     = $instance->id;
                    $userNotification->notification_id = $u['notification_id'];
                    $userNotification->user            = $user;

                    $em->persist($userNotification);
                    $migrated++;
                } else {
                    if ($output->isVerbose()) {
                        $output->writeln('<fg=yellow>==> Ignoring UserNotification</>');
                    }

                    if ($output->isVeryVerbose()) {
                        $output->writeln('<fg=red>User from manager</>');
                    }
                }
            } catch (\Exception $e) {
                if ($output->isVerbose()) {
                    $output->writeln('<fg=yellow>==> Ignoring UserNotification</>');
                }

                if ($output->isVeryVerbose()) {
                    $output->writeln('<fg=red>' . $e->getMessage() . '</>');
                }
            }
        }

        $output->writeln("<info>Migrated: " . $migrated . '</>');
    }
}
