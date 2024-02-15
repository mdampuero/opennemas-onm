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
     * Convert an array keys to int
     *
     * @param array $array The array to convert keys to int
     * @param array $toint The keys to convert
     *
     * @return array array coverted
     */
    public function toInt(array $array, array $toint) :array
    {
        foreach ($toint as $key) {
            if (!empty($array[$key])) {
                if (is_array($array[$key])) {
                    foreach ($array[$key] as $subkey => $value) {
                        $array[$key][$subkey] = (int) $value;
                    }
                } else {
                    $array[$key] = (int) $array[$key];
                }
            }
        }

        return $array;
    }

    /**
     * Convert an array keys to boolean
     *
     * @param array $array The array to convert keys to boolean
     * @param array $tobool The keys to convert
     *
     * @return array array coverted
     */
    public function toBoolean(array $array, array $tobool) :array
    {
        foreach ($tobool as $key) {
            if (!empty($array[$key])) {
                if (is_array($array[$key])) {
                    foreach ($array[$key] as $subkey => $value) {
                        $array[$key][$subkey] = ($value == '1' || $value == 'true') ? true : false;
                    }
                } else {
                    $array[$key] = ($array[$key] == '1' || $array[$key] == 'true' ) ? true : false;
                }
            }
        }

        return $array;
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

    /**
     * Checks if the default Opennemas Google Analytics is disabled.
     * @return bool True if disabled. False otherwise.
     */
    public function isDefaultGADisabled() : bool
    {
        return boolval($this->ds->get('disable_default_ga'));
    }
}
