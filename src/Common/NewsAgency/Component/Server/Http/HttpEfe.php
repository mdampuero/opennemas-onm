<?php
/*
 * This file is part of the onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Server\Http;

use Common\NewsAgency\Component\Server\Server;

/**
 * Synchronize local folders with an external XML-based source server.
 */
class HttpEfe extends Http
{
    /**
     * {@inheritdoc}
     */
    public function checkParameters() : bool
    {
        if (array_key_exists('url', $this->params)
            && preg_match('@efeservicios@', $this->getUrl())
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContentFromUrl(string $url) : ?string
    {
        $auth = '';
        if (array_key_exists('username', $this->params)
            && array_key_exists('password', $this->params)
        ) {
            $auth = $this->params['username'] . ':' . $this->params['password'];
        }

        $ch                  = curl_init();
        $httpCode            = 0;
        $maxRedirects        = 0;
        $maxRedirectsAllowed = 3;

        do {
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
                CURLOPT_USERPWD        => $auth,
            ]);

            $content  = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 301 || $httpCode == 302 || $httpCode == 307) {
                $url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

                continue;
            }

            if ($httpCode == 401 || $httpCode == 403) {
                return null;
            }

            $body = $content;

            $maxRedirects++;
        } while ($httpCode == 307 || $httpCode == 302 || $httpCode == 301 || $maxRedirects > $maxRedirectsAllowed);

        curl_close($ch);

        return $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoteFiles() : Server
    {
        $content = $this->getContentFromUrl($this->getUrl());

        if (!$content) {
            throw new \Exception(sprintf(
                _('Can\'t connect to server %s. Please check your connection details.'),
                $this->params['name']
            ));
        }

        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOERROR);

        if (empty($xml)) {
            return $this;
        }

        $files = $xml->xpath('//elemento');

        foreach ($files as $value) {
            $this->remoteFiles[] = [
                'filename' => (string) $value->attributes()->id . '.xml',
                'url'      => preg_replace([ '/\n/', '/\s+/' ], '', $value[0])
            ];
        }

        return $this;
    }
}
