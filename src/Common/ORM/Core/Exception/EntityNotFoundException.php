<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Exception;

/**
 * Exception thrown when no entities found basing on an id or criteria.
 */
class EntityNotFoundException extends \Exception
{
    /**
     * Initializes the exception.
     *
     * @param String $entity The entity name.
     * @param String $id     The entity id.
     * @param String $error  The error message.
     */
    public function __construct($entity, $id = '', $error = '')
    {
        $message = _('Unable to find entity of type "%s"');

        if (!empty($id)) {
            $message = _('Unable to find entity of type "%s" with id "%s"');
        }

        if (!empty($id) && is_array($id)) {
            $str = '';
            foreach ($id as $key => $value) {
                $str .= $key . '=' . $value . ',';
            }

            $id = rtrim($str, ',');
        }

        if (!empty($error)) {
            $message .= ' (' . $error . ')';
        }

        parent::__construct(sprintf($message, $entity, $id), 404);
    }
}
