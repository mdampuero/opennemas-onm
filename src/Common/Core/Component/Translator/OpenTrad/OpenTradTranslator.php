<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Translator\OpenTrad;

use Common\Core\Component\Translator\Translator;

/**
 * The `OpenTradTranslator` defines methods to translate strings by using the
 * OpenTrad translation service.
 */
class OpenTradTranslator extends Translator
{
    /**
     * The SOAP client.
     *
     * @var \SoapClient
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    public function getRequiredParameters()
    {
        return [
            'translator' => _('Translator'),
            'url'        => 'URL'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function translate($str)
    {
        $client = $this->getClient();

        return $client->__soapCall('traducir', [
            'traductor' => $this->translator,
            'direccion' => "{$this->from}-{$this->to}",
            'tipo'      => 'htmlu',
            'cadena'    => $str
        ]);
    }

    /**
     * Returns a SOAP client.
     *
     * @return \SoapClient The SOAP client.
     *
     * @codeCoverageIgnore
     */
    protected function getClient()
    {
        if (empty($this->client)) {
            $this->client = new \SoapClient($this->url);
        }

        return $this->client;
    }
}
