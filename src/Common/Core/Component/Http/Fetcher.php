<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Http;

/**
 * The Fetcher class retrieves contents from http endpoints.
 */
class Fetcher
{
    /**
     * Get content from a given url using http digest auth and curl
     *
     * @param $url the http server url
     *
     * @return $content the content from this url
     *
     */
    public function getContentFromUrlWithDigestAuth($url, $username, $password)
    {
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => true,
            CURLOPT_VERBOSE        => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,    // for https
            CURLOPT_USERPWD        => $username . ":" . $password,
            CURLOPT_HTTPAUTH       => CURLAUTH_DIGEST,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $httpCode         = '';
        $maxRedirects     = 0;
        $redirectsAllowed = 3;

        do {
            try {
                $content = curl_exec($ch);

                // validate CURL status
                if (curl_errno($ch)) {
                    throw new \Exception(curl_error($ch), 500);
                }

                // validate HTTP status code (user/password credential issues)
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode != 200) {
                    throw new \Exception("Response with Status Code [" . $httpCode . "].", 500);
                }
                $response = explode("\r\n\r\n", $content);
                $content  = $response[count($response) - 1];

                if ($httpCode == 301 || $httpCode == 302) {
                    $matches = [];
                    preg_match('/(Location:|URI:)(.*?)\n/', $response[0], $matches);
                    $url = trim(array_pop($matches));
                }
            } catch (\Exception $ex) {
                if ($ch != null) {
                    curl_close($ch);
                }
                return false;
            }

            $maxRedirects++;
        } while ($httpCode == 302 ||
            $httpCode == 301 ||
            $maxRedirects > $redirectsAllowed
        );

        curl_close($ch);

        return $content;
    }
}
