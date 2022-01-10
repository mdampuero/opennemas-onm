<?php

namespace Api\Service\V1;

use Api\Service\V1\ContentService;

class WidgetService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    protected function getOqlForList($oql)
    {
        $oql = parent::getOqlForList($oql);

        preg_match('/and widget_type=\"html\"/', $oql, $matches);

        if (!empty($matches)) {
            $cleanOql = preg_replace('/and widget_type=\"html\"/', '', $oql);

            $intelligent = $this->container->get('api.service.widget')
                ->getList('widget_type = "intelligentwidget"')['items'];

            $ids = array_map(function ($a) {
                return $a->id;
            }, $intelligent);

            return empty($ids)
                ? $cleanOql
                : $this->container->get('orm.oql.fixer')->fix($cleanOql)
                ->addCondition(sprintf('pk_content !in [%s]', implode(',', $ids)))
                ->getOql();
        }

        preg_match('/and class=\"([a-zA-z]+)\"/', $oql, $matches);

        return empty($matches)
            ? $oql
            : preg_replace('/and widget_type=\"intelligentwidget\"/', '', $oql);
    }
}
