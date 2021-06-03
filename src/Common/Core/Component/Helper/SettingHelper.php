<?php

namespace Common\Core\Component\Helper;

use Symfony\Component\DependencyInjection\Container;

/**
* Perform searches in Database Settings data
*/
class SettingHelper
{
    /**
     * The services container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Initializes the SettingLogoHelper.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->em        = $container->get('orm.manager');
        $this->ds        = $this->em->getDataSet('Settings', 'instance');
    }

    /**
     * Returns the logo item
     *
     * @param string $format The logo format (default|simple|favico|embed).
     *
     * @return object The logo image object.
     */
    public function getLogo($format = 'default') : ?object
    {
        if (!$this->isLogoEnabled()) {
            return null;
        }

        $format = 'logo_' . $format;

        $logo = $this->ds->get($format);

        if (empty($logo)) {
            return null;
        }

        return $this->container
            ->get('core.helper.content')
            ->getContent($logo, 'photo');
    }

    /**
     * Checks if the logo for the provided format is configured.
     *
     * @param string $format The logo format (default|simple|favico|embed)..
     *
     * @return bool True if the logo for the provided format is configured. False
     *              otherwise.
     */
    public function hasLogo($format = 'default') : bool
    {
        return !empty($this->getLogo($format));
    }

    /**
     * Checks if the logo for the provided format is enabled.
     * @return bool True if the logo for the provided format is enabled. False
     *              otherwise.
     */
    protected function isLogoEnabled() : bool
    {
        return boolval($this->ds->get('logo_enabled'));
    }
}
