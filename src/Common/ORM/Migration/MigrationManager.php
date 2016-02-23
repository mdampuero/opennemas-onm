<?php

namespace Common\ORM\Migration;

use Common\ORM\Migration\Migration;
use Common\ORM\Migration\Validator\MigrationValidator;
use Common\ORM\Core\Exception\InvalidPersisterException;
use Common\ORM\Core\Exception\InvalidProviderException;

class MigrationManager
{
    /**
     * Creates a new MigrationManager.
     *
     * @param Logger $logger The logger.
     */
    public function __construct($logger)
    {
        $this->logger = $logger;

        $this->migration = new Migration($settings);

        $validator = new MigrationValidator();
        $validator->validate($this->migration);
    }

    /**
     * Return the current migration.
     *
     * @return Migration The current migration.
     */
    public function getMigration($settings)
    {
        return $this->migration;
    }

    /**
     * Returns a persister by name.
     *
     * @param sting $persister The persister name.
     *
     * @return MigrationPersister The migration persister.
     */
    public function getPersister($persister)
    {
        $provider = __NAMESPACE__ . '\\Persister\\' . ucfirst($persister)
            . 'Provider';

        if (class_exists($persister)) {
            return new $persister($this->logger);
        }

        throw new InvalidMigrationPersisterException($persister);
    }

    /**
     * Returns a provider by name.
     *
     * @param sting $provider The provider name.
     *
     * @return MigrationProvider The migration provider.
     */
    public function getProvider($provider)
    {
        $provider = __NAMESPACE__ . '\\Provider\\' . ucfirst($provider)
            . 'Provider';

        if (class_exists($provider)) {
            return new $provider($this->logger);
        }

        throw new InvalidMigrationProviderException($provider);
    }

    public function migrate()
    {
        $this->persister = $this->getPersister($this->migration->persister);
        $translations    = $this->persister->getTranslations();

        $this->provider = $this->getProvider($this->migration->provider, $translations);

        foreach ($this->migration->schemas as $key => $schema) {
            $this->migrateSchema($migration->instance, $key, $schema);
        }
    }

    /**
     * Migrates a schema.
     *
     * @param string $prefix Prefix to use in traslations.
     * @param string $name   The schema name.
     * @param array  $schema The schema to migrate.
     */
    public function migrateSchema($prefix, $name, $schema)
    {
        $source = $this->provider->getSource($schema);

        $this->persister->configure($prefix, $schema);
        $this->persister->persist($source);

        $this->stats[$name] = $persister->getStats($name);
    }
}
