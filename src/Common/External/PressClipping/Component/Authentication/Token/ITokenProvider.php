<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\PressClipping\Component\Authentication\Token;

interface ITokenProvider
{
    /**
     * Returns the access token.
     *
     * @return mixed The access token if token exists or null if token does not
     *               exist.
     */
    public function getAccessToken();

    /**
     * Returns the token provider namespace.
     *
     * @return mixed The token provider namespace.
     */
    public function getNamespace();

    /**
     * Returns the refresh token.
     *
     * @return mixed The refresh token if token exists or null if token does not
     *               exist.
     */
    public function getRefreshToken();

    /**
     * Checks if there is a valid access token.
     *
     * @return boolean True if there is a valid access token. False otherwise.
     */
    public function hasAccessToken();

    /**
     * Checks if there is a valid refresh token.
     *
     * @return boolean True if there is a valid access token. False otherwise.
     */
    public function hasRefreshToken();

    /**
     * Sets the access token.
     *
     * @param string $token The access token.
     * @param string $ttl   The miliseconds before access token expires.
     *
     * @return TokenProvider The current token provider.
     */
    public function setAccessToken($token, $ttl);

    /**
     * Sets the token provider namespace.
     *
     * @param string $namespace The token provider namespace.
     *
     * @return TokenProvider The current token provider.
     */
    public function setNamespace($token);

    /**
     * Sets the refresh token.
     *
     * @param string $token The refresh token.
     *
     * @return TokenProvider The current token provider.
     */
    public function setRefreshToken($token);
}
