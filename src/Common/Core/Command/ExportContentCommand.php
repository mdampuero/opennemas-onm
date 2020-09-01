<?php

namespace Common\Core\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ExportContentCommand extends Command
{
    /**
     * The command stats.
     *
     * @var array
     */
    protected $stats = [
        'album'   => 0,
        'article' => 0,
        'opinion' => 0,
        'video'   => 0
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('core:content:export')
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
            );

        $this->steps[] = 5;
        $this->steps[] = 4;
    }

    /**
     * Converts a content to NewsML.
     *
     * @param \Content $content Content to convert.
     *
     * @return string Content in NewsML format.
     */
    protected function convertToNewsML($content) : string
    {
        if ($content->content_type_name === 'video') {
            return $this->getContainer()->get('view')->get('backend')
                ->fetch('news_agency/newsml_templates/video.tpl', [
                    'video' => $content
                ]);
        }

        return $this->getContainer()->get('view')->get('backend')
            ->fetch('news_agency/newsml_templates/base.tpl', [
                'content'    => $content,
                'photo'      => $content->img1,
                'photoInner' => $content->img2,
                'tags'       => $this->getContainer()->get('api.service.tag')
                    ->getListByIdsKeyMapped($content->tags)['items']
            ]);
    }

    /**
     * Executes the current command.
     */
    protected function do()
    {
        $this->writeStep('Checking parameters');
        list($instance, $from, $limit, $target) = $this->getParameters($this->input);
        $this->writeStatus('success', 'DONE', true);

        $this->mediaPath = APPLICATION_PATH . DS . 'public' . DS . 'media' . DS . $instance;

        $this->getContainer()->get('core.loader')
            ->load($instance)
            ->onlyEnabled()
            ->init();

        $this->writeStep("Exporting contents from instance $instance");

        if ($this->output->isVerbose()) {
            $this->writeStatus('warning', 'IN PROGRESS', true);
        }

        $this->getContainer()->get('core.helper.url_generator')->forceHttp(true);

        $this->exportContents($target, $from, $limit);

        if (!$this->output->isVerbose()) {
            $this->writeStatus('success', 'DONE', true);
        }

        $this->writeStep('Generating report');
        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', sprintf(
            ' (articles: %d, opinions %d, albums: %s, videos: %d)',
            $this->stats['article'],
            $this->stats['opinion'],
            $this->stats['album'],
            $this->stats['video']
        ), true);
    }

    /**
     * Writes a content in NewsML format to a file.
     *
     * @param \Content $content Content to export.
     * @param string   $newsml  Content in NewsML format.
     * @param string   $folder  Path where file will be created.
     */
    protected function dumpFile($content, $newsml, $folder)
    {
        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }

        $path = $folder . DS . $content->content_type_name . $content->id . '.xml';

        file_put_contents($path, $newsml);
    }

    /**
     * Export contents in NewsML files
     */
    protected function exportContents($target, $from = null, $limit = null)
    {
        $this->er = $this->getContainer()->get('entity_repository');

        $order      = [ 'pk_content' => 'ASC' ];
        $perPage    = $limit ?? 100;
        $iterations = 1;
        $total      = $limit;
        $criteria   = [];

        if (!empty($from)) {
            $criteria['pk_content'] = [[ 'value' => $from, 'operator' => '>=' ]];
        }

        foreach ([ 'video', 'album', 'opinion', 'article' ] as $type) {
            if ($this->output->isVerbose()) {
                $this->writeStep("Processing $type contents", $this->output->isVeryVerbose(), 2);
            }

            $criteria['content_type_name'] = [[ 'value' => $type ]];

            if (empty($limit)) {
                $total      = $this->er->countBy($criteria);
                $iterations = ceil($total / $perPage);
            }

            for ($i = 0; $i < $iterations; $i++) {
                $contents = $this->er->findBy($criteria, $order, $perPage, $i + 1);
                $this->processContents($contents, $target);

                unset($contents);
                gc_collect_cycles();
            }

            if ($this->output->isVerbose() && !$this->output->isVeryVerbose()) {
                $this->writeStatus('success', 'DONE', true);
            }
        }
    }

    /**
     * Returns the list of parameters for the command based on the input.
     *
     * @return array The list of parameters.
     */
    protected function getParameters()
    {
        return [
            $this->input->getArgument('instance'),
            $this->input->getOption('from'),
            $this->input->getOption('limit'),
            $this->input->getOption('target-dir')
        ];
    }

    /**
     * Loads author object on content.
     *
     * @param \Content $content Content where to load author.
     */
    protected function loadAuthor($content)
    {
        try {
            if (!empty($content->fk_author)) {
                $content->author = $this->getContainer()->get('api.service.author')
                    ->getItem($content->fk_author);

                if (!empty($content->author->avatar_img_id)) {
                    $content->author->photo = $this->getContainer()
                        ->get('api.service.photo')
                        ->getItem($content->author->avatar_img_id);
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Processes an album.
     *
     * @param \Album $album Album to process.
     */
    protected function processAlbum($album)
    {
        $album->all_photos = [];

        foreach ($album->photos as $value) {
            $photo = $this->getContainer()->get('api.service.photo')->getItem($value['pk_photo']);

            $photo->img_source =
                $this->mediaPath . DS . 'images' .
                $photo->path;

            $album->all_photos[] = $photo;
        }
    }

    /**
     * Processes an article.
     *
     * @param \Article $article Article to process.
     */
    protected function processArticle($article)
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
    }

    /**
     * Export all articles from an instance in xml files
     *
     * @param array $contents Contents to process.
     */
    protected function processContents($contents, $target)
    {
        foreach ($contents as $content) {
            if ($this->output->isVeryVerbose()) {
                $this->writePad(sprintf(
                    '- Processing %s (id: %s)',
                    $content->content_type_name,
                    $content->pk_content
                ));
            }

            $method = 'process' . ucfirst($content->content_type_name);

            if (method_exists($this, $method)) {
                $this->{$method}($content);
            }

            $this->loadAuthor($content);
            $this->dumpFile($content, $this->convertToNewsML($content), $target);

            $this->stats[$content->content_type_name]++;

            if ($this->output->isVeryVerbose()) {
                $this->writeStatus('success', 'DONE', true);
            }
        }
    }

    /**
     * Process content frontend image
     *
     * @param $content The content to process frontend image
     */
    protected function processFrontImage($content)
    {
        $image = $this->getContainer()->get('api.service.photo')->getItem($content->img1);

        if (is_null($image)) {
            $content->img1 = 0;
            return;
        }

        $content->img1_path = $image->path;
        $content->img1      = $image;

        if (!mb_check_encoding($content->img1->description)) {
            $content->img1->description = utf8_encode($content->img1->description);
        }
    }

    /**
     * Process content inner image
     *
     * @param $content The content to process inner image
     */
    protected function processInnerImage($content)
    {
        $image = $this->getContainer()->get('api.service.photo')->getItem($content->img2);

        if (is_null($image)) {
            $content->img2 = 0;
            return;
        }

        $content->img2_path = $image->path;
        $content->img2      = $image;

        if (!mb_check_encoding($content->img2->description)) {
            $content->img2->description = utf8_encode($content->img2->description);
        }
    }

    /**
     * Processes an opinion.
     *
     * @param \Opinion $opinion Opinion to process.
     */
    protected function processOpinion($opinion)
    {
        // Process frontpage image
        if (!empty($opinion->img1)) {
            $this->processFrontImage($opinion);
        }

        // Process inner image
        if (!empty($opinion->img2)) {
            $this->processInnerImage($opinion);
        }
    }
}
