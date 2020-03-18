<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Loader;

use Common\Cache\Core\CacheManager;
use Common\ORM\Core\EntityManager;
use Common\Model\Entity\Instance;
use Symfony\Component\HttpFoundation\Request;

class InstanceLoader
{
    /**
     * The Cache service.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The EntityManager service.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * The loaded instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * Initializes the InstanceLoader
     *
     * @param CacheManager  $cache The cache service.
     * @param EntityManager $em    The entity manager service.
     */
    public function __construct(CacheManager $cm, EntityManager $em)
    {
        $this->cache = $cm->getConnection('manager');
        $this->em    = $em;
    }

    /**
     * Returns the current loaded Instance.
     *
     * @return Instance The current loaded Instance.
     */
    public function getInstance() : ?Instance
    {
        return $this->instance;
    }

    /**
     * Loads an instance basing on the domain and the requested URI.
     *
     * @param string $domain The requested domain.
     * @param string $uri    The requested URI.
     *
     * @return InstanceLoader The current InstanceLoader.
     */
    public function loadInstanceByDomain(string $domain, string $uri) : InstanceLoader
    {
        if ($this->isManagerUri($uri)) {
            $this->instance = $this->getManagerInstance();
            return $this;
        }

        $domain   = preg_replace('/\.+$/', '', $domain);
        $instance = $this->cache->exists($domain)
            ? $this->cache->get($domain)
            : null;

        if ($this->isValid($instance, $domain)) {
            $this->instance = $instance;
            return $this;
        }

        $oql = sprintf(
            'domains regexp "^%s($|,)|,\s*%s\s*,|(^|,)\s*%s$"',
            $domain,
            $domain,
            $domain
        );

        $this->instance = $this->em->getRepository('Instance')->findOneBy($oql);

        $this->cache->set($domain, $this->instance);

        return $this;
    }

    /**
     * Returns an instance basing on the internal name.
     *
     * @param string $internalName The instance internal name.
     *
     * @return The instance.
     */
    public function loadInstanceByName($name) : InstanceLoader
    {
        if ($name === 'manager') {
            $this->instance = $this->getManagerInstance();
            return $this;
        }

        $oql = sprintf('internal_name = "%s"', $name);

        $this->instance = $this->em->getRepository('Instance')->findOneBy($oql);

        return $this;
    }

    /**
     * Changes the current instance in the loader.
     *
     * @param Instance $instance The new instance.
     */
    public function setInstance(Instance $instance) : void
    {
        $this->instance = $instance;
    }

    /**
     * Returns a pseudo-instance for the manager.
     *
     * @return Instance The manager instance.
     */
    protected function getManagerInstance() : Instance
    {
        return new Instance([
            'activated'     => true,
            'internal_name' => 'manager',
            'settings'      => [
                'BD_DATABASE'   => 'onm-instances',
                'TEMPLATE_USER' => 'manager'
            ],
            'activated_modules' => [],
        ]);
    }

    /**
     * Checks if the current request URI is for a regular instance or for the
     * opennemas manager.
     *
     * @param string $uri The requested URI.
     *
     * @return bool True if the request if for manager. False otherwise.
     */
    protected function isManagerUri(string $uri) : bool
    {
        return preg_match('@^\/(manager|_wdt|framework)@', $uri);
    }

    /**
     * Checks if the instance is valid basing on the requested domain.
     *
     * Note: This is only needed to prevent errors while loading instance from
     *       cache.
     *
     * @param mixed  $instance The instance to check.
     * @param string $domain   The domain.
     *
     * @return boolean true if the instance is valid
     */
    protected function isValid($instance, string $domain) : bool
    {
        return !empty($instance)
            && $instance instanceof Instance
            && in_array($domain, $instance->domains);
    }
}
