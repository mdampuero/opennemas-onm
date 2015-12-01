<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Loader;

use Framework\ORM\Core\Schema;
use Framework\ORM\Validator\SchemaValidator;

class SchemaLoader
{
    /**
     * The schema validator.
     *
     * @var SchemaValidator
     */
    protected $validator;

    /**
     * Initializes the SchemaLoader.
     *
     * @param SchemaValidator $validator The schema validator.
     */
    public function __construct(SchemaValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Loads a schema from a configuration.
     *
     * @param array $config The schema configuration.
     *
     * @return Theme The loaded theme.
     */
    public function load($config)
    {
        $schema = new Schema($config);

        $this->validator->validate($schema);

        return $schema;
    }
}
