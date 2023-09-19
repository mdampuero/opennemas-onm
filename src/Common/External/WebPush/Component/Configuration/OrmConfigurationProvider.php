<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\WebPush\Component\Configuration;

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
        return [
            'webpushrKey' => $this->dataset->get('webpush_apikey', ''),
            'webpushrAuthToken' => $this->dataset->get('webpush_token', ''),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration($config)
    {
        if (empty($config)) {
            $this->dataset->delete('webpush_apikey');
            $this->dataset->delete('webpush_token');
            return;
        }

        $this->dataset->set('webpush_apikey', $config['webpush_apikey']);
        $this->dataset->set('webpush_token', $config['webpush_token']);
    }
}
