<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
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
        $this->setDefinition(
            [
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
                    'source',
                    false,
                    InputOption::VALUE_REQUIRED,
                    'Source from where to get the videos',
                    'youtube'
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
        $start = time();

        // Get arguments
        $instance       = $input->getArgument('instance-name');
        $source         = $input->getOption('source');
        $this->category = $input->getArgument('category-id');
        $this->channel  = $input->getOption('channel');

        $this->input  = $input;
        $this->output = $output;

        // Initialize application
        $basePath = APPLICATION_PATH;

        chdir($basePath);

        $dbConn = $this->getContainer()->get('db_conn_manager');

        $rs = $dbConn->GetAll('SELECT internal_name, settings FROM instances');

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

        // Initialize database connection
        $this->connection = $this->getContainer()->get('db_conn');
        $this->connection->selectDatabase($instances[$instance]['BD_DATABASE']);

        // Initialize application
        $GLOBALS['application'] = new \Application();
        \Application::load();
        \Application::initDatabase($this->connection);

        // Initialize the template system
        define('CACHE_PREFIX', '');

        // Set session variable
        $_SESSION['username'] = 'console';
        $_SESSION['userid'] = '0';

        $commonCachepath = APPLICATION_PATH.DS.'tmp'.DS.'instances'.DS.'common';
        if (!file_exists($commonCachepath)) {
            mkdir($commonCachepath, 0755, true);
        }

        $this->tpl = new \TemplateAdmin('admin');

        $conn = $this->getContainer()->get('dbal_connection');
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
            'http://gdata.youtube.com/feeds/base/users/'.$this->channel.
            '/uploads?max-results=1&start-index=1'
        );
        $totalVideos = $videos->children('openSearch', true)->totalResults;
        settype($totalVideos, 'integer');

        $this->output->writeln(
            "<fg=yellow>*** Importing $totalVideos Youtube videos from ".
            "<info>$this->channel</info> channel ***</fg=yellow>\n"
        );

        $maxVideosPerQuery = 50;
        $totalqueries = ceil($totalVideos/$maxVideosPerQuery);

        $failedVideosUrl = [];
        $importedVideos = $notImportedVideos = $alreadyImported = 0;
        for ($i = 1; $i <= $totalqueries; $i++) {
            // Fetch $maxVideosPerQuery video from channel
            $videos = simplexml_load_file(
                'http://gdata.youtube.com/feeds/base/users/'.$this->channel.
                '/uploads?max-results='.$maxVideosPerQuery.'&start-index='.
                ($maxVideosPerQuery*($i-1)+1)
            );

            foreach ($videos->entry as $video) {
                // Get video public url
                $videoUrl = (string)$video->link->attributes()['href'];

                if ($this->isAlreadyImported($videoUrl)) {
                    $alreadyImported++;
                    continue;
                }

                // Get all video information
                $videoP = new \Panorama\Video(rawurldecode($videoUrl));
                $information = $videoP->getVideoDetails();

                // Get video title
                $title = (string)$video->title;

                // Fetch dates with format
                $published = new \DateTime((string)$video->published);
                $date = date_format($published, 'Y-m-d H:i:s');

                // Generate data array for creating video in Onm instance
                $data = [
                    'content_status' => 1,
                    'with_comment' => 1,
                    'created' => $date,
                    'starttime' => $date,
                    'category' => $this->category,
                    'fk_author' => 0,
                    'video_url' => $videoUrl,
                    'title' => $title,
                    'metadata' => \Onm\StringUtils::getTags($title),
                    'description' => (string)$video->summary,
                    'author_name' => 'Youtube',
                    'information' => $information,
                    'type' => 'web-source',
                    'id' => '',
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
            'Imported videos: '. $importedVideos ."\n".
            'Already imported videos: '. $alreadyImported ."\n".
            'Failed imported videos:' . $notImportedVideos ."\n"
        );

        if (!empty($failedVideosUrl)) {
            foreach ($failedVideosUrl as $video) {
                $this->output->writeln(
                    $video."\n"
                );
            }
        }
    }

    /**
     * Check if the video is already imported
     *
     * @param string Video url
     */
    protected function isAlreadyImported($video)
    {
        $sql = "SELECT count(pk_video) as total FROM contents, videos WHERE ".
               "video_url='".$video."' AND pk_content=pk_video";
        $rs  = $this->connection->Execute($sql);

        return (bool)($rs->fields['total']);
    }

    /**
     * Displays final info about the video import
     *
     * @param integer
     */
    protected function displayFinalInfo($time)
    {
        $this->output->writeln(
            '<fg=yellow>*** Videos import finished in '.$time.
            ' secs. ***</fg=yellow>'
        );
    }
}
