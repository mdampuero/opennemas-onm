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

            $cleanCriteria = preg_replace('/and tag\\s*=\\s*\"?([0-9]*)\"?\\s*/', '', $criteria);
            preg_match('/tag\\s*=\\s*\"?([0-9]*)\"?\\s*/', $criteria, $matches);

            $tagJoin = '';
            if (!empty($matches)) {
                $tagJoin = sprintf(
                    ' inner join content_tag on contents.pk_content = content_tag.content_id and content_tag.tag_id in (%s) ',
                    $matches[1]
                );

                $countOql = $this->container->get('orm.oql.fixer')->fix($cleanCriteria)
                    ->addCondition(sprintf('tag_id in [%s]', $matches[1]))
                    ->getOql();

                $total = $this->countBy($countOql);
            } else {
                $countOql = $this->container->get('orm.oql.fixer')->fix($cleanCriteria)
                    ->getOql();

                $total = $this->countBy($countOql);
            }

            $criteria = $cleanCriteria;
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
                . $tagJoin
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

            return [ 'items' => $items, 'total' => $total ];
        }

        return parent::getList($oql);
    }
}
