<?php

namespace Common\ORM\Core\Exception;

class EntityNotFoundException extends \Exception
{
    public function __construct($entity, $id, $error = '')
    {
        $message = _('Unable to find entity of type %s with %s');

        if (is_array($id)) {
            $str = '';
            foreach ($id as $key => $value) {
                $str .= $key . '=' . $value . ',';
            }

            $id = rtrim($str, ',');
        }

        if (!empty($error)) {
            $message .= ': ' . $error;
        }

        parent::__construct(sprintf($message, $entity, $id));
    }
}
