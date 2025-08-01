<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\EventDispatcher;

use Symfony\Component\EventDispatcher\GenericEvent;

class Event extends GenericEvent
{
    /**
     * The event response.
     *
     * @var mixed
     */
    protected $response;

    /**
     * Returns the response.
     *
     * @return mixed The response.
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the value of the response.
     *
     * @param mixed $response The response value.
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
}
