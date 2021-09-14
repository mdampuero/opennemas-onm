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

use Api\Exception\GetItemException;

class PollService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            if (empty($id)) {
                throw new \InvalidArgumentException();
            }

            $item = $this->em->getRepository($this->entity, $this->origin)->find($id);

            $this->localizeItem($item);

            $this->dispatcher->dispatch($this->getEventName('getItem'), [
                'id'   => $id,
                'item' => $item
            ]);

            $total_votes = $this->container->get('core.helper.poll')->getTotalVotes($item);

            $item->items = array_map(function ($a) use ($item, $total_votes) {
                $percent = round($a['votes'] /
                    ($total_votes[$item->pk_content] > 0 ? $total_votes[$item->pk_content] : 1), 4) * 100;

                $a['percent'] = sprintf('%.2f', $percent);
                return $a;
            }, $item->items);

            return $item;
        } catch (\Exception $e) {
            throw new GetItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function localizeItem($item)
    {
        $keys = [
            'items' => [ 'item' ],
        ];

        $item = parent::localizeItem($item);

        foreach ($keys as $key => $value) {
            if (!empty($item->{$key})) {
                $item->{$key} = $this->container->get('data.manager.filter')
                    ->set($item->{$key})
                    ->filter('localize', [ 'keys' => $value ])
                    ->get();
            }
        }

        return $item;
    }
}
