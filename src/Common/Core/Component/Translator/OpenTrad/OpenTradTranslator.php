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
 * The `OpenTradTranslator` class defines methods to translate strings by using
 * the OpenTrad translation service.
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
    public function translate($str, $from = null, $to = null)
    {
        if (empty($str)) {
            return '';
        }

        $from   = empty($from) ? $this->from : $from;
        $to     = empty($to) ? $this->to : $to;
        $client = $this->getClient();

        if (empty($client)
            || empty($from)
            || empty($to)
            || empty($this->translator)
        ) {
            throw new \RuntimeException();
        }

        return $client->__soapCall('traducir', [
            'traductor' => $this->translator,
            'direccion' => "{$from}-{$to}",
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
        if (empty($this->client) && !empty($this->url)) {
            $this->client = new \SoapClient($this->url);
        }

        return $this->client;
    }
}
