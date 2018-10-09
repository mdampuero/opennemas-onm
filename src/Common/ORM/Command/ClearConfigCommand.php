<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The ClearConfigCommand class defines a command to load the ORM configuration.
 */
class ClearConfigCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('orm:config:clear')
            ->setDescription('Clears the ORM configuration from cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cache = $this->getContainer()->get('cache.manager')
            ->getConnection('internal');

        try {
            $cache->remove('orm_' . DEPLOYED_AT);
            $output->writeln('<info>[OK]</>   ORM configuration deleted from cache');
        } catch (\Exception $e) {
            $output->writeln('<fg=red>[FAIL]</> Unable to delete the ORM configuration from cache');
        }
    }
}
