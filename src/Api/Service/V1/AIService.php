<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

use Api\Exception\ApiException;
use Api\Exception\GetListException;
use Common\Model\Entity\Ai;

class AIService extends OrmService
{
    /**
     * Retrieves a list of entries filtered by the specified month and year.
     *
     * If the month or year is not provided, the function defaults to the current month and year.
     *
     * @param int|null $month The month to filter by (1-12). Defaults to the current month if not provided.
     * @param int|null $year The year to filter by. Defaults to the current year if not provided.
     * @return array The list of entries filtered by the specified month and year.
     * @throws GetListException If the provided month or year is invalid.
     */
    public function getListByMonthy($month = null, $year = null)
    {
        $currentMonth = date('m');
        $currentYear  = date('Y');

        $month = $month ?? $currentMonth;
        $year  = $year ?? $currentYear;

        if (!checkdate($month, 1, $year)) {
            throw new GetListException('Invalid month or year', 400);
        }

        $startDate = sprintf('%04d-%02d-01 00:00:00', $year, $month);
        $endDate   = sprintf('%04d-%02d-%02d 23:59:59', $year, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year));

        $oql = 'date >= "' . $startDate . '" and date <= "' . $endDate . '"';

        return $this->getList($oql);
    }
}
