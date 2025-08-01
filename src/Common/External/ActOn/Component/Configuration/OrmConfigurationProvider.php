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

class OrmConfigurationProvider implements ConfigurationProvider
{
    /**
     * Initializes the OrmConfigurationProvider
     *
     * @param EntityManager $em The entity manager.
     */
    public function __construct($em)
    {
        $this->dataset = $em->getDataSet('Settings', 'instance');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->dataset->get('actOn.authentication', []);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration($config)
    {
        if (empty($config)) {
            $this->dataset->delete('actOn.authentication');
            return;
        }

        $this->dataset->set('actOn.authentication', $config);
    }
}
