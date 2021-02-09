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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CleanCommand extends Command
{
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
                InputOption::VALUE_REQUIRED,
                'Database name'
            )
            ->addOption(
                'path',
                'p',
                Inputoption::VALUE_REQUIRED,
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
            $this->getAttachments($database)
        );

        $this->steps[] = count($files);

        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', sprintf(' (%s files)', count($files)), true);

        $this->writeStep('Cleaning files');

        if ($this->output->isVerbose()) {
            $this->writeStatus('warning', 'IN PROGRESS', true);
        }

        $finder     = new Finder();
        $fs         = new FileSystem();
        $removed    = 0;
        $notRemoved = 0;

        foreach ($finder->files()->in($path) as $file) {
            if ($this->output->isVerbose()) {
                $this->writeStep('Checking ' . $file->getFilename(), false, 2);
            }

            if (in_array($file->getFilename(), $files)) {
                $notRemoved++;

                if ($this->output->isVerbose()) {
                    $this->writeStatus('warning', 'SKIP', true);
                }

                continue;
            }

            try {
                $fs->remove($file->getPathName());
                $removed++;
                $this->writeStatus('success', 'DONE', true);
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
            return substr($a['path'], strrpos($a['path'], '/') + 1);
        }, $rs);
    }

    /**
     * Returns the list of parameters for the command based on the input.
     *
     * @return array The list of parameters.
     */
    protected function getParameters() : array
    {
        $database = $this->input->getOption('database');
        $path     = $this->input->getOption('path');

        if (empty($database) || empty($path)) {
            throw new \InvalidArgumentException(
                'No option specified (`database` and `path` required)'
            );
        }

        return [ $database, $path ];
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

        $rs = $conn->fetchAll('SELECT `slug` FROM `contents` WHERE `content_type_name` = "photo"');

        return !$rs ? [] : array_map(function ($a) {
            return $a['slug'];
        }, $rs);
    }
}
