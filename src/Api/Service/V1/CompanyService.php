<?php

namespace Api\Service\V1;

use Api\Exception\CreateItemException;
use Api\Exception\UpdateItemException;
use Api\Service\V1\ContentService;

class CompanyService extends ContentService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        $data['changed'] = new \DateTime();
        $data['created'] = new \DateTime();

        $data = $this->assignUser($data, [ 'fk_user_last_editor', 'fk_publisher' ]);

        try {
            $data = $this->em->getConverter($this->entity)
                ->objectify($this->parseData($data));

            // Format the deeply nested data to the correct format.
            foreach ($data['timetable'] as &$day) {
                $day['enabled']   = $day['enabled'] === 'true' ? true : false;
                $day['schedules'] = !is_array($day['schedules']) ? [] : $day['schedules'];
            }

            $item = new $this->class($data);

            $this->validate($item);
            $this->em->persist($item, $this->getOrigin());

            $id = $this->em->getMetadata($item)->getId($item);

            $this->dispatcher->dispatch($this->getEventName('createItem'), [
                'action' => __METHOD__,
                'id'     => array_pop($id),
                'item'   => $item
            ]);

            return $item;
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        try {
            $data = $this->em->getConverter($this->entity)
                ->objectify($data);

            // Format the deeply nested data to the correct format.
            foreach ($data['timetable'] as &$day) {
                $day['enabled']   = $day['enabled'] === 'true' ? true : false;
                $day['schedules'] = !is_array($day['schedules']) ? [] : $day['schedules'];
            }

            $item = $this->getItem($id);
            $item->setData($data);

            $this->validate($item);
            $this->em->persist($item, $item->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('updateItem'), [
                'action' => __METHOD__,
                'id'   => $id,
                'item' => $item
            ]);
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage());
        }
    }
}
