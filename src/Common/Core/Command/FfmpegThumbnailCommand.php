<?php

namespace Common\Core\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class FfmpegThumbnailCommand extends ContainerAwareCommand
{
    private const FFMPEG_PATH = '/usr/bin/ffmpeg';

    protected function configure()
    {
        $this
            ->setName('app:ffmpeg:thumbnail')
            ->setDescription('Create thumbnail')
            ->addOption(
                'input',
                null,
                InputOption::VALUE_REQUIRED,
                'Input file'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFile = $input->getOption('input');

        if (!$inputFile || !file_exists($inputFile)) {
            $output->writeln('<error>File not found.</error>');
            return 1;
        }

        $pathInfo      = pathinfo($inputFile);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumbnail.jpg';

        $command = sprintf(
            '%s -ss 5 -i %s -vframes 1 -q:v 2 -y %s',
            self::FFMPEG_PATH,
            escapeshellarg($inputFile),
            escapeshellarg($thumbnailPath)
        );

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $output->writeln('<error>Error:</error>');
            $output->writeln($process->getErrorOutput());
            return 1;
        }
        $this->setInstance(1);

        $created = new \Datetime();
        $this->getContainer()->get('api.service.photo')->createItem([
            'created'     => $created->format('Y-m-d H:i:s'),
            'description' => 'pepe',
            'path_file'   => $created->format('/Y/m/d/'),
            'title'       => 'pepe'
        ], new \SplFileInfo($thumbnailPath), true);

        $output->writeln('<info>Success:</info> ' . $thumbnailPath);
        return 0;
    }

    /*
     * Set the value of instance
     *
     * @return  self
     */
    public function setInstance($instance)
    {
        $instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find($instance);
        $this->getContainer()->get('core.loader')->configureInstance($instance);
        $this->getContainer()->get('core.security')->setInstance($instance);

        return $this;
    }
}
