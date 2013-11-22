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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;
use Onm\Settings as s;

class DisqusSyncCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('user', InputArgument::REQUIRED, 'user'),
                    new InputOption('password', 'p', InputOption::VALUE_OPTIONAL, 'The database password'),
                    new InputArgument('database', InputArgument::REQUIRED, 'database'),
                )
            )
            ->setName('disqus:import')
            ->setDescription('Executes comments import action with Disqus Api')
            ->setHelp(
                <<<EOF
The <info>disqus:import</info> executes acomments import action with Disqus Api.

<info>php app/console disqus:import user [-p pass] database</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $basePath = APPLICATION_PATH;
        $phpBinPath = exec('which php');

        chdir($basePath);

        $dataBaseHost = 'localhost';
        $dataBaseType = 'mysqli';
        $dataBaseUser = $input->getArgument('user');
        $dataBaseName = $input->getArgument('database');
        $dataBasePass = $input->getOption('password');

        define('BD_HOST', $dataBaseHost);
        define('BD_USER', $dataBaseUser);
        define('BD_TYPE', $dataBaseType);
        define('BD_DATABASE', $dataBaseName);

        if (!$dataBasePass) {
            // Ask password
            $dialog = $this->getHelperSet()->get('dialog');

            $validator = function ($value) {
                if (trim($value) == '') {
                    throw new \Exception('The password can not be empty, please try again');
                } elseif (!$connect = @mysql_connect('localhost', BD_USER, $value)) {
                    throw new \Exception('The password is wrong, please try again');
                }

                // Close connection if opened
                if ($connect) {
                    mysql_close($connect);
                }

                return $value;
            };

            $dataBasePass = $dialog->askHiddenResponseAndValidate(
                $output,
                'What is the password?',
                $validator,
                5,
                false
            );
        }

        if (trim($dataBasePass) == '') {
            throw new \Exception('The password can not be empty, please try again');
        } elseif (!$connect = @mysql_connect('localhost', BD_USER, $dataBasePass)) {
            throw new \Exception('The password is wrong, please try again');
        }

        // Close connection if opened
        if ($connect) {
            mysql_close($connect);
        }

        define('BD_PASS', $dataBasePass);

        // Initialize internal constants for
        define('CACHE_PREFIX', 'disqus');
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', 'community');

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();

        // Init script
        $output->writeln("\tStart disqus comments import");

        // Import
        $this->fetchDisqusPosts($input, $output);

        // Finish script
        $output->writeln("\n\tFinished disqus comments import");

    }


    protected function fetchDisqusPosts($input, $output)
    {
        // Get disqus shortname and secretkey
        $disqusShortName = s::get('disqus_shortname');
        $disqusSecretKey = s::get('disqus_secret_key');

        // Create Disqus instance
        $disqus = new \DisqusAPI($disqusSecretKey);

        // Set API call params
        $params = array('forum' => $disqusShortName, 'order' =>  'asc', 'limit' => 100);

        // Fetch last comment date
        $comment = new \Comment();
        $lastDate = $comment->getLastCommentDate();
        if ($lastDate) {
            $params['since'] = date('Y-m-d H:i:s', strtotime($lastDate) + 1);
        }

        // Store all contents id on this array to update num comments
        $contents = array();

        // Fetch the latest comments (http://disqus.com/api/docs/posts/list/)
        do {
            try {
                $posts = $disqus->posts->list($params);

                foreach ($posts as $post) {
                    $output->writeln("\tImporting comment: ".$post->id." - ".date('Y-m-d H:i:s', strtotime($post->createdAt)));

                    // Fetch thread details (http://disqus.com/api/docs/threads/details/)
                    $threadDetails = $disqus->threads->details(array('thread' => $post->thread));

                    // Get content id from disqus identifier
                    $contentId = 0;
                    if (!empty($threadDetails) && isset($threadDetails->identifiers[0])) {
                        $disqusIdentifier = @explode('-', $threadDetails->identifiers[0]);
                        if (isset($disqusIdentifier[1])) {
                            $contentId = $disqusIdentifier[1];
                        }
                    }

                    // Add contents id to array
                    $contents[$contentId] = $contentId;

                    // Get parent_id if not null
                    $parentId = 0;
                    if (!is_null($post->parent)) {
                        $parentId = $comment->getCommentIdFromPropertyAndValue('disqus_post_id', $post->parent);
                    }

                    $data = array(
                        'content_id'   => $contentId,
                        'author'       => $post->author->name,
                        'author_email' => @$post->author->email,
                        'author_url'   => @$post->author->url,
                        'author_ip'    => @$post->ipAddress,
                        'date'         => date('Y-m-d H:i:s', strtotime($post->createdAt)),
                        'body'         => $post->raw_message,
                        'status'       => ($post->isApproved) ? 'accepted': 'rejected',
                        'agent'        => 'Disqus v3.0',
                        'type'         => 'comment',
                        'parent_id'    => $parentId,
                        'user_id'      => 0,
                    );

                    // Create comment
                    $comment->create($data);

                    // Set contentmeta for comment
                    $comment->setProperty('disqus_post_id', $post->id);
                    $comment->setProperty('disqus_thread_id', $post->thread);
                    $comment->setProperty('disqus_thread_link', $threadDetails->link);
                }

                if (!empty($posts)) {
                    $params['since'] = $posts[count($posts)-1]->createdAt;
                }

            } catch (\DisqusAPIError $e) {
                $output->writeln("\tUnable to import comment: ".$e->getMessage());
            }

        } while (count($posts) == 100);

        foreach ($contents as $id) {
            $comment->updateContentTotalComments($id);
        }

        // Get service container and save disqus_last_sync time
        global $sc;
        $cache = $sc->get('cache');
        $cache->save(CACHE_PREFIX.'disqus_last_sync', time());

        $output->writeln("\t<info>Disqus comments imported successfully</info>");
    }
}
