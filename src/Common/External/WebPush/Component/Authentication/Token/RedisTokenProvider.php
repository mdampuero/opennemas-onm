<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\WebPush\Component\Authentication\Token;

class RedisTokenProvider implements TokenProvider
{
    /**
     * The redis connection.
     *
     * @var Connection
     */
    protected $conn;

    /**
     * The token provider namespace.
     *
     * @var string
     */
    protected $namespace;

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
     * Changes and recovers cache namespace before and after executing a
     * function given as callback.
     *
     * @param callable $callback The function to execute.
     * @param array    $args     The list of arguments for the callable.
     *
     * @return mixed The result of the callback action.
     */
    public function execute($callback, $args)
    {
        $namespace = $this->conn->getNamespace();

        $this->conn->setNamespace($this->namespace);

        $result = call_user_func_array($callback, $args);

        $this->conn->setNamespace($namespace);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken()
    {
        return $this->execute(function () {
            return $this->conn->get('sendpulse-access-token');
        }, []);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getRefreshToken()
    {
        return $this->execute(function () {
            return $this->conn->get('sendpulse-refresh-token');
        }, []);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAccessToken()
    {
        return $this->execute(function () {
            return $this->conn->exists('sendpulse-access-token');
        }, []);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRefreshToken()
    {
        return $this->execute(function () {
            return $this->conn->exists('sendpulse-refresh-token');
        }, []);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccessToken($token, $ttl)
    {
        return $this->execute(function ($a, $b) {
            $this->conn->set('sendpulse-access-token', $a, $b);

            return $this;
        }, [ $token, $ttl ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRefreshToken($token)
    {
        return $this->execute(function ($a) {
            $this->conn->set('sendpulse-refresh-token', $a);

            return $this;
        }, [ $token ]);
    }
}
