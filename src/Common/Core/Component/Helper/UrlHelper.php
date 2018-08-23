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

/**
 * The UrlHelper service provides methods to parse and generate URLs.
 */
class UrlHelper
{
    /**
     * Checks if the current URI is for frontend.
     *
     * @param string $uri The current URI.
     *
     * @return boolean True if the current URI is for frontend. False otherwise.
     */
    public function isFrontendUri($uri)
    {
        $ignore = [
            '_profiler',
            '_wdt',
            'admin',
            'api',
            'asset',
            'build\/assets',
            'content\/share-by-email',
            'manager',
            'ws',
        ];

        return !preg_match('/^(' . implode('|', $ignore) . ')/', trim($uri, '/'));
    }

    /**
     * Parses and returns the list of parts from a URL.
     *
     * @param string $url The URL to parse.
     *
     * @return array The list of parts.
     */
    public function parse($url)
    {
        return parse_url($url);
    }

    /**
     * Generates an URL basing on an array of URL parts.
     *
     * @param array $data The URL parts.
     *
     * @return string The generated URL.
     */
    public function unparse($data)
    {
        $scheme   = isset($data['scheme']) ? $data['scheme'] . '://' : '';
        $host     = isset($data['host']) ? $data['host'] : '';
        $port     = isset($data['port']) ? ':' . $data['port'] : '';
        $user     = isset($data['user']) ? $data['user'] : '';
        $pass     = isset($data['pass']) ? ':' . $data['pass'] : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($data['path']) ? $data['path'] : '';
        $query    = isset($data['query']) ? '?' . $data['query'] : '';
        $fragment = isset($data['fragment']) ? '#' . $data['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
