<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Session;

use Common\Model\Entity\Instance;

/**
 * Utility helpers to handle redis session key prefixes.
 */
final class RedisSessionKeyHelper
{
    /**
     * Default redis session prefix used by the php redis extension.
     */
    public const BASE_PREFIX = 'PHPREDIS_SESSION:';

    /**
     * Builds the redis prefix for a given instance.
     */
    public static function buildInstancePrefix(?string $identifier) : string
    {
        $identifier = $identifier !== null ? trim((string) $identifier) : '';

        if ($identifier === '') {
            return self::BASE_PREFIX;
        }

        return self::BASE_PREFIX . '__' . $identifier . '__';
    }

    /**
     * Extracts the best identifier for an instance.
     */
    public static function extractInstanceIdentifier(?Instance $instance) : ?string
    {
        if ($instance === null) {
            return null;
        }

        if (isset($instance->internal_name) && trim((string) $instance->internal_name) !== '') {
            return trim((string) $instance->internal_name);
        }

        if (isset($instance->id) && $instance->id !== null) {
            return (string) $instance->id;
        }

        return null;
    }

    /**
     * Ensures the redis session save path contains the provided prefix.
     */
    public static function applyPrefixToSavePath(string $savePath, string $prefix) : string
    {
        $savePath = trim($savePath);

        if ($savePath === '') {
            return $savePath;
        }

        $parts = explode(',', $savePath);
        foreach ($parts as $index => $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $segments = explode('?', $part, 2);
            $query    = self::parseQueryString($segments[1] ?? '');

            $query['prefix'] = $prefix;

            $segments[1] = self::buildQueryString($query);
            $parts[$index] = implode('?', $segments);
        }

        return implode(',', array_map('trim', $parts));
    }

    /**
     * Normalises a redis pattern to include the expected prefix.
     */
    public static function normalisePatternWithPrefix(string $pattern, string $prefix) : string
    {
        if (strpos($pattern, $prefix) === 0) {
            return $pattern;
        }

        $suffix = self::stripKnownPrefix($pattern);

        return $prefix . $suffix;
    }

    /**
     * Removes the default prefix from the provided key or pattern.
     */
    public static function stripKnownPrefix(string $key) : string
    {
        $regex = sprintf('/^%s(__.+?__)?/i', preg_quote(self::BASE_PREFIX, '/'));

        return (string) preg_replace($regex, '', $key, 1);
    }

    private static function parseQueryString(string $query) : array
    {
        if ($query === '') {
            return [];
        }

        $result = [];
        foreach (explode('&', $query) as $chunk) {
            if ($chunk === '') {
                continue;
            }

            $pair = explode('=', $chunk, 2);
            $key  = $pair[0];
            $value = $pair[1] ?? '';
            $result[$key] = $value;
        }

        return $result;
    }

    private static function buildQueryString(array $params) : string
    {
        $chunks = [];
        foreach ($params as $key => $value) {
            $chunks[] = sprintf('%s=%s', $key, $value);
        }

        return implode('&', $chunks);
    }
}
