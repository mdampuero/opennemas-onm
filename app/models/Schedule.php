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
 **/
// TODO redefine agenda_colectividad adapt to php 5.3

/**
 * Class for handling schedule. //Agenda de la colectividad de cronicas
 *
 * @package    Model
 **/
class Schedule extends Content
{
    /**
     * Returns a list of events
     *
     * @param int $limit max number of elements to get
     *
     * @return array the list of events
     **/
    public function getDataCalendars($limit = 8)
    {
        $sql = "SELECT * FROM phpc_AR_calendars "
             . "WHERE status=1 "
             . "ORDER BY position ASC, calendar DESC LIMIT 0, $limit";
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $calendars=array();
        while (!$rs->EOF) {
            $item=new stdClass();
            $item->id=$rs->fields['calendar'];
            $item->calendar_title = $rs->fields['calendar_title'];
            $item->contact_email  = $rs->fields['contact_email'];
            $item->contact_name   = $rs->fields['contact_name'];
            $item->bgcolor        = $rs->fields['bgcolor'];
            $item->ensign         = $rs->fields['ensign'];
            $item->position       = $rs->fields['position'];
            $item->name = \StringUtils::get_title($rs->fields['calendar_title']);

            $calendars[]=$item;
            $rs->MoveNext();
        }

        return $calendars;
    }

    /**
     * returns all the events for a particular where. Using in sitemap
     *
     * @param string $where the WHERE clause to filter the events with
     *
     * @return array the list of events
     **/
    public function getEventsByWhere($where)
    {
        $sql = 'SELECT * FROM phpc_AR_events '
            ."WHERE  ".$where
            ." ORDER BY  starttime DESC";

        $rs = $GLOBALS['application']->conn->Execute($sql);

        $events = array();
        while (!$rs->EOF) {
            $item= new stdClass();
            $item->id        = $rs->fields['id'];
            $item->title     = $rs->fields['subject'];
            $item->calendar  = $rs->fields['calendar'];
            $item->startdate = $rs->fields['startdate'];
            $item->enddate   = $rs->fields['enddate'];
            $item->section   = $rs->fields['section'];
            $item->name = \StringUtils::get_slug(
                html_entity_decode($rs->fields['subject'], ENT_QUOTES, 'UTF-8')
            );
            $item->slug =  \StringUtils::get_slug($rs->fields['subject']);

            $events[]=$item;
            $rs->MoveNext();
        }

        return $events;
    }
}
