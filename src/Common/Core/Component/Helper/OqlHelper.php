<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

/**
 * The OqlHelper service converts an OQL query to SQL values.
 */
class OqlHelper
{
    /**
     * The OQL to parse.
     *
     * @var string
     */
    protected $oql;

    /**
     * Returns an array with the criteria, the order, the items per page and the
     * page to use with old repositories basing on an OQL query.
     *
     * @param string $oql The OQL query.
     *
     * @return array Description
     */
    public function getFiltersFromOql($oql = '')
    {
        if (empty($oql)) {
            return [ '', '', 10, 1 ];
        }

        $this->oql = $oql;

        $epp    = $this->getEpp();
        $offset = $this->getOffset();
        $order  = $this->getOrder();
        $page   = ($offset / $epp) + 1;

        // Replace like operator
        $this->oql = preg_replace('/\s+~\s+/', ' LIKE ', $this->oql);

        return [ $this->oql, $order, $epp, $page ];
    }

    /**
     * Returns the number of items per page and updates the OQL query.
     *
     * @return integer The number of items per page.
     */
    protected function getEpp()
    {
        preg_match_all(
            '/limit\s+(?<epp>\d+)/',
            $this->oql,
            $matches
        );

        if (array_key_exists('epp', $matches) && !empty($matches['epp'])) {
            $this->oql = trim(str_replace($matches[0], '', $this->oql));

            return (int) $matches['epp'][0];
        }

        return 10;
    }

    /**
     * Returns the offset and updates the OQL query.
     *
     * @return interger The offset.
     */
    protected function getOffset()
    {
        preg_match_all(
            '/offset\s+(?<offset>\d+)/',
            $this->oql,
            $matches
        );

        if (array_key_exists('offset', $matches)
            && !empty($matches['offset'])
        ) {
            $this->oql = trim(str_replace($matches[0], '', $this->oql));

            return (int) $matches['offset'][0];
        }

        return 0;
    }

    /**
     * Returns the order condition and updates the OQL query.
     *
     * @return string The order condition.
     */
    protected function getOrder()
    {
        preg_match_all('/order by\s+[a-z0-9_]+\s+(asc|desc)/', $this->oql, $matches);

        if (!empty($matches) && !empty($matches[0])) {
            $this->oql = trim(str_replace($matches[0], '', $this->oql));

            return preg_replace('/order\s*by\s*/', '', $matches[0][0]);
        }

        return '';
    }
}
