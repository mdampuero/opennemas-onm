<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\PressClipping\Component\Configuration;

interface ConfigurationProvider
{
    /**
     * Returns the PressClipping configuration.
     *
     * @return array The PressClipping configuration.
     */
    public function getConfiguration();

    /**
     * Updates the PressClipping configuration.
     *
     * @param array $config The PressClipping configuration.
     */
    public function setConfiguration($config);
}
