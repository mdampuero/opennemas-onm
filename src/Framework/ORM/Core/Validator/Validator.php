<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Core\Validator;

use Framework\ORM\Core\Entity;
use Framework\ORM\Exception\InvalidEntityException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

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
     * @param Entity $entity The entity to validate.
     *
     * @throws InvalidEntityException If the entity is not valid.
     */
    public function validate(Entity $entity)
    {
        $data     = $entity->getData();
        $rulesets = [
            \underscore($entity->getClassName()),
            \underscore($entity->getParentClassName())
        ];

        $rulesets = array_intersect($this->rulesets, $rulesets);

        if (empty($rulesets)) {
            throw new InvalidEntityException(
                sprintf(
                    _("Unable to validate entity of type '%s'"),
                    $entity->getClassName()
                )
            );
        }

        foreach ($rulesets as $ruleset) {
            $this->validateRequired($ruleset, $data);
            $this->validateData($ruleset, $data);
        }
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
     * Checks if the value is a double.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is a double. Otherwise, return false.
     */
    protected function isDouble($value)
    {
        return is_double($value);
    }

    /**
     * Checks if the value is valid basing on the defined enumerations.
     *
     * @param mixed $value The value to check.
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
     * Checks if the value is numeric.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is numeric. Otherwise, return false.
     */
    protected function isNumeric($value)
    {
        return is_numeric($value);
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
            $checkType = 'is' . ucfirst($type);

            if (method_exists($this, $checkType)
                && $this->{$checkType}($value, $ruleset, $property)) {
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

    /**
     * Loads validation rules from a validation object.
     *
     * @param Validation $validation The path to validation rules file.
     */
    protected function loadValidation($validation)
    {
        $ruleset = \underscore($validation->entity['name']);

        if (array_key_exists($ruleset, $this->properties)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The validation rules for entity %s already exist',
                    $ruleset
                )
            );
        }

        $this->rulesets[] = $ruleset;

        foreach ($validation->entity as $key => $value) {
            if ($key !== 'name') {
                $this->{$key}[$ruleset] = $value;
            }
        }
    }
}
