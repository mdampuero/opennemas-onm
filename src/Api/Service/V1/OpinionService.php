<?php

namespace Api\Service\V1;

use Api\Service\V1\ContentService;

class OpinionService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    protected function getOqlForList($oql)
    {
        preg_match('/blog\\s*=\\s*\"?([01])\"?\\s*/', $oql, $matches);

        if (!empty($matches)) {
            $cleanOql = preg_replace('/and blog\\s*=\\s*\"?[01]\"?\\s*/', '', $oql);
            // If blog = 0 negate the in condition in the oql
            $operator = (boolean) array_pop($matches) ? '' : '!';

            $authors = $this->container->get('api.service.author')
                ->getList(sprintf('is_blog = 1'))['items'];

            $ids = array_map(function ($a) {
                return $a->id;
            }, $authors);

            // No bloggers found
            if (empty($ids)) {
                // Return all when searching by blog = 0
                if (!empty($operator)) {
                    return $cleanOql;
                }

                // Force an empty result with invalid user id when searching by
                // blog = 1
                $ids = [ 0 ];
            }

            return $this->container->get('orm.oql.fixer')->fix($cleanOql)
                ->addCondition(sprintf('fk_author %sin [%s]', $operator, implode(',', $ids)))
                ->getOql();
        }

        return $oql;
    }
}
