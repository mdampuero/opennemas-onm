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

class EntityNotFoundException extends \Exception
{
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

        parent::__construct(sprintf($message, $entity, $id));
    }
}
