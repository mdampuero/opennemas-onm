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

use Common\Core\Component\Exception\Translator\InvalidTranslationException;
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

        $from = empty($from) ? $this->from : $from;
        $to   = empty($to) ? $this->to : $to;

        if (strpos($from, '_')) {
            $from = substr($from, 0, strpos($from, '_'));
        }

        if (strpos($to, '_')) {
            $to = substr($to, 0, strpos($to, '_'));
        }

        $client = $this->getClient();

        if (empty($client)
            || empty($from)
            || empty($to)
            || empty($this->translator)
        ) {
            throw new \RuntimeException();
        }

        try {
            $response = $client->call('traducir', [
                'tradutor'  => $this->translator,
                'direccion' => "{$from}-{$to}",
                'tipo'      => 'htmlu',
                'cadea'     => $str
            ]);
        } catch (\Exception $e) {
            throw new InvalidTranslationException();
        }

        if (strpos($response, 'lt-proc') === 0 || $response === false) {
            throw new InvalidTranslationException();
        }

        return $response;
    }

    /**
     * Returns a SOAP client.
     *
     * @return \SoapClient The SOAP client.
     */
    protected function getClient()
    {
        if (empty($this->client) && !empty($this->url)) {
            $this->client = new \nusoap_client($this->url, true);
        }

        return $this->client;
    }
}
