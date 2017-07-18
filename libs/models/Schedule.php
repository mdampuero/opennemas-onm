<?php
/**
 * Defines the Schedule class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 */
// TODO redefine agenda_colectividad adapt to php 5.3

/**
 * Class for handling schedule. //Agenda de la colectividad de cronicas
 *
 * @package    Model
 */
class Schedule extends Content
{
    /**
     * Returns a list of events
     *
     * @param int $limit max number of elements to get
     *
     * @return array the list of events
     */
    public function getDataCalendars($limit = 8)
    {
        $calendars = [];
        try {
            $rs = getService('dbal_connection')->fetchAll(
                "SELECT * FROM phpc_AR_calendars "
               . "WHERE status=1 "
               . "ORDER BY position ASC, calendar DESC LIMIT 0, ?",
               [ $limit ]
            );

            foreach ($rs as $calendar) {
                $item                 = new stdClass();
                $item->id             = $calendar['calendar'];
                $item->calendar_title = $calendar['calendar_title'];
                $item->contact_email  = $calendar['contact_email'];
                $item->contact_name   = $calendar['contact_name'];
                $item->bgcolor        = $calendar['bgcolor'];
                $item->ensign         = $calendar['ensign'];
                $item->position       = $calendar['position'];
                $item->name           = \Onm\StringUtils::getTitle($calendar['calendar_title']);

                $calendars[] = $item;
            }

            return $calendars;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $calendars;
        }
    }

    /**
     * returns all the events for a particular where. Using in sitemap
     *
     * @param string $where the WHERE clause to filter the events with
     *
     * @return array the list of events
     */
    public function getEventsByWhere($where, $limit = 8)
    {
        $events = [];
        try {
            $rs = getService('dbal_connection')->fetchAll(
                "SELECT * FROM phpc_AR_events "
                ."WHERE ".$where
                ." ORDER BY  starttime DESC LIMIT ?",
                [ $limit ]
            );

            foreach ($rs as $event) {
                $item            = new stdClass();
                $item->id        = $event['id'];
                $item->title     = $event['subject'];
                $item->calendar  = $event['calendar'];
                $item->startdate = $event['startdate'];
                $item->enddate   = $event['enddate'];
                $item->section   = $event['section'];
                $item->slug      = \Onm\StringUtils::generateSlug($event['subject']);
                $item->name      = \Onm\StringUtils::generateSlug(
                    html_entity_decode($event['subject'], ENT_QUOTES, 'UTF-8')
                );

                $events[] = $item;
            }

            return $events;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $events;
        }
    }
}
