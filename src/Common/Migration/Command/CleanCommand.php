<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Command;

use Common\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CleanCommand extends Command
{
    /**
     * The Filesystem component.
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('migration:clean')
            ->setDescription('Clean files from a dirty media directory')
            ->setHelp(
                'Removes files from media directory if they are not stored in database.'
            )
            ->addOption(
                'database',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Database name'
            )
            ->addOption(
                'instance',
                'i',
                InputOption::VALUE_OPTIONAL,
                'Instance name'
            )
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_REQUIRED,
                'Full path to the media directory'
            );

        $this->steps[] = 5;
    }

    /**
     * Executes the command.
     */
    protected function do()
    {
        $this->writeStep('Checking parameters');
        list($database, $path) = $this->getParameters($this->input);
        $this->writeStatus('success', 'DONE', true);

        $this->writeStep('Getting files from database');

        $files = array_merge(
            $this->getPhotos($database),
            $this->getKiosko($database),
            $this->getAttachments($database)
        );

        $this->steps[] = count($files);

        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', sprintf(' (%s files)', count($files)), true);

        $this->writeStep('Cleaning files');

        if ($this->output->isVerbose()) {
            $this->writeStatus('warning', 'IN PROGRESS', true);
        }

        $removed    = 0;
        $notRemoved = 0;

        foreach ($this->getFinder()->files()->in($path) as $file) {
            if ($this->output->isVerbose()) {
                $this->writeStep('Checking ' . $file->getPathName(), false, 2);
            }

            if (preg_match('@.*/sections/.*@', $file->getPathName()) ||
                in_array(str_replace($path, '', $file->getPathName()), $files)) {
                $notRemoved++;

                if ($this->output->isVerbose()) {
                    $this->writeStatus('warning', 'SKIP', true);
                }

                continue;
            }

            try {
                $this->getFileSystem()->remove($file->getPathName());
                $removed++;
                if ($this->output->isVerbose()) {
                    $this->writeStatus('success', 'DONE', true);
                }
            } catch (\Exception $e) {
                if ($this->output->isVerbose()) {
                    $this->writeStatus('warning', sprintf('FAIL (%s)', $e->getMessage()), true);
                }
            }
        }

        $this->output->isVerbose()
            ? $this->writeStatus('info', "  ==> ")
            : $this->writeStatus('success', 'DONE ');

        $this->writeStatus('info', sprintf(
            "(removed: %d, not removed: %d)",
            $removed,
            $notRemoved
        ), true);
    }

    /**
     * Returns a new Finder.
     *
     * @return Finder The finder.
     */
    protected function getFinder() : Finder
    {
        return new Finder();
    }

    /**
     * Returns a new FileSystem.
     *
     * @return FileSystem The fileSystem.
     */
    protected function getFileSystem() : FileSystem
    {
        return new FileSystem();
    }

    /**
     * Returns the list of parameters for the command based on the input.
     *
     * @return array The list of parameters.
     */
    protected function getParameters(InputInterface $input) : array
    {
        $database = $input->getOption('database');
        $instance = $input->getOption('instance');
        $path     = $input->getOption('path');

        if ((empty($database) && empty($instance)) || empty($path)) {
            throw new \InvalidArgumentException(
                'No option specified (`database`/`instance` and `path` required)'
            );
        }

        if (!empty($database)) {
            return [ $database, $path ];
        }

        if (!empty($instance)) {
            $oql = sprintf('internal_name = "%s"', $instance);

            $instances = $this->getContainer()->get('orm.manager')
                ->getRepository('Instance')->findBy($oql);

            $database = $instances[0]->getDatabaseName();
        }

        return [ $database, $path ];
    }

    /**
     * Returns a list of attachment filenames from database.
     *
     * @param string $database The database name.
     *
     * @return array The list of attachment filenames.
     */
    protected function getAttachments(string $database) : array
    {
        $conn = $this->getContainer()->get('orm.manager')->getConnection('instance');
        $conn->selectDatabase($database);

        $rs = $conn->fetchAll('SELECT path FROM attachments');

        return !$rs ? [] : array_map(function ($a) {
            return 'files' . $a['path'];
        }, $rs);
    }

    /**
     * Returns a list of photo filenames from database.
     *
     * @param string $database The database name.
     *
     * @return array The list of photo filenames.
     */
    protected function getPhotos(string $database) : array
    {
        $conn = $this->getContainer()->get('orm.manager')->getConnection('instance');
        $conn->selectDatabase($database);

        $rs = $conn->fetchAll('SELECT `meta_value` FROM `contentmeta` ' .
            'INNER JOIN `contents` ON `pk_content` = `fk_content` ' .
            'AND `content_type_name` = "photo" WHERE `meta_name` = "path"');

        return !$rs ? [] : array_map(function ($a) {
            return $a['meta_value'];
        }, $rs);
    }

    /**
     * Returns a list of kiosco filenames from database.
     *
     * @param string $database The database name.
     *
     * @return array The list of kiosco filenames.
     */
    protected function getKiosko(string $database) : array
    {
        $conn = $this->getContainer()->get('orm.manager')->getConnection('instance');
        $conn->selectDatabase($database);

        $rs = $conn->fetchAll('SELECT `meta_value` FROM `contentmeta` ' .
            'INNER JOIN `contents` ON `pk_content` = `fk_content` ' .
            'AND `content_type_name` = "kiosko" WHERE `meta_name` = "path" ' .
            'OR `meta_name` = "thumbnail"');

        return !$rs ? [] : array_map(function ($a) {
            return 'kiosko/' . $a['meta_value'];
        }, $rs);
    }
}
