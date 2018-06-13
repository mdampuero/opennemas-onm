<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\ActOn\Component\Configuration;

interface ConfigurationProvider
{
    /**
     * Returns the Act-On configuration.
     *
     * @return array The Act-On configuration.
     */
    public function getConfiguration();

    /**
     * Updates the Act-On configuration.
     *
     * @param array $config The Act-On configuration.
     */
    public function setConfiguration($config);
}
