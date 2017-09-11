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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class ImportVideosFromExternalCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(
                [
                    new InputArgument(
                        'source',
                        InputArgument::REQUIRED,
                        'Source from where to get the videos'
                    ),
                    new InputArgument(
                        'instance-name',
                        InputArgument::REQUIRED,
                        'Instance to import videos'
                    ),
                    new InputArgument(
                        'category-id',
                        InputArgument::REQUIRED,
                        'Category id to import videos'
                    ),
                    new InputOption(
                        'channel',
                        'c',
                        InputOption::VALUE_REQUIRED,
                        'Channel to get the videos'
                    ),
                    new InputOption(
                        'file',
                        'f',
                        InputOption::VALUE_REQUIRED,
                        'Csv file to get the videos'
                    ),
                ]
            )
            ->setName('import:external:videos')
            ->setDescription(
                'Import videos from a external channel into a selected category'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new \Exception('I am using the old Youtube API. Please, update me.');

        $start = time();

        // Get arguments
        $instance       = $input->getArgument('instance-name');
        $source         = $input->getArgument('source');
        $this->category = $input->getArgument('category-id');
        $this->channel  = $input->getOption('channel');
        $csv            = $input->getOption('file');

        $this->input  = $input;
        $this->output = $output;

        // Initialize application
        $basePath = APPLICATION_PATH;

        chdir($basePath);

        $conn = $this->getContainer()->get('orm.manager')->getConnection('instance');

        $rs = $conn->fetchAll('SELECT internal_name, settings FROM instances');

        $instances = array();
        foreach ($rs as $database) {
            $dbSettings = unserialize($database['settings']);
            $instances[$database['internal_name']] = $dbSettings;
        }

        $instanceNames = array_keys($instances);

        if (!in_array($instance, $instanceNames)) {
            throw new \Exception('Instance name not valid');
        }

        // Initialize internal constants for logger
        define('INSTANCE_UNIQUE_NAME', $instance);

        // Initialize the template system
        define('CACHE_PREFIX', '');

        // Set session variable
        $this->getContainer()->get('session')->set(
            'user',
            json_decode(json_encode([ 'id' => 0, 'username' => 'console' ]))
        );

        $commonCachepath = APPLICATION_PATH.DS.'tmp'.DS.'instances'.DS.'common';
        if (!file_exists($commonCachepath)) {
            mkdir($commonCachepath, 0755, true);
        }

        $this->tpl = new \TemplateAdmin('admin');

        $conn = $this->getContainer()->get('orm.manager')
            ->getConnection('instance');

        $conn->selectDatabase($instances[$instance]['BD_DATABASE']);

        switch ($source) {
            case 'youtube':
                if (is_null($this->channel)) {
                    throw new \Exception(
                        "For Youtube videos you need to select a channel"
                    );
                }

                $this->importYoutubeVideos();

                $end = time();
                $this->displayFinalInfo($end - $start);
                break;

            case 'csv':
                $videos = array_map('str_getcsv', @file($csv));

                if (!is_array($videos) || empty($videos)) {
                    throw new \Exception("Invalid csv file");
                }

                $this->importHTMLVideosFromCsv($videos);

                $end = time();
                $this->displayFinalInfo($end - $start);
                break;

            default:
                throw new \Exception(
                    "There is no support for the selected source"
                );
                break;
        }
    }

    /**
     * Import all videos from a Youtube channel
     *
     */
    public function importYoutubeVideos()
    {
        // Get total number of videos
        $videos = simplexml_load_file(
            'http://gdata.youtube.com/feeds/base/users/' . $this->channel .
            '/uploads?max-results=1&start-index=1'
        );

        $totalVideos = $videos->children('openSearch', true)->totalResults;
        settype($totalVideos, 'integer');

        $this->output->writeln(
            "<fg=yellow>*** Importing $totalVideos Youtube videos from " .
            "<info>$this->channel</info> channel ***</fg=yellow>\n"
        );

        $maxVideosPerQuery = 50;
        $totalqueries      = ceil($totalVideos / $maxVideosPerQuery);

        $failedVideosUrl = [];
        $importedVideos  = $notImportedVideos = $alreadyImported = 0;
        for ($i = 1; $i <= $totalqueries; $i++) {
            // Fetch $maxVideosPerQuery video from channel
            $videos = simplexml_load_file(
                'http://gdata.youtube.com/feeds/base/users/' . $this->channel
                . '/uploads?max-results=' . $maxVideosPerQuery . '&start-index='
                . ($maxVideosPerQuery * ($i - 1) + 1)
            );

            foreach ($videos->entry as $video) {
                // Get video public url
                $videoUrl = (string) $video->link->attributes()['href'];

                if ($this->isAlreadyImported($videoUrl)) {
                    $alreadyImported++;
                    continue;
                }

                // Get all video information
                $videoP      = new \Panorama\Video(rawurldecode($videoUrl));
                $information = $videoP->getVideoDetails();

                // Get video title
                $title = (string) $video->title;

                // Fetch dates with format
                $published = new \DateTime((string) $video->published);
                $date      = date_format($published, 'Y-m-d H:i:s');

                $fm = $this->getcontainer()->get('data.manager.filter');

                // Generate data array for creating video in Onm instance
                $data = [
                    'content_status' => 1,
                    'with_comment'   => 1,
                    'created'        => $date,
                    'starttime'      => $date,
                    'category'       => $this->category,
                    'fk_author'      => 0,
                    'video_url'      => $videoUrl,
                    'title'          => $title,
                    'metadata'       => $fm->set($title)->filter('tags')->get(),
                    'description'    => (string) $video->summary,
                    'author_name'    => 'Youtube',
                    'information'    => $information,
                    'type'           => 'web-source',
                    'id'             => '',
                ];

                $video = new \Video();

                // Generate stats for imported videos
                if ($video->create($data)) {
                    $importedVideos++;
                } else {
                    $notImportedVideos++;
                    array_push($failedVideosUrl, $videoUrl);
                }
            }

            // Try to avoid youtube query limits
            sleep(3);
        }

        $this->output->writeln(
            'Imported videos: ' . $importedVideos . "\n"
            . 'Already imported videos: ' . $alreadyImported . "\n"
            . 'Failed imported videos:' . $notImportedVideos . "\n"
        );

        if (!empty($failedVideosUrl)) {
            foreach ($failedVideosUrl as $video) {
                $this->output->writeln($video . "\n");
            }
        }
    }

    /**
     * Import Videos from a csv file
     *
     * csv order: title,body,date,thumbnail
     *
     * @param string csv file path
     */
    public function importHTMLVideosFromCsv($videos)
    {
        $this->output->writeln(
            "<fg=yellow>*** Importing count($videos) videos from csv "
            . "<info>$file</info> ***</fg=yellow>\n"
        );

        $importedVideos = $notImportedVideos = $alreadyImported = 0;
        foreach ($videos as $item) {
            $starttime = new \DateTime((string) $item[2]);
            $date      = date_format($starttime, 'Y-m-d H:i:s');
            $thumbnail = $item[3];

            if ($this->isAlreadyImported($date . $item[0] . 'csv')) {
                $alreadyImported++;
                continue;
            }

            $fm = $this->getContainer()->get('data.manager.filter');

            $data = [
                'author_name'    => 'script',
                'body'           => $item[1],
                'category'       => $this->category,
                'content_status' => 1,
                'fk_author'      => 0,
                'metadata'       => $fm->set($item[0])->filter('tags')->get(),
                'params'         => [],
                'description'    => $item[0],
                'endtime'        => '',
                'starttime'      => $date,
                'created'        => $date,
                'changed'        => $date,
                'title'          => $item[0],
                'video_url'      => $date . $item[0] . 'csv',
                'with_comment'   => 1,
                'information'    => [ 'thumbnail' => $thumbnail ],
            ];

            $video = new \Video();

            // Generate stats for imported videos
            if ($video->create($data)) {
                $importedVideos++;
            } else {
                $notImportedVideos++;
                array_push($failedVideosUrl, $data['title']);
            }
        }

        $this->output->writeln(
            'Imported videos: ' . $importedVideos . "\n"
            . 'Already imported videos: ' . $alreadyImported . "\n"
            . 'Failed imported videos:' . $notImportedVideos . "\n"
        );

        if (!empty($failedVideosUrl)) {
            foreach ($failedVideosUrl as $fail) {
                $this->output->writeln($fail . "\n");
            }
        }
    }

    /**
     * Check if the video is already imported
     *
     * @param string Video url
     */
    protected function isAlreadyImported($url)
    {
        $sql = "SELECT count(pk_video) as total FROM contents, videos WHERE "
            . "video_url='" . $url . "' AND pk_content=pk_video";
        $rs  = $this->connection->fetchAll($sql);

        return (bool) ($rs['total']);
    }

    /**
     * Displays final info about the video import
     *
     * @param integer
     */
    protected function displayFinalInfo($time)
    {
        $this->output->writeln(
            '<fg=yellow>*** Videos import finished in ' . $time
            . ' secs. ***</fg=yellow>'
        );
    }
}
