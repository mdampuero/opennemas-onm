<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Criteria;

class OQLTranslator
{
    protected $splitters = [ 'order by', 'limit', 'page', 'offset' ];

    public
    /**
     * Translates an OQL query to a MySQL query.
     *
     * @param string $query The OQL query.
     *
     * @return string The MySQL query.
     */
    public function translate($query)
    {

    }
}
