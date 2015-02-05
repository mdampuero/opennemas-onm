<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onm\Validator;

class Validator
{
    /**
     * The class reflection class of the object to validate.
     *
     * @var \ReflectionClass
     */
    protected $ref;

    /**
     * Validates an object according to its class definition.
     *
     * @param mixed $class The object to validate.
     *
     * @throws InvalidArgumentException If the object state is invalid.
     */
    public function validate(&$class)
    {
        $this->ref = new \ReflectionClass($class);
        $default = $this->ref->getDefaultProperties();
        $success = true;

        foreach ($default as $key => $value) {
            if (!is_null($value)) {
                if (!isset($class->{$key})) {
                    $class->{$key} = $value;
                }

                $given    = getType($class->{$key});
                $expected = getType($value);

                // Change value to the proper type
                if ($expected != $given) {
                    $success = settype($class->{$key}, $expected);
                }

                // Unable to change type
                if (!$success) {
                    throw new \InvalidArgumentException(
                        "The property '{$key}' of '{$this->ref->name}'"
                        . ' expects a value of type ' . $expected
                        . ' (' . $given . ' given).'
                    );
                }
            }
        }
    }
}
