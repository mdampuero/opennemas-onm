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

use Api\Exception\UpdateItemException;

class PollService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        return $this->getPercents([0 => parent::getItem($id)])[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        $list = parent::getList($oql);

        $list['items'] = $this->getPercents($list['items']);

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function updateVotedItem($id, $data)
    {
        try {
            $em = $this->container->get('orm.manager');
            $data = $this->em->getConverter($this->entity)
                ->objectify($data);

            $item = $this->getItem($id);
            $item->setData($data);

            $this->validate($item);
            $em->persist($item, $item->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('updateVotedItem'), [
                'action' => __METHOD__,
                'id'     => $id,
                'item'   => $item,
            ]);
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage());
        }
    }

    /**
     * Generate percents for poll items
     *
     * @param array $list The list of polls.
     *
     * @return array $list The list of polls with items percents.
     */
    private function getPercents($list)
    {
        $total_votes = $this->container->get('core.helper.poll')->getTotalVotes($list);

        $list = array_map(function ($poll) use ($total_votes) {
            $items = array_map(function ($item) use ($poll, $total_votes) {
                $percent = round($item['votes'] /
                    ($total_votes[$poll->pk_content] > 0 ? $total_votes[$poll->pk_content] : 1), 4) * 100;

                $item['percent'] = sprintf('%.2f', $percent);

                return $item;
            }, $poll->items);

            $poll->items = $items;

            return $poll;
        }, $list);

        return $list;
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
