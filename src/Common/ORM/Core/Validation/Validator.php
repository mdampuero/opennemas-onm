<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Validation;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\InvalidEntityException;
use Common\ORM\Core\Validation\Validable;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * The Validator class validates entities basing on the entity metadata.
 */
class Validator
{
    /**
     * Array of defined enumerations.
     *
     * @var array
     */
    protected $enum = [];

    /**
     * Array of allowed properties.
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Array of required fields.
     *
     * @var array
     */
    protected $required = [];

    /**
     * Array of rulesets.
     *
     * @var array
     */
    protected $rulesets = [];

    /**
     * Array of PHP-Doctrine equivalent types
     *
     * @var array
     */
    protected $types = [
        'array'        => [ 'array', 'array_json', 'simple_array' ],
        'boolean'      => [ 'boolean' ],
        'dateinterval' => [ 'dateinterval' ],
        'datetime'     => [ 'date', 'datetime', 'datetimetz', 'time' ],
        'float'        => [ 'decimal', 'float' ],
        'integer'      => [ 'bigint', 'integer', 'smallint' ],
        'object'       => [ 'object' ],
        'string'       => [ 'binary', 'blob', 'guid', 'string', 'text' ]
    ];

    /**
     * Initializes the Validator.
     *
     * @param array $validations The list of validations.
     */
    public function __construct($validations = [])
    {
        if (empty($validations)) {
            return;
        }

        $this->configure($validations);
    }

    /**
     * Configures the Validator with new validations.
     *
     * @param array $validations The validations to add.
     */
    public function configure($validations)
    {
        foreach ($validations as $validation) {
            $this->loadValidation($validation);
        }
    }

    /**
     * Validates the entity.
     *
     * @param Validable $entity The entity to validate.
     *
     * @throws InvalidEntityException If the entity is not valid.
     */
    public function validate(Validable $entity)
    {
        $data    = $entity->getData();
        $ruleset = \underscore($entity->getClassName());

        if (empty($ruleset) || !in_array($ruleset, $this->rulesets)) {
            throw new InvalidEntityException(
                sprintf(
                    _("Unable to validate entity of type '%s'"),
                    $entity->getClassName()
                )
            );
        }

        $this->validateRequired($ruleset, $data);
        $this->validateData($ruleset, $data);
    }

    /**
     * Checks if the value is an array.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is an array. Otherwise, return false.
     */
    protected function isArray($value)
    {
        return is_array($value);
    }

    /**
     * Checks if the value is boolean.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is boolean. Otherwise, return false.
     */
    protected function isBoolean($value)
    {
        return is_bool($value);
    }

    /**
     * Checks if the value is an instance of DateInterval.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is an instance of DateInterval.
     *                 Otherwise, return false.
     */
    protected function isDateinterval($value)
    {
        return $value instanceof \DateInterval;
    }

    /**
     * Checks if the value is an instance of DateTime.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is an instance of DateTime.
     *                 Otherwise, return false.
     */
    protected function isDatetime($value)
    {
        return $value instanceof \DateTime;
    }

    /**
     * Checks if the value is an Entity.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is an Entity. Otherwise, return false.
     */
    protected function isEntity($value)
    {
        return $value instanceof Entity;
    }

    /**
     * Checks if the value is valid basing on the defined enumerations.
     *
     * @param mixed  $value    The value to check.
     * @param string $ruleset  The ruleset name.
     * @param string $property The property name.
     *
     * @return boolean True if the value is valid. Otherwise, return false.
     */
    protected function isEnum($value, $ruleset, $property)
    {
        if (array_key_exists($ruleset, $this->enum)
            && array_key_exists($property, $this->enum[$ruleset])
            && in_array($value, $this->enum[$ruleset][$property])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the value is a float.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is a float. Otherwise, return false.
     */
    protected function isFloat($value)
    {
        return is_integer($value) || is_double($value);
    }

    /**
     * Checks if the value is a integer.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is a integer. Otherwise, return false.
     */
    protected function isInteger($value)
    {
        return is_integer($value);
    }

    /**
     * Checks if the value is null.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is null. Otherwise, return false.
     */
    protected function isNull($ruleset, $property, $value)
    {
        return (empty($this->required)
            || !array_key_exists($ruleset, $this->required)
            || !array_key_exists($property, $this->required[$ruleset]))
            && is_null($value);
    }

    /**
     * Checks if the value is an object.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is an object. Otherwise, return false.
     */
    protected function isObject($value)
    {
        return is_object($value);
    }

    /**
     * Checks if the value is string.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is string. Otherwise, return false.
     */
    protected function isString($value)
    {
        return is_string($value);
    }

    /**
     * Loads validation rules from a validation object.
     *
     * @param Validation $validation The path to validation rules file.
     */
    protected function loadValidation($validation)
    {
        $ruleset = \underscore($validation->name);

        if (array_key_exists($ruleset, $this->properties)) {
            return;
        }

        $this->rulesets[] = $ruleset;

        foreach ($validation->getData() as $key => $value) {
            if ($key !== 'name') {
                $this->{$key}[$ruleset] = $value;
            }
        }
    }

    /**
     * Validates an entity property.
     *
     * @param string $ruleset  The ruleset to use.
     * @param string $property The property name.
     * @param mixed  $value    The property value.
     */
    protected function validateProperty($ruleset, $property, $value)
    {
        if (!array_key_exists($property, $this->properties[$ruleset])) {
            return true;
        }

        $types = $this->properties[$ruleset][$property];

        if (!is_array($types)) {
            $types = [ $types ];
        }

        foreach ($types as $type) {
            if (preg_match_all('/entity\:\:.+/', $type)) {
                $type = 'entity';
            }

            if (preg_match_all('/array\:\:.+/', $type)) {
                $type = 'array';
            }

            $checkType = 'is' . ucfirst($type);

            if (method_exists($this, $checkType)
                && $this->{$checkType}($value, $ruleset, $property)
            ) {
                return true;
            }

            if ($this->isNull($ruleset, $property, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validates the entity data.
     *
     * @param string $ruleset The ruleset to use.
     * @param array  $data    The data to validate.
     *
     * @throws InvalidEntityException If some required parameters left.
     */
    protected function validateData($ruleset, $data)
    {
        foreach ($data as $property => $value) {
            if (!$this->validateProperty($ruleset, $property, $value)) {
                throw new InvalidEntityException(
                    sprintf(
                        _("The property '%s' of type '%s' has an invalid value of '%s'"),
                        $property,
                        gettype($value),
                        print_r($value, true)
                    )
                );
            }
        }
    }

    /**
     * Checks if one or more required values are missing.
     *
     * @param string $ruleset The ruleset to use.
     * @param array  $data    The data to validate.
     *
     * @throws InvalidEntityException If some required value is missing.
     */
    protected function validateRequired($ruleset, $data)
    {
        if (empty($this->required)
            || !array_key_exists($ruleset, $this->required)
        ) {
            return;
        }

        $missed = array_diff($this->required[$ruleset], array_keys($data));

        if (count($missed) > 0) {
            throw new InvalidEntityException(
                sprintf(
                    _("The fields '%s' are missing for entity of class '%s'"),
                    implode("', '", $missed),
                    \classify($ruleset)
                )
            );
        }
    }
}
