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
 * Exception thrown when the requested metadata does not exist.
 */
class InvalidMetadataException extends \Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $entity The entity name.
     */
    public function __construct($entity)
    {
        $message = _('No metadata found for "%s"');
        $message = sprintf($message, $entity);

        parent::__construct($message);
    }
}
