<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Diego Blanco Estévez <diego@openhost.es>
 *
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

use Framework\Migrator\Saver\MigrationSaver;
use Onm\DatabaseConnection;

class OnmMigratorCommand extends ContainerAwareCommand
{
    /**
     * If true, debug messages will be shown during importing.
     *
     * @var boolean
     */
    protected $debug;

    /**
     * Array of database settings to use in migration process.
     *
     * @var array
     */
    protected $settings;

    /**
     * Array to save some results during the migration process.
     *
     * @var array
     */
    protected $stats = array();

    /**
     * Array of database translations
     *
     * @var array
     */
    protected $translations;

    /**
     * Provider to use during migration
     *
     * @var MigrationProvider
     */
    protected $provider;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('migrate:onm')
            ->setDescription('Migrate a region database to Openemas')
            ->setHelp(
                "Migrates an existing database to a openenmas database."
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
     * Displays a message before starting migration.
     */
    protected function displayConfigurationInfo()
    {
        $info = "Instance: " . $this->settings['migration']['instance']
            . "\nSite url: " . $this->settings['migration']['url']
            . "\nProvider: " . $this->settings['migration']['provider']
            . "\nSaver:    "
            . (array_key_exists('saver', $this->settings['migration']) ?
                $this->settings['migration']['saver'] : 'MigrationSaver');

        $this->output->writeln($info);
    }

    /**
     * Displays a message when ONM Migrator finishes the migration.
     *
     * @param integer
     */
    protected function displayFinalInfo($time)
    {
        $this->output->writeln(
            '<fg=yellow>*** ONM Migrator Stats ***</fg=yellow>'
        );

        foreach ($this->stats as $section => $stats) {
            $this->displaySectionResults($section, $stats);
        }

        $this->output->writeln(
            '<fg=yellow>*** ONM Importer finished in '
            . $time . ' secs. ***</fg=yellow>'
        );
    }

    /**
     * Display results after importing a section.
     *
     * @param string $section Section imported
     * @param array  $stats   Results after importing $section.
     */
    protected function displaySectionResults($section, $stats)
    {
        $this->output->writeln(
            ucwords($section) . " ("
            . ($stats['end'] - $stats['start']) . " secs.)"
        );

        $this->output->writeln(
            "<fg=green>Imported: " . $stats['imported']
            . "</fg=green><fg=yellow>\tAlready imported: "
            . $stats['already_imported'] . "</fg=yellow><fg=red>\tNot found: "
            . $stats['not_found'] . "</fg=red><fg=red>\tError: "
            . $stats['error'] . "</fg=red>\n"
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
        $start        = time();

        $basePath     = APPLICATION_PATH;
        $this->debug  = $input->getOption('debug');
        $this->output = $output;
        $this->logger = $this->getContainer()->get('logger');

        chdir($basePath);

        $this->output->writeln(
            '<fg=yellow>*** Starting ONM Migrator ***</fg=yellow>'
        );

        $path = $input->getArgument('conf-file');
        $yaml = new Parser();
        $this->settings = $yaml->parse(file_get_contents($path));

        $this->configureMigrator();
        $this->displayConfigurationInfo();

        $this->import();

        $end = time();
        $this->displayFinalInfo($end - $start);
    }

    /**
     * Configures the migrator.
     */
    private function configureMigrator()
    {
        if (array_key_exists('saver', $this->settings['migration'])
            && class_exists(
                'Framework\Migrator\Provider\\'
                . $this->settings['migration']['saver']
            )
        ) {
            $saver = 'Framework\Migrator\Saver\\'
                . $this->settings['migration']['saver'];

            $this->saver = new $saver(
                $this->logger,
                $this->settings,
                $this->translations,
                $this->stats,
                $this->output,
                $this->debug
            );
        } else {
            // Creates a new migration saver
            $this->saver = new MigrationSaver(
                $this->logger,
                $this->settings,
                $this->translations,
                $this->stats,
                $this->output,
                $this->debug
            );
        }

        if (array_key_exists('provider', $this->settings['migration'])) {
            $provider = 'Framework\Migrator\Provider\\'
                . $this->settings['migration']['provider'];

            if (class_exists($provider)) {
                $this->provider = new $provider(
                    $this->logger,
                    $this->settings,
                    $this->translations,
                    $this->stats,
                    $this->output,
                    $this->debug
                );
            } else {
                throw new \Exception($provider . " class not found");
            }
        } else {
            throw new \Exception("Provider not defined.");
        }
    }

    /**
     * Import from origin database to final database
     */
    protected function import()
    {
        foreach ($this->settings['migration']['schemas'] as $key => $schema) {
            $this->output->writeln(
                "\n<fg=yellow>Migrating from <fg=red>"
                . (isset($schema['source']['table']) ?
                $schema['source']['table'] : $schema['source']['path'])
                . '</fg=red> to <fg=green>'
                . $schema['target']
                . "</fg=green>...</fg=yellow>"
            );

            $this->stats[$key]['already_imported'] = 0;
            $this->stats[$key]['error']            = 0;
            $this->stats[$key]['imported']         = 0;
            $this->stats[$key]['not_found']        = 0;
            $this->stats[$key]['start']            = time();

            $data = $this->provider->getSource($key, $schema);

            switch ($schema['target']) {
                case 'album':
                    $this->saver->saveAlbums($key, $schema, $data);
                    break;
                case 'album_photos':
                    $this->saver->saveAlbumPhotos($key, $schema, $data);
                    break;
                case 'article':
                    $this->saver->saveArticles($key, $schema, $data);
                    break;
                case 'attachment':
                    $this->saver->saveAttachments($key, $schema, $data);
                    break;
                case 'category':
                    $this->saver->saveCategories($key, $schema, $data);
                    break;
                case 'comment':
                    $this->saver->saveComments($key, $schema, $data);
                    break;
                case 'photo':
                    $this->saver->savePhotos($key, $schema, $data);
                    break;
                case 'opinion':
                    $this->saver->saveOpinions($key, $schema, $data);
                    break;
                case 'user':
                    $this->saver->saveUsers($key, $schema, $data);
                    break;
                case 'user_group':
                    $this->saver->saveUserGroups($key, $schema, $data);
                    break;
                case 'video':
                    $this->saver->saveVideos($key, $schema, $data);
                    break;
                default:
                    break;
            }

            $this->saver->remapTranslations($key, $schema);

            $this->stats[$key]['end'] = time();
            $this->displaySectionResults($key, $this->stats[$key]);
        }
    }
}
