<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Migrator\Provider;

use Onm\Database\DbalWrapper;

abstract class MigrationProvider
{
    /**
     * Database connection to use while getting data from source.
     *
     * @var Onm\Database\DbalWrapper
     */
    protected $connection;

    /**
     * If true, debug messages will be shown during importing.
     *
     * @var boolean
     */
    protected $debug;

    /**
     * Logger to use during migration process.
     *
     * @var Monolog\Logger
     */
    protected $logger;

    /**
     * Array of settings to use during migration process.
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
     * Gets data from source.
     *
     * @param  string $name   Schema name.
     * @param  array  $schema Database schema.
     * @return array          Array of fields used to create new entities.
     */
    abstract public function getSource($name, $schema);

    /**
     * Constructs a new Migration provider.
     *
     * @param Logger $logger
     * @param array  $settings
     * @param array  $translations Array of translations.
     * @param array  $stats
     * @param array  $debug
     */
    public function __construct(
        $logger,
        $settings,
        &$translations,
        &$stats,
        $output,
        $checkTranslations = false,
        $debug = false
    ) {
        $this->debug        = $debug;
        $this->logger       = $logger;
        $this->output       = $output;
        $this->settings     = $settings;
        $this->stats        = &$stats;
        $this->translations = &$translations;

        $this->configure();

        if ($checkTranslations) {
            $this->configureTranslations();
        }

        $this->loadTranslations();
    }

    /**
     * Configures the current provider.
     */
    public function configure()
    {
        // Initialize target database
        $this->targetConnection = getService('dbal_connection');
        $this->targetConnection->selectDatabase(
            $this->settings['migration']['target']
        );
    }

    /**
     * Read the correspondence between identifiers
     *
     * @param  integer $oldId Element id (origin database).
     * @param  string  $type  Element type.
     * @return mixed          The new element id if it's already imported.
     *                        Otherwise, return false.
     */
    public function elementIsImported($oldId, $type)
    {
        if (array_key_exists($type, $this->translations)
                && array_key_exists($oldId, $this->translations[$type])) {
            return $this->translations[$type][$oldId];
        }

        return false;
    }

    /**
     * Fetches all translations.
     */
    public function loadTranslations()
    {
        $sql = 'SELECT * FROM translation_ids';
        $this->translations = array();

        $translations = $this->targetConnection->fetchAll($sql);

        foreach ($translations as $translation) {
            $this->translations[$translation['type']]
                [$translation['pk_content_old']] = $translation['pk_content'];
        }
    }

    /**
     * Prepares the database before starting migration.
     */
    private function configureTranslations()
    {
        // Initialize target database
        $conn = new DbalWrapper(getContainerParameter('database'), getContainerParameter('environment'));
        $conn->selectDatabase('information_schema');

        $sql = "SELECT * FROM information_schema.COLUMNS"
            . " WHERE TABLE_SCHEMA = '" . $this->settings['migration']['target']
            . "' AND TABLE_NAME = 'translation_ids' AND COLUMN_NAME = 'slug'";
        $rss = $conn->fetchAll($sql);

        $sql = "ALTER TABLE `translation_ids` CHANGE `pk_content_old`"
            . " `pk_content_old` VARCHAR(50) NOT NULL, "
            . " CHANGE `pk_content` `pk_content` VARCHAR(50) NOT NULL;";

        if ($rss && count($rss) == 0) {
            $sql .= "ALTER TABLE translation_ids ADD `slug` VARCHAR(200) DEFAULT"
                . " '' AFTER `type`;";
        }

        $rss = $this->targetConnection->executeQuery($sql);
    }
}
