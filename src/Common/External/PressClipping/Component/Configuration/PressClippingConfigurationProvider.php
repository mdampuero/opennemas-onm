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

class PressClippingConfigurationProvider implements ConfigurationProvider
{
    private $authUri = 'v1/authenticate';

    private $isTokenRequired = true;

    private $dataset;

    /**
     * Initializes the PressClippingConfigurationProvider
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
            'Content-Type' => 'application/json',
            'pressClippingApiKey' => 'asdasdasdasd', // $this->dataset->get('pressclipping_apikey', ''),
            'pressClippingAuthToken' => 'sadasdasdasdasdasd', //$this->dataset->get('pressclipping_token', ''),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthUri()
    {
        return $this->authUri;
    }

    /**
     * {@inheritdoc}
     */
    public function isTokenRequired()
    {
        return $this->isTokenRequired;
    }

    public function getAuthHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->dataset->get('pressclipping_token', ''),
        ];
    }

    public function getConfigParams()
    {
        return [
            'headers' => $this->getAuthHeaders(),
            'form_params' => $this->getConfiguration()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration($config)
    {
        if (empty($config)) {
            $this->dataset->delete('pressclipping_apikey');
            $this->dataset->delete('pressclipping_token');
            return;
        }

        $this->dataset->set('pressclipping_apikey', $config['pressclipping_apikey']);
        $this->dataset->set('pressclipping_token', $config['pressclipping_token']);
    }
}
