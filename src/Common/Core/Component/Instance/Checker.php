<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Instance;

use Onm\Exception\InstanceAlreadyExistsException;

class Checker
{
    /**
     * The EntityManager service.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Initializes the InstanceChecked.
     *
     * @param \Repository\EntityManager $em The EntityManager service.
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Validates an instance according to its class definition.
     *
     * @param \Common\ORM\Entity\Instance $class The instance to validate.
     *
     * @return void
     */
    public function check(&$instance)
    {
        $this->fixInternalName($instance);
        $this->validateDomains($instance);
    }

    /**
     * Fixes the internal_name for the given instance.
     *
     * @param Instance $instance The instance.
     */
    public function fixInternalName(&$instance)
    {
        if (empty($instance->internal_name) && !empty($instance->domains)) {
            $instance->internal_name = explode('.', $instance->domains[0]);
            $instance->internal_name = array_shift($instance->internal_name);
        }

        if (empty($instance->internal_name)) {
            $instance->internal_name = uniqid();
        }

        $instance->internal_name = strtolower($instance->internal_name);

        $oql = sprintf('internal_name regexp "^%s[0-9]*$"', $instance->internal_name);

        if (!empty($instance->id)) {
            $oql .= sprintf(' and id != "%s"', $instance->id);
        }

        $count = $this->em->getRepository('Instance')->countBy($oql);

        if (!empty($count)) {
            $instance->internal_name .= $count;
        }
    }

    /**
     * Validates the domains for the given instance.
     *
     * @param \Common\ORM\Entity\Instance $instance The instance.
     *
     * @return void
     *
     * @throws InstanceAlreadyExistsException
     */
    public function validateDomains(&$instance)
    {
        if (empty($instance->domains)) {
            throw new \InvalidArgumentException(
                "The property 'domains' of 'Instance' cannot be empty"
            );
        }

        $placeholder = 'domains regexp "^%s|,\s*%s\s*,|,\s*%s$"';
        $oql         = [];

        foreach ($instance->domains as $domain) {
            $oql[] = sprintf($placeholder, $domain, $domain, $domain);
        }

        $oql = implode(' or ', $oql);
        $i   = null;

        try {
            $i = $this->em->getRepository('Instance')->findOneBy($oql);
        } catch (\Exception $e) {
            return;
        }

        if (!empty($instance) && !empty($i) && $instance->id != $i->id) {
            throw new InstanceAlreadyExistsException();
        }
    }
}
