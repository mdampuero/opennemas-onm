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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Yaml\Yaml;

use Common\ORM\Entity\Client;

class MigrateTagsCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('migrate:tags')
            ->setDescription('Replace accents in tags')
            ->addArgument(
                'instance',
                InputArgument::REQUIRED,
                'What instance do you want to update'
            )->addOption(
                'preview',
                'p',
                InputOption::VALUE_NONE,
                'If set, the command will preview (not execute) the migration'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instance = $input->getArgument('instance');
        $preview  = $input->getOption('preview');
        $loader   = $this->getContainer()->get('core.loader');

        $loader->loadInstanceFromInternalName($instance);

        $output->write('Migrating instance <fg=blue>' . $instance . '</>...');

        $conn    = $this->getContainer()->get('orm.manager')->getConnection('instance');
        $cache   = $this->getContainer()->get('cache.manager')->getConnection('instance');

        $sql = 'select pk_content, content_type_name, metadata'
            . ' from contents'
            . ' where metadata regexp "(á|à|ã|ä|â|Á|À|Ã|Ä|Â|é|è|ë|ê|É|È|Ë|Ê|í|ì|ï|î|Í|Ì|Ï|Î|ó|ò|õ|ö|ô|Ó|Ò|Õ|Ö|Ô|ú|ù|ü|û|Ú|Ù|Ü|Û)+"';
        $rs  = $conn->fetchAll($sql);

        //if (count($rs) === 0) {
            //if ($output->isVerbose()) {
                //$output->writeln("\n<info>Hurrah! No contents to migrate</>");
                //return;
            //}

            //$output->writeln(' <info>DONE</>');
            //return;
        //}

        $output->write("\nMigrating <info>" . count($rs) . '</> contents...');

        if ($output->isVerbose()) {
            $output->write("\n");
        }

        $updated = 0;
        $errors  = 0;

        foreach ($rs as $r) {
            if ($output->isVerbose()) {
                $output->writeln("  - Migrating <info>{$r['content_type_name']}</><fg=magenta>({$r['pk_content']})</>...");
            }

            if ($output->isVeryVerbose()) {
                $output->writeln("    <fg=red>Before</>: {$r['metadata']}");
            }

            $metadata = \Onm\StringUtils::normalizeMetadata($r['metadata']);

            if ($output->isVeryVerbose()) {
                $output->writeln("    <info>After</>:  $metadata\n");
            }

            if (!$preview) {
                try {
                    $conn->update('contents', [ 'metadata' => $metadata ], [ 'pk_content' => $r['pk_content'] ]);
                    $cache->delete($r['content_type_name'] . '-' . $r['pk_content']);
                    $updated++;
                } catch (\Exception $e) {
                    $errors++;
                    error_log($e->getMessage());
                }
            }
        }

        if (!$output->isVerbose()) {
            $output->writeln(" <info>DONE</>");
        } else {
            $output->writeln(sprintf('<info>%s contents updated successfully</>', $updated));
        }

        if ($errors > 0) {
            $output->writeln(sprintf("<fg=red>%s errors while updating</>. For more information, check error log.", $errors));
        }
    }
}
