<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Import;

/**
 * Base interface for create resource importers.
 *
 * @package    Onm
 * @subpackage Import
 */
interface ImporterInterface
{
    public function findAll();
    public function findAllBy($params);
}
