<?php

namespace Common\Core\Command;

use Api\Exception\GetListException;
use Common\Core\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

class ConvertNewsstandCommand extends Command
{
    protected function configure()
    {
        $this->setName('convert:newsstand')
            ->setDescription('Convert newsstand images to image objects.')
            ->addOption(
                'instances',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'The list of instances to convert newsstand(e.g. norf quux)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->start();
        $output->writeln(sprintf(
            str_pad('<options=bold>(1/3) Starting command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $instances = $this->getInstances($input->getOption('instances'));

        $output->writeln(sprintf(
            str_pad('<options=bold>(2/3) Processing instances', 43, '.')
                . '<fg=yellow;options=bold>IN PROGRESS</> '
                . '<fg=blue;options=bold>(%s instances)</></>',
            count($instances)
        ));

        $index = 1;
        foreach ($instances as $instance) {
            try {
                $this->getContainer()->get('core.loader')
                    ->load($instance->internal_name);

                $this->getContainer()->get('core.security')->setInstance($instance);

                $ps = $this->getContainer()->get('api.service.photo');
                $ns = $this->getContainer()->get('api.service.newsstand');

                $oql = 'content_type_name="kiosko" order by starttime desc';

                $newsstands = array_filter($ns->getList($oql)['items'], function ($item) {
                    return substr($item->thumbnail, -4) === '.jpg';
                });

                $output->writeln(str_pad(sprintf(
                    '<fg=blue;options=bold>==></><options=bold> (%s/%s)'
                    . ' Processing instance %s</> <fg=blue;options=bold>(%s newsstand) </>',
                    $index++,
                    count($instances),
                    $instance->internal_name,
                    count($newsstands)
                ), 50, '.'));

                foreach ($newsstands as $key => $newsstand) {
                    $output->write(str_pad(sprintf(
                        '<fg=yellow;options=bold>=====></><options=bold> Processing %s</> ',
                        $key
                    ), 100, '.'));

                    if (empty($newsstand->thumbnail)) {
                        $output->writeln(sprintf(
                            '<fg=yellow;options=bold>SKIP</>'
                        ));
                        continue;
                    }
                    $filename = basename($newsstand->thumbnail);
                    $photoId  = $this->checkPhotoExists($filename);
                    $filePath = sprintf(
                        '%s/../public%s',
                        $this->getContainer()->getParameter('kernel.root_dir'),
                        $instance->getMediaShortPath() . '/kiosko/' . $newsstand->thumbnail
                    );

                    if (!file_exists($filePath)) {
                        $output->writeln(sprintf(
                            '<fg=yellow;options=bold>FILE NOT EXISTS</>'
                        ));
                        continue;
                    }

                    if (!empty($photoId)) {
                        $output->writeln(sprintf(
                            '<fg=yellow;options=bold>IMAGE ALREADY EXISTS - SKIP</>'
                        ));
                        continue;
                    }

                    if (file_exists($filePath) && empty($photoId)) {
                        $photo = $ps->createItem([
                            'created'     => $newsstand->created->format('Y-m-d H:i:s'),
                            'description' => $filename
                        ], new File($filePath), true);

                        $ns->patchItem($newsstand->id, [ 'related_contents' => $this->getContainer()->get('core.helper.featured_media')
                            ->getRelated($photo, [ 'featured_frontpage', 'featured_inner' ]) ]);

                        $output->writeln(sprintf(
                            '<fg=green;options=bold>DONE</>'
                        ));
                    }
                }
            } catch (\Exception $e) {
                $output->writeln(sprintf(
                    ' <fg=red;options=bold>FAIL</> <fg=blue;options=bold>(%s)</>',
                    $e->getMessage()
                ));
                continue;
            }
        }

        $this->end();
        $output->writeln(sprintf(
            str_pad('<options=bold>(3/3) Ending command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));
    }


    /**
     * Checks if exists an image in the database and returns its id if exists.
     *
     * @param string $filename The photo filename.
     *
     * @return integer The photo id or null if it doesnt exists.
     */
    protected function checkPhotoExists($filename)
    {
        $conn = $this->getContainer()->get('dbal_connection');

        try {
            $photo = $conn->fetchAssoc(
                "SELECT `pk_content` FROM `contents` WHERE `content_type_name` = 'photo'"
                . " AND `title` = ? OR `description` = ?",
                [ $filename, $filename ]
            );

            return $photo['pk_content'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the list of active instances.

     * @return array The list of instances.
     */
    protected function getInstances(?array $names = []) : array
    {
        $oql = sprintf(
            'activated = 1',
        );

        if (!empty($names)) {
            $oql .= sprintf(
                ' and internal_name in ["%s"]',
                implode('","', $names)
            );
        }

        return $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy($oql);
    }
}
