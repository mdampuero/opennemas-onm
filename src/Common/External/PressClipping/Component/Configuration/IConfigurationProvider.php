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

interface IConfigurationProvider
{
    /**
     * Returns the WebPush configuration.
     *
     * @return array The WebPush configuration.
     */
    public function getConfiguration();

    /**
     * Updates the WebPush configuration.
     *
     * @param array $config The WebPush configuration.
     */
    public function setConfiguration($config);
}
