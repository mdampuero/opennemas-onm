<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Serialize\Serializable;

interface CsvSerializable
{
    /**
     * Returns all content information when converted to CSV.
     *
     * @return array The content information.
     */
    public function csvSerialize();
}
