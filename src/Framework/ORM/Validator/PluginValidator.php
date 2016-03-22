<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Validator;

class PluginValidator extends Validator
{
    /**
     * The valid values for fields.
     *
     * @var array
     */
    protected $enum = [ 'type' => [ 'theme' ] ];

    /**
     * The optional fields in configuration.
     *
     * @var array
     */
    protected $optional = [
        'description'       => [ 'array', 'string' ],
        'parameters'        => [ 'array' ],
        'price'             => [ 'array' ],
        'short-description' => [ 'array', 'string' ],
    ];

    /**
     * The required fields in configuration.
     *
     * @var array
     */
    protected $required = [
        'author'      => [ 'string' ],
        'name'        => [ 'string' ],
        'type'        => [ 'enum' ],
        'version'     => [ 'string' ],
    ];
}
