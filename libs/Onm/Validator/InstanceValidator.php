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

use Onm\Exception\InstanceAlreadyExistsException;

class InstanceValidator extends Validator
{
    /**
     * The InstanceManager service.
     *
     * @var InstanceManager
     */
    protected $im;

    /**
     * Initializes the InstanceValidator.
     *
     * @param InstanceManager $im The InstanceManager service.
     */
    public function __construct($im)
    {
        $this->im = $im;
    }

    /**
     * Validates an instance according to its class definition.
     *
     * @param Instance $class The instance to validate.
     *
     * @throws InvalidArgumentException If the instance state is invalid.
     */
    public function validate(&$class)
    {
        parent::validate($class);

        $this->validateDomains($class);
        $this->validateInternalName($class);
        $this->validateDomainExpire($class);
        $this->validateLastInvoice($class);
    }

    /**
     * Validates the domains for the given instance.
     *
     * @param Instance $class The instance to validate.
     */
    public function validateDomains(&$class)
    {
        if (empty($class->domains)) {
            throw new \InvalidArgumentException(
                "The property 'domains' of '{$this->ref->name}'"
                . ' cannot be empty.'
            );
        }

        $criteria = array('domains' => array());

        foreach ($class->domains as $domain) {
            $criteria['domains']['union'] = 'OR';
            $criteria['domains'][] = array(
                'value' => "^$domain|,[ ]*$domain|$domain$",
                'operator' => 'REGEXP'
            );
        }

        $instance = $this->im->findOneBy($criteria);

        if ($instance && $instance->id != $class->id) {
            throw new InstanceAlreadyExistsException();
        }
    }

    /**
     * Validates the internal_name for the given instance. If not set, creates a
     * new one from the first domain.
     *
     * @param Instance $class The instance to validate.
     */
    public function validateInternalName(&$class)
    {
        // Create internal_name from domains
        if (!$class->internal_name) {
            if (array_key_exists(0, $class->domains)) {
                $class->internal_name = explode('.', $class->domains[0]);
                $class->internal_name = array_shift($class->internal_name);
            }
        }

        $class->internal_name = strtolower($class->internal_name);

        $criteria = [
            'internal_name' => [
                [
                    'value' => '^'.$class->internal_name . '[0-9]*$',
                    'operator' => 'REGEXP'
                ]
            ]
        ];

        if ($class->id) {
            $criteria['id'] = [
                [
                    'value' => $class->id,
                    'operator' => '!='
                ]
            ];
        }

        $count = $this->im->countBy($criteria);

        if ($count && $count > 0) {
            $class->internal_name .= $count;
        }
    }

    /**
     * Validates the domain_expire for the given instance.
     *
     * @param Instance $class The instance to validate.
     */
    public function validateDomainExpire(&$class)
    {
        if (isset($class->domain_expire)
            && !empty($class->domain_expire)
            && is_string($class->domain_expire)
        ) {
            $date = new \Datetime($class->domain_expire);
            $class->domain_expire = $date->format('Y-m-d H:i:s');
        }
    }

    /**
     * Validates the last_invoice for the given instance.
     *
     * @param Instance $class The instance to validate.
     */
    public function validateLastInvoice(&$class)
    {
        if (property_exists($class, 'external')
            && array_key_exists('last_invoice', $class->external)
            && !empty($class->external)
            && is_string($class->external)
        ) {
            $date = new \Datetime($class->external['last_invoice']);
            $class->external['last_invoice'] = $date->format('Y-m-d H:i:s');
        }
    }
}
