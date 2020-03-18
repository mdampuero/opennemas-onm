<?php

namespace Common\Data\Core;

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
    public function __construct($data = [], $env = 'prod')
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
