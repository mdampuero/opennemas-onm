<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Data;

class DataBuffer extends DataObject
{
    /**
     * The function call buffer.
     *
     * @var array
     */
    protected $buffer = [];

    /**
     * The current environment.
     *
     * @var string
     */
    protected $env;

    /**
     * Initializes the Provider.
     *
     * @param string $env The current environment.
     */
    public function __construct($data = [], $env = 'dev')
    {
        $this->env = $env;

        parent::__construct($data);
    }

    /**
     * Adds a function call to the buffer.
     *
     * @param $method string The method name.
     * @param $params mixed  The method parameters.
     */
    public function addToBuffer($method, $params)
    {
        if ($this->env !== 'prod') {
            $this->buffer[] = [
                'method' => $method,
                'params' => $params,
                'time'   => microtime(true)
            ];
        }
    }

    /**
     * Returns the current buffer.
     *
     * @return array The current buffer.
     */
    public function getBuffer()
    {
        return $this->buffer;
    }
}
