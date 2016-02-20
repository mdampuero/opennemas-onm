<?php

namespace Common\ORM\Core\Exception;

class InvalidCriteriaException extends \Exception
{
    /**
     * Creates a new exception from criteria, source and error message.
     *
     * @param integer $criteria The criteria.
     * @param string  $source   The source name.
     * @param string  $error    The error message.
     */
    public function __construct($criteria, $source, $error = null)
    {
        $message = _('The criteria (%s) is not valid in %s');

        if (!empty($error)) {
            $message .= ': ' . $error;
        }

        parent::__construct(sprintf($message, @serialize($criteria), $source));
    }
}
