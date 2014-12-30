<?php
/**
 * Defines the Onm\Import\Synchronizer\LockException class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Import
 */
namespace Onm\Import\Synchronizer;

/**
 * Exception to handle concurrent synchronizations and locks.
 *
 * @package    Onm_Import
 */
class LockException extends \Exception
{
}
