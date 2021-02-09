<?php

namespace Api\Service\V1;

use Api\Exception\GetListException;
use Api\Service\V1\ContentService;

class OpinionService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    protected function getOqlForList($oql)
    {
        preg_match('/blog=\"([01])\"/', $oql, $matches);

        if (!empty($matches)) {
            // If blog = 0 negate the in condition in the oql
            $condition = (boolean) array_pop($matches) ? '' : '!';

            try {
                $authors = $this->container->get('api.service.author')
                    ->getList(sprintf('is_blog = 1'))['items'];

                $ids = array_map(function ($a) {
                    return $a->id;
                }, $authors);
            } catch (GetListException $e) {
                return $oql;
            }

            $oql = preg_replace('/and blog=\"[01]\"/', '', $oql);

            return $this->container->get('orm.oql.fixer')->fix($oql)
                ->addCondition(sprintf('fk_author %sin [%s]', $condition, implode(',', $ids)))
                ->getOql();
        }

        return $oql;
    }
}
