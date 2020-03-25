<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Model\Database\Repository;

use Opennemas\Orm\Database\Repository\BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    protected function refresh($ids)
    {
        return parent::refresh($ids);
    }
}
