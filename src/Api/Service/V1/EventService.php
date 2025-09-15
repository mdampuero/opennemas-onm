<?php

namespace Api\Service\V1;

class EventService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        if (preg_match('/order by.*event_(start|end)_date/i', $oql)) {
            list($criteria, $order, $epp, $page) = $this->container
                ->get('core.helper.oql')
                ->getFiltersFromOql($oql);

            $oql = $this->getOqlForList($oql);
            $total = $this->countBy($oql);

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

            $sql = 'SELECT contents.* FROM contents '
                . $tagJoin . $categoryJoin
                . 'left join contentmeta as start_date_meta on contents.pk_content = start_date_meta.fk_content '
                . 'and start_date_meta.meta_name = "event_start_date" '
                . 'left join contentmeta as end_date_meta on contents.pk_content = end_date_meta.fk_content '
                . 'and end_date_meta.meta_name = "event_end_date" ';
            if (!empty($criteria)) {
                $sql .= 'WHERE ' . $criteria . ' ';
            }

            if (!empty($order)) {
                $sql .= 'ORDER BY ' . $order . ' ';
            }

            $sql .= $limit;

            $repository = $this->em->getRepository($this->entity, $this->origin);
            $items = $repository->findBySql($sql);

            $this->localizeList($items);

            return ['items' => $items, 'total' => $total];
        }

        return parent::getList($oql);
    }
}
