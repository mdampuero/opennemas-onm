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

use Framework\ORM\Core\Entity;

class Validator
{
    /**
     * Validates the entity.
     *
     * @param Entity $entity The entity to validate.
     *
     * @throws InvalidEntityException If the entity is not valid.
     */
    public function validate(Entity $entity)
    {
        $data = $entity->getData();

        if (property_exists($this, 'required')) {
            $this->validateRequired($data);
        }

        if (property_exists($this, 'optional')) {
            $this->validateOptional($data);
        }
    }

    /**
     * Validates the custom parameters.
     *
     * @param arra $data The configuration parameters.
     *
     * @throws InvalidEntityException If some required parameters left.
     */
    public function validateCustom($data)
    {
        $missed = array_diff(array_keys($this->custom), array_keys($data));

        if (count($missed) > 0) {
            throw new \Exception('NotValidException');
        }
    }

    /**
     * Validates the required parameters.
     *
     * @param arra $data The configuration parameters.
     */
    public function validateOptional($data)
    {
        $custom = array_diff(
            array_keys($data),
            array_keys($this->required),
            array_keys($this->optional)
        );

        if (count($custom) > 0) {
            if (property_exists($this, 'custom')) {
                $this->validateCustom(array_intersect_key($data, $custom));
            }
        }
    }

    /**
     * Validates the required parameters.
     *
     * @param arra $data The configuration parameters.
     *
     * @throws InvalidEntityException If some required parameters left.
     */
    public function validateRequired($data)
    {
        $missed = array_diff(array_keys($this->required), array_keys($data));

        if (count($missed) > 0) {
            throw new \Exception('NotValidException');
        }
    }
}
