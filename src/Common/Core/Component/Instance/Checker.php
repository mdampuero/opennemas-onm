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
use Onm\Exception\InvalidSubdirectoryException;

class Checker
{
    /**
     * The EntityManager service.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * The Locale component.
     *
     * @var Locale
     */
    protected $locale;

    /**
     * Initializes the InstanceChecked.
     *
     * @param \Repository\EntityManager     $em     The EntityManager service.
     * @param \Common\Core\Component\Locale $locale The locale component.
     */
    public function __construct($em, $locale)
    {
        $this->em     = $em;
        $this->locale = $locale;
    }

    /**
     * Validates an instance according to its class definition.
     *
     * @param \Common\Model\Entity\Instance $class The instance to validate.
     */
    public function check(&$instance)
    {
        $this->fixInternalName($instance);
        $this->validateDomains($instance);
        $this->validateSubdirectory($instance);
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
     * @param \Common\Model\Entity\Instance $instance The instance.
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
            throw new InstanceAlreadyExistsException(_('The instance already exists'), 409);
        }
    }

    /**
     * Validates the subdirectory for the given instance.
     *
     * @param \Common\Model\Entity\Instance $instance The instance.
     *
     * @throws InvalidSubdirectoryException
     */
    public function validateSubdirectory($instance)
    {
        $locales = $this->locale->getSlugs('frontend');

        $locales = array_map(function ($a) {
            return '/' . $a;
        }, array_values($locales));

        if (in_array($instance->subdirectory, $locales)) {
            throw new InvalidSubdirectoryException(
                _('Cannot use a language code as subdirectory'),
                409
            );
        }
    }
}
