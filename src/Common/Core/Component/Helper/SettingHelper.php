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
     * @param string $format The logo format (site_logo|mobile_logo|favico|sn_default_img).
     *
     * @return object The logo image object.
     */
    public function getLogo($format = 'site_logo') : ?object
    {
        $settings = [
            'site_logo'      => 'site_logo',
            'mobile_logo'    => 'mobile_logo',
            'favico'         => 'favico',
            'sn_default_img' => 'sn_default_img',
        ];

        if (!array_key_exists($format, $settings) || !$this->logoEnabled()) {
            return null;
        }

        $logo = $this->ds->get($settings[$format]);

        return $this->container
            ->get('core.helper.content')
            ->getContent($logo, 'photo');
    }

    /**
     * Checks if the logo for the provided format is configured.
     *
     * @param string $format The logo format (site_logo|mobile_logo|favico|sn_default_img).
     *
     * @return bool True if the logo for the provided format is configured. False
     *              otherwise.
     */
    public function hasLogo($format = 'site_logo') : bool
    {
        return !empty($this->getLogo($format));
    }

    /**
     * Checks if the logo for the provided format is enabled.
     * @return bool True if the logo for the provided format is enabled. False
     *              otherwise.
     */
    protected function logoEnabled() : bool
    {
        return $this->ds->get('logo_enabled');
    }
}
