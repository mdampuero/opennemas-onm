<?php

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Instance;

/**
 * Helper class for link related utilities.
 */
class LinkHelper
{
    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * Initializes the helper.
     *
     * @param Instance $instance The current instance.
     */
    public function __construct(Instance $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Validates menu items of type external.
     *
     * @param array $menuItems The menu items to validate.
     *
     * @return array The list of invalid links.
     */
    public function validateExternalLinks(array $menuItems): array
    {
        $errors         = [];
        $allowedSchemes = ['whatsapp', 'mailto', 'tel'];

        foreach ($menuItems as $item) {
            if (($item['type'] ?? '') !== 'external') {
                continue;
            }

            $link = $item['link_name'] ?? $item['link'] ?? '';

            if ($link === '#' || strpos($link, '#') === 0) {
                continue;
            }

            if (preg_match('#^([a-z][a-z0-9+\.-]*):#i', $link, $matches)) {
                $scheme = strtolower($matches[1]);

                if (in_array($scheme, $allowedSchemes, true)) {
                    continue;
                }

                if (!preg_match('#^[a-z][a-z0-9+\.-]*://#i', $link)) {
                    $errors[] = $link;
                    continue;
                }
            } elseif (strpos($link, '/') === 0) {
                $link = $this->instance->getBaseUrl(true) . $link;
            } else {
                $errors[] = $link;
                continue;
            }

            $ch = curl_init($link);
            curl_setopt_array($ch, [
                CURLOPT_NOBODY         => true,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 
                (KHTML, like Gecko) Chrome/126.0 Safari/537.36',
            ]);

            curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err  = curl_errno($ch);

            if ($code === 405) {
                curl_setopt($ch, CURLOPT_NOBODY, false);
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $err  = curl_errno($ch);
            }

            curl_close($ch);

            if ($err || ($code >= 404 && $code !== 403) || $code === 0) {
                $errors[] = $link;
            }
        }

        return $errors;
    }
}
