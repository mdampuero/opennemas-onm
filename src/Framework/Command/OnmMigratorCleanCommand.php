<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Diego Blanco Estévez <diego@openhost.es>
 *
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Finder\Finder;
// use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

use Onm\DatabaseConnection;

class OnmMigratorCleanCommand extends ContainerAwareCommand
{
    /**
     * If true, debug messages will be shown during importing.
     *
     * @var boolean
     */
    protected $debug;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('migrate:clean')
            ->setDescription('Clean media files from a dirty media directory')
            ->setHelp(
                "Removes files form media directory if they are not stored in"
                . " database."
            )
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'Database name'
            )
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Full path to the media directory'
            )
            ->addOption(
                'debug',
                false,
                InputOption::VALUE_NONE,
                'If set, the command will be run in debug mode.'
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
        $database = $input->getArgument('database');
        $files    = array();
        $fs       = new FileSystem();
        $path     = $input->getArgument('path');

        // Initialize target database
        $this->targetConnection = getService('db_conn');
        $this->targetConnection->selectDatabase($database);

        $files = array_merge($files, $this->getPhotos());
        $files = array_merge($files, $this->getAttachments());

        $output->writeln(
            '<fg=yellow>*** ONM Migrator:Clean Stats ***</fg=yellow>'
        );

        $finder = new Finder();
        $total = 0;
        $removed = 0;
        $notRemoved = 0;
        foreach ($finder->files()->in($path) as $file) {
            $total++;
            if (!in_array($file->getFilename(), $files)) {
                $fs->remove($file->getPathName());
                $removed++;

                if ($this->debug) {
                    $this->output->writeln(
                        'Removed ' . $file->getPathName . 'file'
                    );
                }
            } else {
                $notRemoved++;
            }
        }

        $output->writeln(
            "Total: " . $total
            . "\t<fg=red>Removed: " . $removed . "</fg=red>"
            . "\t<fg=green>Not removed: " . $notRemoved . "</fg=green>"
        );
    }

    /**
     * Get photo names from database
     *
     * @return array Array of photo filenames.
     */
    private function getPhotos()
    {
        $files = array();
        $sql   = 'SELECT name FROM photos';

        $rs = $this->targetConnection->Execute($sql);
        $rss = $rs->getArray();

        if ($rss) {
            foreach ($rss as $value) {
                $files[] = $value['name'];
            }
        }

        return $files;
    }


    /**
     * Get attachment names from database
     *
     * @return array Array of attachment filenames.
     */
    private function getAttachments()
    {
        $files = array();
        $sql   = 'SELECT path FROM attachments';

        $rs = $this->targetConnection->Execute($sql);
        $rss = $rs->getArray();

        if ($rss) {
            foreach ($rss as $value) {
                $files[] = substr(
                    $value['path'],
                    strrpos($value['path'], '/') + 1
                );
            }
        }

        return $files;
    }
}
