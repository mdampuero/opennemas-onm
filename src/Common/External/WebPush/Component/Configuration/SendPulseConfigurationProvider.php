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

class SendPulseConfigurationProvider implements ConfigurationProvider
{
    private $authUri = 'oauth/access_token';

    private $isTokenRequired = false;

    private $dataset;
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
            "grant_type" => "client_credentials",
            "client_id" => $this->dataset->get('webpush_apikey', ''),
            "client_secret" => $this->dataset->get('webpush_token', '')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthUri()
    {
        return $this->authUri;
    }

    public function getAuthHeaders()
    {
        return [
            "grant_type" => "client_credentials",
            "client_id" => "237b4af9c99d0f89bdbd876dcd5a0000",
            "client_secret" => "a99e7d506d3701c5c04de3db1913eeee"
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function isTokenRequired()
    {
        return $this->isTokenRequired;
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
