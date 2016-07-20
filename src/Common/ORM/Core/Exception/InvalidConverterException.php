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
 * Exception thrown when the requested converter does not exist.
 */
class InvalidConverterException extends \Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $entity    The entity name.
     * @param string $converter The converter name.
     */
    public function __construct($entity, $converter = null)
    {
        $message = _('No converters found for "%s"');
        $message = sprintf($message, $entity);

        if (!empty($converter)) {
            $message = _('The converter "%s" for "%s" does not exist');
            $message = sprintf($message, $converter, $entity);
        }

        parent::__construct($message);
    }
}
