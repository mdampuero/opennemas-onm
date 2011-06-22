<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\Synchronizer;

/**
 * Exception to handle concurrent synchronizations and locks.
 *
 * @package    Onm
 * @subpackage Import
 * @author     Fran Dieguez <fran@openhost.es>
 * @version    SVN: $Id: LockException.php 28842 Mér Xuñ 22 16:23:46 2011 frandieguez $
 */
class LockException extends \Exception {

}
