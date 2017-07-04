<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Translator\Google;

use Common\Core\Component\Exception\Translator\InvalidTranslationException;
use Common\Core\Component\Translator\Translator;
use GuzzleHttp\Client;

/**
 * The `GoogleTranslator` class defines methods to translate strings by using
 * the Google Translate service.
 */
class GoogleTranslator extends Translator
{
    /**
     * The URL to Google Cloud Translation API
     *
     * @var string
     */
    protected $url = 'https://translation.googleapis.com/language/translate/v2'
        . '?format=html&key=%s&q=%s&source=%s&target=%s';

    /**
     * {@inheritdoc}
     */
    public function getRequiredParameters()
    {
        return [ 'key' => _('API key') ];
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
        $url    = sprintf($this->url, $this->key, $str, $from, $to);
        $client = $this->getClient();

        if (empty($client)
            || empty($from)
            || empty($to)
        ) {
            throw new \RuntimeException();
        }

        try {
            $response = $client->get($url);
        } catch (\Exception $e) {
            throw new InvalidTranslationException();
        }

        $body = $response->getBody();
        $body = json_decode($body, true);

        if (empty($body)
            || !array_key_exists('data', $body)
            || !array_key_exists('translations', $body['data'])
            || count($body['data']['translations']) === 0
        ) {
            throw new InvalidTranslationException();
        }

        $translated = array_shift($body['data']['translations']);

        return $translated['translatedText'];
    }

    /**
     * Returns a new Guzzle client.
     *
     * @return Client The new Guzzle client.
     */
    protected function getClient()
    {
        if (empty($this->client)) {
            $this->client = new Client();
        }

        return $this->client;
    }
}
