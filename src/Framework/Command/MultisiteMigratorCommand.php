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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

use Onm\Exception\InstanceAlreadyExistsException;
use Common\ORM\Entity\Instance;

class MultisiteMigratorCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('multisite:migrate')
            ->setDescription('Migrate a multisite blog to Opennemas instances')
            ->setHelp(
                "Migrates an existing database to a openenmas database."
                . " and also creates the instance"
            )
            ->addArgument(
                'conf-file',
                InputArgument::REQUIRED,
                'Describes origin database and how to import from it.'
            )
            ->addOption(
                'debug',
                false,
                InputOption::VALUE_NONE,
                'If set, the command will be run in debug mode.'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath     = APPLICATION_PATH;
        $this->debug  = $input->getOption('debug');
        $this->output = $output;
        $this->input  = $input;

        chdir($basePath);
        gc_enable();

        $path           = $this->input->getArgument('conf-file');
        $yaml           = new Parser();
        $this->settings = $yaml->parse(file_get_contents($path));

        $blogs = $this->getAllBlogs();

        foreach ($blogs as $id => $data) {
            $instance = $this->createInstance($data);
            if ($instance === false) {
                continue;
            }
            // Update and replace contents on config file
            $configFile   = file_get_contents($path);
            $patterns     = ['/blog_number/', '/instance_name/', '/target_instance/'];
            $replacements = [ $id, $instance->internal_name, $instance->id ];
            $configFile   = preg_replace($patterns, $replacements, $configFile);
            file_put_contents('/tmp/config.yml', $configFile);

            // Call migrate:onm command
            $command   = $this->getApplication()->find('migrate:onm');
            $arguments = [
                'conf-file'  => '/tmp/config.yml',
                '--debug'    => $this->debug,
                '--no-debug' => true
            ];

            $input      = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
            gc_collect_cycles();
        }
    }

    /**
     * Create instance on manager
     *
     * @return Instance The instace object
     */
    protected function createInstance($data)
    {
        if (!is_array($data) ||
            !array_key_exists('path', $data) ||
            !array_key_exists('domain', $data) ||
            !array_key_exists('blogname', $data) ||
            !array_key_exists('siteurl', $data)
        ) {
            throw new \Exception(
                "The instance data from blog is missing. Please provide at least:\n"
                . "'path', 'domain', 'siteurl', 'blogname'"
            );
        }

        if (!array_key_exists('create', $this->settings['migration']) ||
            !array_key_exists('template', $this->settings['migration']['create']) ||
            !array_key_exists('contact_mail', $this->settings['migration']['create']) ||
            !array_key_exists('ext_domain', $this->settings['migration']['create'])
        ) {
            throw new \Exception(
                "Missing new instance data. Please provide at least:\n"
                . "'template', 'contact_mail', 'ext_domain'"
            );
        }

        $createData = $this->settings['migration']['create'];

        $this->output->writeln(
            '<fg=green>*** Creating new instance: ' . $data['blogname'] . ' ***</fg=green>'
        );

        $packProfesional = [
            'ADVANCED_SEARCH', 'ARTICLE_MANAGER', 'CATEGORY_MANAGER',
            'COMMENT_MANAGER', 'FILE_MANAGER', 'FRONTPAGE_MANAGER',
            'IMAGE_MANAGER', 'KEYWORD_MANAGER', 'MENU_MANAGER',
            'OPINION_MANAGER', 'SETTINGS_MANAGER', 'STATIC_PAGES_MANAGER',
            'TRASH_MANAGER', 'USERVOICE_SUPPORT', 'WIDGET_MANAGER',
            'ADS_MANAGER', 'ALBUM_MANAGER', 'POLL_MANAGER', 'VIDEO_MANAGER'
        ];

        $external = [
            'site_language' => 'es_ES',
            'pass_level'    => '-1',
            'max_mailing'   => '0',
            'max_users'     => '0',
            'time_zone'     => '335'
        ];

        // Domain and internal_name
        $prefix = '';
        if (array_key_exists('prefix', $createData)) {
            $prefix = $createData['prefix'];
        }

        $internal_name = $prefix . substr($data['path'], 1, -1); // /ourense/
        $domain        = $internal_name . $createData['ext_domain']; // ourense.prod

        $instance = new Instance([
            'name'              => $data['blogname'],
            'domains'           => [ $domain ],
            'contact_mail'      => $createData['contact_mail'],
            'created'           => date('Y-m-d H:i:s'),
            'activated'         => 1,
            'activated_modules' => $packProfesional,
            'settings'          => [ 'TEMPLATE_USER' => $createData['template'] ],
            'support_plan'      => 'SUPPORT_NONE',
        ]);

        // Get default sql if exists
        $defaultSql = null;
        if (array_key_exists('default_sql', $createData)) {
            $defaultSql = $createData['default_sql'];
        }

        $em = $this->getContainer()->get('orm.manager');
        try {
            $em->persist($instance);

            $this->createDatabase($instance->id, $defaultSql);
            $this->createMediaDir($instance->internal_name);

            $this->configureInstance($instance, $external);
        } catch (InstanceAlreadyExistsException $e) {
            $helper   = $this->getHelper('question');
            $question = new ConfirmationQuestion(
                'Instance already exists!! Continue with this action?',
                false
            );

            if (!$helper->ask($this->input, $this->output, $question)) {
                return false;
            }

            $instance = $em->getRepository('Instance')->findOneBy(
                sprintf('internal_name = "%s"', $internal_name)
            );
        } catch (\Exception $e) {
            $em->remove($instance);

            $this->output->writeln($e->getMessage());
            return false;
        }

        return $instance;
    }


    /**
     * Fetch all blogs from a multisite wordpress
     *
     * @return Array All blogs for this multisite wp
     */
    protected function getAllBlogs()
    {
        // Initialize multisite database connection
        $db = $this->settings['migration']['source'];

        $this->originConnection = $this->getContainer()->get('orm.manager')
            ->getConnection('instance');

        $this->originConnection->selectDatabase($db);

        // Find all active blogs
        $sql   = "SELECT `blog_id`, `domain`, `path` FROM `wp_blogs`"
            . " WHERE `blog_id` > 1 AND `public` = 1";
        $blogs = $this->originConnection->fetchAll($sql);

        // Get all blogs data
        $blogsData = [];
        foreach ($blogs as $blog) {
            $sql   = "SELECT `option_value`, `option_name` FROM `wp_" . $blog['blog_id']
                . "_options` WHERE `option_name` IN ('siteurl', 'blogname', 'blogdescription')";
            $rsAux = $this->originConnection->fetchAll($sql);

            $blogsData[$blog['blog_id']]['path']   = $blog['path'];
            $blogsData[$blog['blog_id']]['domain'] = $blog['domain'];
            foreach ($rsAux as $value) {
                $blogsData[$blog['blog_id']][$value['option_name']] = utf8_encode($value['option_value']);
            }
        }

        return $blogsData;
    }

    /**
     * Create media directory
     */
    protected function createMediaDir($instance)
    {
        $mediaPath = SITE_PATH . 'media' . DS . $instance;

        mkdir($mediaPath, 0755, true);
    }

    /**
     * Create new database for instance
     */
    protected function createDatabase($database, $default = null)
    {
        $dbconn = $this->getContainer()->get('orm.manager')
            ->getConnection('manager');

        // Create instance database
        $sql = "CREATE DATABASE IF NOT EXISTS `$database`";
        $rs  = $dbconn->executeQuery($sql);

        if (!$rs) {
            throw new \Exception(
                'Could not create the default database for the instance'
            );
        }

        if (is_null($default)) {
            $default = realpath(
                APPLICATION_PATH . DS . 'db' . DS . 'instance-default.sql'
            );
        }

        if (!file_exists($default)) {
            throw new \Exception(
                "cannot open " . $default . ": No such file"
            );
        }

        $cmd = "mysql -u{$dbconn->connectionParams['user']}"
            . " -p{$dbconn->connectionParams['password']}"
            . " -h{$dbconn->connectionParams['host']}"
            . ($database ? " $database" : '')
            . " < $default";

        exec($cmd, $output, $result);

        if ($result != 0) {
            $sql = "DROP DATABASE IF EXISTS `$database`";

            if (!$dbconn->executeQuery($sql)) {
                throw new \Exception(
                    "Could not drop the database $database"
                );
            }

            throw new \Exception(
                'Could not import the default database for the instance '
                . print_r($output)
            );
        }
    }

    /**
     * Configures the instance with the given data.
     *
     * @param Instance $instance The instance to configure.
     */
    public function configureInstance(&$instance, $data)
    {
        $cache     = $this->getContainer()->get('cache');
        $namespace = $cache->getNamespace();

        $cache->setNamespace($instance->internal_name);
        $this->sm->setConfig([
            'database'     => $instance->getDatabaseName(),
            'cache_prefix' => $instance->internal_name
        ]);

        // Build external parameters
        $external['site_name']    = $instance->name;
        $external['site_created'] = $instance->created;

        $title = $this->sm->get('site_title');
        if (strpos($title, $instance->name) === false) {
            $external['site_title'] = $instance->name . ' - ' . $title;
        }

        $description = $this->sm->get('site_description');
        if (strpos($description, $instance->name) === false) {
            $external['site_description'] = $instance->name . ' - ' . $description;
        }

        $keywords = $this->sm->get('site_keywords');
        if (strpos($keywords, $instance->name) === false) {
            $external['site_keywords'] = $instance->name . ' - ' . $keywords;
        }

        $external['site_agency'] = $instance->internal_name . '.opennemas.com';

        foreach (array_keys($external) as $key) {
            $cache->delete($key);
            $this->sm->invalidate($key);

            if (!$this->sm->set($key, $external[$key])) {
                throw new InstanceNotConfiguredException(
                    'The instance could not be configured'
                );
            }
        }

        $cache->setNamespace($namespace);
    }
}
