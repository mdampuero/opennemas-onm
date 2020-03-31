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
    /**
     * Configures the current command.
     */
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
                null
            )->addOption(
                'from',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Content id from when to export',
                null
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

Specify from which content id (pk_content) you want to start the backup:

  <info>%command.full_name% --from=content_id</info>

To specify where to store the backup you have to provide the <info>--target-dir</info> option:

  <info>%command.full_name% --target-dir=backups/</info>

You can specify the limit of contents to export with the <info>--limit</info> option:

  <info>%command.full_name% --limit=200</info>
EOF
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

        $this->getContainer()->get('core.loader')
            ->load($instanceName)
            ->onlyEnabled()
            ->init();

        $output->writeln("Exporting contents from instance $instanceName");

        $this->tpl = $this->getContainer()->get('view')->get('backend');

        // Set media
        $this->mediaPath = APPLICATION_PATH . DS . 'public' . DS . 'media' . DS . $instanceName;

        $this->exportContents();
    }

    /**
     * Converts a content to NewsML.
     *
     * @param \Content $content Content to convert.
     * @return string  $newsML  Content in NewsML format.
     */
    public function convertToNewsML($content)
    {
        if ($content->content_type_name === 'video') {
            $newsML = $this->tpl->fetch(
                'news_agency/newsml_templates/video.tpl',
                [ 'video' => $content ]
            );

            return $newsML;
        }

        $newsML = $this->tpl->fetch(
            'news_agency/newsml_templates/base.tpl',
            [
                'article'    => $content,
                'photo'      => $content->img1,
                'photoInner' => $content->img2,
                'tags'       => $this->getContainer()->get('api.service.tag')
                    ->getListByIdsKeyMapped($content->tags)['items']
            ]
        );

        return $newsML;
    }

    /**
     * Copy an image to another location.
     *
     * @param string $source Path to original image
     * @param string $dest   Path of destination
     * @param string $file   File name of image
     *
     * @return boolean $isCopied True if copied successfully
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
     * Export contents in NewsML files
     */
    public function exportContents()
    {
        // Sql order, limit and filters
        $order = [ 'pk_content' => 'ASC' ];

        // Get entity repository
        $this->er = $this->getContainer()->get('entity_repository');

        // Get total per page contents
        $perPage = 100;

        // Initialize counters
        $this->imagesCounter   = 0;
        $this->articlesCounter = 0;
        $this->opinionsCounter = 0;
        $this->albumsCounter   = 0;
        $this->videosCounter   = 0;

        // Set contents type array
        $types = [ 'video', 'album', 'opinion', 'article' ];

        // Fetch contents
        foreach ($types as $type) {
            $filters['content_type_name'] = [[ 'value' => $type ]];

            if (!empty($this->from)) {
                $filters['pk_content'] = [[ 'value' => $this->from, 'operator' => '>=' ]];
            }

            if (!empty($this->limit)) {
                $contents = $this->er->findBy($filters, $order, $this->limit, 1);

                $this->processContents($contents);
            } else {
                // Count contents
                $this->total = $this->er->countBy($filters);
                $iterations  = (int) ($this->total / $perPage) + 1;
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
            PHP_EOL . "Saved contents with <info>" . $this->imagesCounter
                . "</info> images into "
                . $this->targetDir . PHP_EOL,
            "\tArticles -> <info>" . $this->articlesCounter . "</info>\n",
            "\tOpinions -> <info>" . $this->opinionsCounter . "</info>\n",
            "\tAlbums -> <info>" . $this->albumsCounter . "</info>\n",
            "\tVideos -> <info>" . $this->videosCounter . "</info>\n",
        ]));
    }

    /**
     * Loads author object on content
     *
     * @param \Content $content Content where to load author.
     */
    public function loadAuthor($content)
    {
        try {
            if (!empty($content->fk_author)) {
                $content->author = $this->getContainer()->get('api.service.author')
                    ->getItem($content->fk_author);

                if (!empty($content->avatar_img_id)) {
                    $content->author->photo = $this->getContainer()->get('entity_repository')
                        ->find('Photo', $content->avatar_img_id);
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Process album
     *
     * @param \Album $album Album to process.
     */
    public function processAlbum($album)
    {
        $this->albumsCounter++;
        $this->output->writeln(
            $this->albumsCounter . " of " . $this->total
            . '(id: ' . $album->id . ')'
        );

        $album->all_photos = [];
        foreach ($album->photos as $value) {
            $photo = $this->er->find('Photo', $value['pk_photo']);

            $photo->img_source =
                $this->mediaPath . DS . 'images' .
                $photo->path_file .
                $photo->name;

            $album->all_photos[] = $photo;

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
                    "</info> from album <info>" . $album->id .
                    "</info> not copied'"
                );
            }
        }
    }

    /**
     * Process article
     *
     * @param \Article $article Article to process.
     */
    public function processArticle($article)
    {
        // Process frontpage image
        if (!empty($article->img1)) {
            $this->processFrontImage($article);
        }

        // Process inner image
        if (!empty($article->img2)) {
            $this->processInnerImage($article);
        }

        // Get related
        $relations = $this->getContainer()->get('related_contents')
            ->getRelations($article->id, 'inner');
        if (count($relations) > 0) {
            $article->related = $this->er->findMulti($relations);
        }

        $this->articlesCounter++;
        $this->output->writeln(
            $this->articlesCounter . " of " . $this->total . '(id: ' . $article->id . ')'
        );
    }

    /**
     * Process opinion
     *
     * @param \Opinion $opinion Opinion to process.
     */
    public function processOpinion($opinion)
    {
        // Process frontpage image
        if (!empty($opinion->img1)) {
            $this->processFrontImage($opinion);
        }

        // Process inner image
        if (!empty($opinion->img2)) {
            $this->processInnerImage($opinion);
        }

        $this->opinionsCounter++;
        $this->output->writeln(
            $this->opinionsCounter . " of " . $this->total . '(id: ' . $opinion->id . ')'
        );
    }

    /**
     * Process video
     *
     * @param \Video $video Video to process.
     */
    public function processVideo($video)
    {
        $this->videosCounter++;
        $this->output->writeln(
            $this->videosCounter . " of " . $this->total . '(id: ' . $video->id . ')'
        );
    }

    /**
     * Process content frontend image
     *
     * @param $content The content to process frontend image
     */
    public function processFrontImage($content)
    {
        $image = $this->er->find('Photo', $content->img1);

        if (is_null($image)) {
            $this->output->write(
                "\t<error>Image id " . $content->img1 . " not found</error>"
            );

            $content->img1 = 0;

            return;
        }

        // Load photo object on content
        $content->loadFrontpageImageFromHydratedArray([ $image ]);

        if (!mb_check_encoding($content->img1->description)) {
            $content->img1->description = utf8_encode($content->img1->description);
        }

        $isCopied = $this->copyImage(
            $this->mediaPath . DS . 'images' . $content->img1_path,
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

    /**
     * Process content inner image
     *
     * @param $content The content to process inner image
     */
    public function processInnerImage($content)
    {
        $image = $this->er->find('Photo', $content->img2);

        if (is_null($image)) {
            $this->output->writeln(
                "\t<error>Image " . $content->img2 . " not found</error>"
            );

            $content->img2 = 0;

            return;
        }

        // Load photo object on content
        $content->loadInnerImageFromHydratedArray([ $image ]);

        if (!mb_check_encoding($content->img2->description)) {
            $content->img2->description = utf8_encode($content->img2->description);
        }

        $isCopied = $this->copyImage(
            $this->mediaPath . DS . 'images' . $content->img2_path,
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

    /**
     * Export all articles from an instance in xml files
     *
     * @param array $contents Contents to process.
     */
    public function processContents($contents)
    {
        foreach ($contents as $content) {
            $this->output->write('Processing ' . $content->content_type_name . ' ');

            $method = 'process' . ucfirst($content->content_type_name);
            if (!method_exists($this, $method)) {
                continue;
            }

            $this->{$method}($content);

            $this->loadAuthor($content);

            // Save xml file
            $this->storeContentFile(
                $content,
                $this->convertToNewsML($content),
                $this->targetDir
            );
        }
    }

    /**
     * Writes a content in NewsML format to a file.
     *
     * @param \Content $content     Content to export.
     * @param string  $newsMLString Content in NewsML format.
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
}
