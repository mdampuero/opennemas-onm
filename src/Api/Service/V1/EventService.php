<?php

namespace Api\Service\V1;

class EventService extends ContentService
{
    public const QUICK_FILTER_TODAY = '__TODAY__';
    public const QUICK_FILTER_TOMORROW = '__TOMORROW__';
    public const QUICK_FILTER_THIS_WEEK = '__THIS_WEEK__';

    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        list($criteria, $order, $epp, $page) = $this->container
            ->get('core.helper.oql')
            ->getFiltersFromOql($oql);

        $oql = $this->getOqlForList($oql);

        $cleanCriteriaTag = preg_replace('/and tag\\s*=\\s*\"?([0-9]*)\"?\\s*/', '', $criteria);
        preg_match('/tag\\s*=\\s*\"?([0-9]*)\"?\\s*/', $criteria, $matches);

        $tagJoin = '';
        if (!empty($matches)) {
            $tagJoin = sprintf(
                ' inner join contents_tags on 
                    contents.pk_content = contents_tags.content_id and 
                    contents_tags.tag_id in (%s) ',
                $matches[1]
            );
            $criteria = $cleanCriteriaTag;
        }

        $cleanCriteriaCat = preg_replace('/and category_id\\s*=\\s*\"?([0-9]*)\"?\\s*/', '', $criteria);
        preg_match('/category_id\\s*=\\s*\"?([0-9]*)\"?\\s*/', $criteria, $matches);

        $categoryJoin = '';
        if (!empty($matches)) {
            $categoryJoin = sprintf(
                ' inner join content_category on 
                    contents.pk_content = content_category.content_id and 
                    content_category.category_id in (%s) ',
                $matches[1]
            );
            $criteria = $cleanCriteriaCat;
        }

        if (!empty($criteria)) {
            $criteria = $this->replaceEventStartDateQuickFilters($criteria);
            $criteria = str_replace(
                ['event_start_date', 'event_end_date'],
                ['start_date_meta.meta_value', 'end_date_meta.meta_value'],
                $criteria
            );
        }


        if (!empty($order)) {
            $order = preg_replace_callback(
                '/event_start_date\s+(asc|desc)/i',
                function ($matches) {
                    $direction = strtoupper($matches[1]);
                    return sprintf(
                        'start_date_meta.meta_value IS NULL, start_date_meta.meta_value %s',
                        $direction
                    );
                },
                $order
            );

            $order = preg_replace_callback(
                '/event_end_date\s+(asc|desc)/i',
                function ($matches) {
                    $direction = strtoupper($matches[1]);
                    return sprintf(
                        'end_date_meta.meta_value IS NULL, end_date_meta.meta_value %s',
                        $direction
                    );
                },
                $order
            );
        }

        $limit = '';
        if ($epp > 0) {
            $offset = ($page - 1) * $epp;
            $limit = sprintf(' limit %d offset %d', $epp, $offset);
        }

        $fromAndJoins = ' FROM contents '
            . $tagJoin . $categoryJoin
            . 'left join contentmeta as start_date_meta on contents.pk_content = start_date_meta.fk_content '
            . 'and start_date_meta.meta_name = "event_start_date" '
            . 'left join contentmeta as end_date_meta on contents.pk_content = end_date_meta.fk_content '
            . 'and end_date_meta.meta_name = "event_end_date" ';

        $whereSql = '';
        if (!empty($criteria)) {
            $whereSql = 'WHERE ' . $criteria . ' ';
        }

        $total = $this->getCustomListTotal($fromAndJoins, $whereSql);

        $sql = 'SELECT contents.pk_content' . $fromAndJoins . $whereSql;

        if (!empty($order)) {
            $sql .= 'ORDER BY ' . $order . ' ';
        }

        $sql .= $limit;

        $repository = $this->em->getRepository($this->entity, $this->origin);
        $items = $repository->findBySql($sql);

        $this->localizeList($items);

        return ['items' => $items, 'total' => $total];

        return parent::getList($oql);
    }

    /**
     * Replaces quick filter tokens in the criteria string with date range conditions.
     *
     * @param string $criteria
     *
     * @return string
     */
    private function replaceEventStartDateQuickFilters($criteria)
    {
        $todayStart            = (new \DateTimeImmutable('today'))->setTime(0, 0, 0);
        $tomorrowStart         = $todayStart->modify('+1 day');
        $dayAfterTomorrowStart = $tomorrowStart->modify('+1 day');
        $weekStart             = $todayStart->modify('monday this week');
        $nextWeekStart         = $weekStart->modify('+1 week');

        $filters = [
            self::QUICK_FILTER_TODAY => [
                'start' => $todayStart,
                'end'   => $tomorrowStart,
            ],
            self::QUICK_FILTER_TOMORROW => [
                'start' => $tomorrowStart,
                'end'   => $dayAfterTomorrowStart,
            ],
            self::QUICK_FILTER_THIS_WEEK => [
                'start' => $weekStart,
                'end'   => $nextWeekStart,
            ],
        ];

        foreach ($filters as $token => $range) {
            if (stripos($criteria, $token) === false) {
                continue;
            }

            $pattern = sprintf(
                '/event_start_date\s*(?:LIKE|~)\s*"[^"]*%s[^"]*"/i',
                preg_quote($token, '/')
            );

            if (!preg_match($pattern, $criteria)) {
                continue;
            }

            $criteria = preg_replace(
                $pattern,
                sprintf(
                    '(start_date_meta.meta_value >= "%s" AND start_date_meta.meta_value < "%s")',
                    $range['start']->format('Y-m-d'),
                    $range['end']->format('Y-m-d')
                ),
                $criteria,
                1
            );
        }

        return $criteria;
    }

    /**
     * Returns the total number of items that match the custom list query.
     *
     * @param string $fromAndJoins The FROM and JOIN part of the SQL query.
     * @param string $whereSql     The WHERE part of the SQL query (optional).
     *
     * @return int
     */
    private function getCustomListTotal($fromAndJoins, $whereSql)
    {
        $sql = 'SELECT COUNT(DISTINCT contents.pk_content)' . $fromAndJoins . $whereSql;

        return (int) $this->container->get('dbal_connection')->fetchColumn($sql);
    }
}
