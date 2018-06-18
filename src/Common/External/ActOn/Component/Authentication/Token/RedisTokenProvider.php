<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\ActOn\Component\Authentication\Token;

class RedisTokenProvider implements TokenProvider
{
    /**
     * The redis connection.
     *
     * @var Connection
     */
    protected $conn;

    /**
     * Initializes the RedisTokenProvider.
     *
     * @param Connection $conn The redis connection.
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken()
    {
        return $this->conn->get('acton-access-token');
    }

    /**
     * {@inheritdoc}
     */
    public function getRefreshToken()
    {
        return $this->conn->get('acton-refresh-token');
    }

    /**
     * {@inheritdoc}
     */
    public function hasAccessToken()
    {
        return $this->conn->exists('acton-access-token');
    }

    /**
     * {@inheritdoc}
     */
    public function hasRefreshToken()
    {
        return $this->conn->exists('acton-refresh-token');
    }

    /**
     * {@inheritdoc}
     */
    public function setAccessToken($token, $ttl)
    {
        $this->conn->set('acton-access-token', $token, $ttl);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRefreshToken($token)
    {
        $this->conn->set('acton-refresh-token', $token);

        return $this;
    }
}
