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
namespace Framework\MigrationProvider;

use Onm\DatabaseConnection;

abstract class MigrationProvider
{
    /**
     * Database connection to use while getting data from source.
     *
     * @var Onm\DatabaseConnection
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
        $debug = false
    ) {
        $this->debug        = $debug;
        $this->logger       = $logger;
        $this->settings     = $settings;
        $this->stats        = &$stats;
        $this->translations = &$translations;

        $this->configure();
    }

    /**
     * Configures the current provider.
     */
    public function configure()
    {
        // Initialize target database
        $this->targetConnection = getService('db_conn');
        $this->targetConnection->selectDatabase(
            $this->settings['provider']['target']
        );

        \Application::load();
        \Application::initDatabase($this->targetConnection);

        $this->loadTranslations();
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

        $rs = $this->targetConnection->Execute($sql);
        $translations = $rs->GetArray();

        foreach ($translations as $translation) {
            $this->translations[$translation['type']]
                [$translation['pk_content_old']] = $translation['pk_content'];
        }
    }
}
