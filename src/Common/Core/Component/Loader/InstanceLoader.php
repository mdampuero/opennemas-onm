<?php

namespace Common\Core\Component\Loader;

use Common\Model\Entity\Instance;
use Opennemas\Cache\Core\CacheManager;
use Opennemas\Orm\Core\EntityManager;
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

        $domain = preg_replace('/\.+$/', '', $domain);
        $match  = preg_match('@(\/[a-zA-Z0-9]+)\/?@', $uri, $subdirectory);

        $subdirectoryMatch = $match ? $subdirectory[1] : '';

        if ($this->cache->exists($domain)) {
            $this->instance = $this->getInstanceFromCache($domain, $subdirectoryMatch);

            if (!$this->isValid($this->instance, $domain)) {
                throw new \Exception();
            }

            return $this;
        }

        $oql = sprintf(
            'domains regexp "^%s($|,)|,\s*%s\s*,|(^|,)\s*%s$"',
            $domain,
            $domain,
            $domain,
        );

        $instances = $this->em->getRepository('Instance')->findBy($oql);

        if (empty($instances)) {
            throw new \Exception();
        }

        $this->cache->set($domain, $instances);

        $this->instance = $this->getInstanceFromCache($domain, $subdirectoryMatch);

        if (!$this->isValid($this->instance, $domain)) {
            throw new \Exception();
        }

        return $this;
    }

    /**
     * Loads an instance basing on the domain and the requested URI.
     *
     * @param string $domain               The requested domain.
     * @param string $subdirectoryMatch    The subdirectory match
     *
     * @return mixed The current InstanceLoader.
     */
    protected function getInstanceFromCache($domain, $subdirectoryMatch)
    {
        $instances = $this->cache->get($domain);
        $instances = is_array($instances) ? $instances : [ $instances ];

        if (count($instances) == 1) {
            return array_pop($instances);
        }

        $subInstance = array_filter($instances, function ($instance) use ($subdirectoryMatch) {
            return $instance->getSubdirectory() == $subdirectoryMatch;
        });

        if (!empty($subInstance)) {
            $instance = array_pop($subInstance);

            return $instance;
        }

        return null;
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

        // Check for valid instance internal name
        if ($this->instance->internal_name !== $name) {
            throw new \Exception();
        }

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
