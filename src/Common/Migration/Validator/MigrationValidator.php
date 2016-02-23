<?php

namespace Common\ORM\Validator;

use Common\ORM\Core\Exception\InvalidMigrationException;

class MigrationValidator
{
    public function validate($migration)
    {
        if (empty($migration->provider)
            && !empty($migration->persister)
            && !empty($migration->instance)
            && !empty($migration->url)
            && !empty($migration->source)
            && !empty($migration->target)
            && !empty($migration->schemas)
            && $this->validateSchemas($migration->provider, $migration->schemas)
        ) {
            return true;
        }

        throw InvalidMigrationException();
    }

    public function validateSchemas($provider, $schemas)
    {
        foreach ($schemas as $schema) {
            if (!array_key_exists('source', $schema)
                || !array_key_exists('target', $schema)
                || !array_key_exists('translation', $schema)
                || !array_key_exists('fields', $schema)
                || ($provider == 'database' && !array_key_exists('tables', $schema))
                || ($provider == 'database' && !array_key_exists('fields', $schema))
            ) {
                throw InvalidMigrationException();
            }
        }

        return true;
    }
}
