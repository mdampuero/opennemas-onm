<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportContentsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('export:contents')
            ->setDescription('Exports contents from one instance to a given folder path')
            ->addArgument(
                'instance',
                InputArgument::REQUIRED,
                'Instance to get contents from'
            )->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Number of contents to export',
                '*'
            )->addOption(
                'from',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Created Date from when to export',
                '*'
            )->addOption(
                'target-dir',
                't',
                InputOption::VALUE_REQUIRED,
                'The folder where store backups',
                './backups'
            )
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> exports contents from an instance:

  <info>%command.full_name%</info>

Specify from which instance you want to export the contents:

  <info>%command.full_name% --instance=path</info>

To specify where to store the backup you have to provide the <info>--target-dir</info> option:

  <info>%command.full_name% --target-dir=backups/</info>

You can specify the limit of contents to export with the <info>--limit</info> option:

  <info>%command.full_name% --limit=200</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        // Get arguments
        $instanceName    = $input->getArgument('instance');
        $this->limit     = $input->getOption('limit');
        $this->from      = $input->getOption('from');
        $this->targetDir = $input->getOption('target-dir');

        // Initialize application
        $basePath = APPLICATION_PATH;

        chdir($basePath);

        // Validate date
        if ($this->from != '*' && !$this->validateDate($this->from)) {
            throw new \Exception('Date format not valid. Required format: Y-m-d H:i:s');
        }

        $this->getContainer()->get('core.loader')
            ->load($instanceName)
            ->onlyEnabled()
            ->init();

        $output->writeln("Exporting contents from instance $instanceName");

        $commonCachepath = APPLICATION_PATH . DS . 'tmp' . DS . 'instances' . DS . 'common';
        if (!file_exists($commonCachepath)) {
            mkdir($commonCachepath, 0755, true);
        }

        $this->tpl = $this->getContainer()->get('view')->get('backend');

        // Set media
        $this->mediaPath = APPLICATION_PATH . DS . 'public' . DS . 'media' . DS . $instanceName;

        $this->exportContents();
    }

    /**
     * Converts an Article, Opinion and album to NewsML.
     *
     * @param \Content $content Content to convert.
     * @return string           Content in NewsML format.
     */
    public function convertToNewsML($content)
    {
        $content = $this->tpl->fetch(
            'news_agency/newsml_templates/export.tpl',
            [
                'article'    => $content,
                'photo'      => $content->img1,
                'photoInner' => $content->img2,
                'tags'       => $this->getContainer()->get('api.service.tag')
                    ->getListByIdsKeyMapped($content->tags)['items']
            ]
        );

        return $content;
    }

    /**
     * Converts a Video to NewsML.
     *
     * @param  \Video $content Video to convert.
     * @return string         Video in NewsML format.
     */
    public function convertVideoToNewsML($content)
    {
        $content = $this->tpl->fetch(
            'news_agency/newsml_templates/video.tpl',
            [ 'video'    => $content ]
        );

        return $content;
    }

    /**
     * Copy an image to another location.
     *
     * @param  string $source path to original image
     * @param  string $dest path of destination
     * @param  string $file file name of image
     *
     * @return boolean
     */
    public function copyImage($source, $dest, $file)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }

        $isCopied = @copy($source, $dest . $file);

        return $isCopied;
    }

    /**
     * Writes an article in NewsML format to a file.
     *
     * @param \Article $content      Article to export.
     * @param string  $newsMLString Article in NewsMML format.
     * @param string  $folder       Path where file will be created.
     */
    public function storeContentFile($content, $newsMLString, $folder)
    {
        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }

        $filename = $folder . DS . $content->content_type_name . $content->id . '.xml';
        file_put_contents($filename, $newsMLString);
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Export all articles from an instance in xml files
     *
     */
    public function exportContents()
    {
        // Sql order, limit and filters
        $order = [ 'created' => 'ASC' ];

        // Get entity repository
        $this->er = getService('entity_repository');

        // Get total per page contents
        $perPage = 100;

        // Initialize counters
        $this->imagesCounter   = 0;
        $this->articlesCounter = 0;
        $this->opinionsCounter = 0;
        $this->albumsCounter   = 0;
        $this->videosCounter   = 0;

        // Set contents type array
        $types = [
            'video',
            'album',
            'opinion',
            'article',
        ];

        // Fetch contents
        foreach ($types as $type) {
            $filters = [
                'content_type_name' => [[ 'value' => $type ]],
            ];

            if ($this->from != '*') {
                $filters['created'] = [[ 'value' => $this->from, 'operator' => '>=' ]];
            }

            if ($this->limit != '*') {
                $contents = $this->er->findBy($filters, $order, $this->limit, 1);

                $this->processContents($contents);
            } else {
                // Count contents
                $countContents = $this->er->countBy($filters);
                $this->total   = $countContents;
                $iterations    = (int) ($countContents / $perPage) + 1;
                // Fetch contents paginated
                $i = 1;
                while ($i <= $iterations) {
                    $contents = $this->er->findBy($filters, $order, $perPage, $i);
                    $i++;
                    $this->processContents($contents);
                    unset($contents);
                    gc_collect_cycles();
                }
            }
        }


        $this->output->writeln(implode(PHP_EOL, [
            PHP_EOL . "Saved contents with <info>$this->imagesCounter</info> images into "
                . $this->targetDir . PHP_EOL,
            "\tArticles -> <info>$this->articlesCounter</info>\n",
            "\tOpinions -> <info>$this->opinionsCounter</info>\n",
            "\tAlbums -> <info>$this->albumsCounter</info>\n",
            "\tVideos -> <info>$this->videosCounter</info>\n",
        ]));
    }


    /**
     * Export all articles from an instance in xml files
     *
     * @param array $contents  Contents to process.
     */
    public function processContents($contents)
    {
        foreach ($contents as $content) {
            $this->output->write('Processing ' . $content->content_type_name . ' ');

            $content->created_datetime   =
                \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $content->created
                );
            $content->starttime_datetime =
                \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $content->starttime
                );
            $content->updated_datetime   =
                \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $content->changed
                );

            switch ($content->content_type_name) {
                case 'album':
                    $this->albumsCounter++;
                    $this->output->writeln(
                        $this->albumsCounter . " of " . $this->total . '(id: ' . $content->id . ')'
                    );

                    $content->all_photos = [];
                    foreach ($content->photos as $value) {
                        $photo = $this->er->find('Photo', $value['pk_photo']);

                        // Add DateTime with format Y-m-d H:i:s
                        $photo->created_datetime =
                            \DateTime::createFromFormat(
                                'Y-m-d H:i:s',
                                $photo->created
                            );

                        $photo->updated_datetime =
                            \DateTime::createFromFormat(
                                'Y-m-d H:i:s',
                                $photo->changed
                            );

                        $photo->img_source =
                            $this->mediaPath . DS . 'images' .
                            $photo->path_file .
                            $photo->name;

                        $content->all_photos[] = $photo;

                        $isCopied = $this->copyImage(
                            $photo->img_source,
                            $this->targetDir . DS . 'images' . $photo->path_file,
                            $photo->name
                        );

                        $this->imagesCounter++;

                        if (!$isCopied) {
                            $this->imagesCounter--;
                            $this->output->writeln(
                                "\tImage <info>" . $photo->name .
                                "</info> from album <info>" . $content->id .
                                "</info> not copied'"
                            );
                        }
                    }
                    break;

                case 'article':
                case 'opinion':
                    if ($content->content_type_name == 'article') {
                        // Get related
                        $relations = getService('related_contents')->getRelations($content->id, 'inner');
                        if (!empty($relations)) {
                            $content->related = $this->er->findMulti($relations);
                        }

                        $this->articlesCounter++;
                        $this->output->writeln(
                            $this->articlesCounter . " of " . $this->total . '(id: ' . $content->id . ')'
                        );
                    } else {
                        $this->opinionsCounter++;
                        $this->output->writeln(
                            $this->opinionsCounter . " of " . $this->total . '(id: ' . $content->id . ')'
                        );
                    }

                    $imageId      = $content->img1;
                    $imageInnerId = $content->img2;

                    if (!empty($imageId)) {
                        $image = $this->er->find('Photo', $imageId);

                        if (is_null($image)) {
                            $this->output->write(
                                "\t<error>Image " . $content->img1 . " not found</error>"
                            );
                            $content->img1 = 0;
                        } else {
                            // Load attached and related contents from array
                            $content->loadFrontpageImageFromHydratedArray([$image]);
                            // Add DateTime with format Y-m-d H:i:s
                            $content->img1->created_datetime =
                                \DateTime::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $content->img1->created
                                );
                            $content->img1->updated_datetime =
                                \DateTime::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $content->img1->changed
                                );
                            if (!mb_check_encoding($content->img1->description)) {
                                $content->img1->description = utf8_encode($content->img1->description);
                            }

                            $content->img1_source = $this->mediaPath . DS . 'images' . $content->img1_path;

                            $isCopied = $this->copyImage(
                                $content->img1_source,
                                $this->targetDir . DS . 'images' . $content->img1->path_file,
                                $content->img1->name
                            );

                            $this->imagesCounter++;

                            if (!$isCopied) {
                                $this->imagesCounter--;
                                $this->output->writeln(
                                    "\tImage <info>" . $content->img1->name .
                                    "</info> from " . $content->content_type_name . " <info>" . $content->id .
                                    "</info> not copied'"
                                );
                            }
                        }
                    }

                    if (!empty($imageInnerId)) {
                        $image = $this->er->find('Photo', $imageInnerId);

                        if (is_null($image)) {
                            $this->output->writeln(
                                "\t<error>Image " . $content->img2 . " not found</error>"
                            );
                            $content->img2 = 0;
                        } else {
                            // Load attached and related contents from array
                            $content->loadInnerImageFromHydratedArray([$image]);
                            // Add DateTime with format Y-m-d H:i:s
                            $content->img2->created_datetime =
                                \DateTime::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $content->img2->created
                                );
                            $content->img2->updated_datetime =
                                \DateTime::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $content->img2->changed
                                );
                            if (!mb_check_encoding($content->img2->description)) {
                                $content->img2->description = utf8_encode($content->img2->description);
                            }

                            $content->img2_source = $this->mediaPath . DS . 'images' . $content->img2_path;

                            $isCopied = $this->copyImage(
                                $content->img2_source,
                                $this->targetDir . DS . 'images' . $content->img2->path_file,
                                $content->img2->name
                            );

                            $this->imagesCounter++;

                            if (!$isCopied) {
                                $this->imagesCounter--;
                                $this->output->writeln(
                                    "\tImage <info>" . $content->img2->name .
                                    "</info> from " . $content->content_type_name . " <info>" . $content->id .
                                    "</info> not copied'"
                                );
                            }
                        }
                    }
                    break;
            }

            if (has_author($content)) {
                $content->author = get_author_name($content);
            } else {
                $content->author = 'Redacción';
            }

            // Convert content to NewsML
            if ($content->content_type_name == 'video') {
                $this->videosCounter++;
                $this->output->writeln(
                    $this->videosCounter . " of " . $this->total . '(id: ' . $content->id . ')'
                );
                $newsMLString = $this->convertVideoToNewsML($content);
            } else {
                $newsMLString = $this->convertToNewsML($content);
            }

            // Save xml file
            $this->storeContentFile($content, $newsMLString, $this->targetDir);
        }
    }
}
