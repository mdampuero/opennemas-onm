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
 * Exception thrown when the requested dataset does not exist.
 */
class InvalidDataSetException extends \Exception
{
    /**
     * Initializes the exception.
     *
     * @param string $entity    The entity name.
     * @param string $dataset The dataset name.
     */
    public function __construct($entity, $dataset = null)
    {
        $message = _('No datasets found for "%s"');
        $message = sprintf($message, $entity);

        if (!empty($dataset)) {
            $message = _('The dataset "%s" for "%s" does not exist');
            $message = sprintf($message, $dataset, $entity);
        }

        parent::__construct($message);
    }
}
