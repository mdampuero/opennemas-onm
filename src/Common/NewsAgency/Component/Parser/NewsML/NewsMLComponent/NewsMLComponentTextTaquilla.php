<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent;

use Common\NewsAgency\Component\Parser\NewsML\NewsML;

/**
 * Parses NewsComponent of text type from NewsML custom format for Taquilla.com.
 */
class NewsMLComponentTextTaquilla extends NewsML
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!parent::checkFormat($data)) {
            return false;
        }

        if ($this->getAgencyName($data) === 'Taquilla.com') {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory($data)
    {
        $property = $data->xpath('//identified-content/location/location.city');

        $category = null;
        if (is_array($property) && !empty($property)) {
            $category = (string) $property[0];
        }

        return $category;
    }

    /**
     * Returns the place address assigned to the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event address place.
     */
    public function getEventAddress($data)
    {
        $property = $data->xpath('//identified-content/location/location.address');

        $address = null;
        if (is_array($property) && !empty($property)) {
            $address = (string) $property[0];
        }

        return $address;
    }

    /**
     * Returns the information about the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event information.
     */
    public function getEventInfo($data)
    {
        $property = $data->xpath('//identified-content/location/location.address');

        $address = null;
        if (is_array($property) && !empty($property)) {
            $address = (string) $property[0];
        }

        return $address;
    }

    /**
     * Returns the event place latitude
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event place latitude.
     */
    public function getEventLatitude($data)
    {
        $property = $data->xpath('//identified-content/location/location.latitude');

        $latitude = '';
        if (is_array($property) && !empty($property)) {
            $latitude = (string) $property[0];
        }

        return $latitude;
    }

    /**
     * Returns the event place latitude
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event place latitude.
     */
    public function getEventLongitude($data)
    {
        $property = $data->xpath('//identified-content/location/location.longitude');

        $longitude = '';
        if (is_array($property) && !empty($property)) {
            $longitude = (string) $property[0];
        }

        return $longitude;
    }

    /**
     * Returns the event organizer name.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event organizer name.
     */
    public function getEventOrganizerName($data)
    {
        $property = $data->xpath('//identified-content/organizer/organizer.name');

        $name = '';
        if (is_array($property) && !empty($property)) {
            $name = (string) $property[0];
        }

        return $name;
    }

    /**
     * Returns the event organizer url
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event organizer url.
     */
    public function getEventOrganizerUrl($data)
    {
        $property = $data->xpath('//identified-content/organizer/organizer.url');

        $url = '';
        if (is_array($property) && !empty($property)) {
            $url = (string) $property[0];
        }

        return $url;
    }

    /**
     * Returns the place assigned to the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event place.
     */
    public function getEventPlace($data)
    {
        $property = $data->xpath('//identified-content/location/location.place');

        $place = null;
        if (is_array($property) && !empty($property)) {
            $place = (string) $property[0];
        }

        return $place;
    }

    /**
     * Returns the start date for the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return DateTime The event start date.
     */
    public function getEventStartDate($data)
    {
        $start = $data->xpath('//identified-content/event/@start-date');

        if (is_array($start) && !empty($start)) {
            $startDate = \DateTime::createFromFormat('Ymd\THisP', $start[0]);

            return $startDate;
        }

        return new \DateTime('now');
    }

    /**
     * Returns the ticket url assigned to the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event ticket url.
     */
    public function getEventTicketUrl($data)
    {
        $property = $data->xpath('//identified-content/virtloc/@value');

        $url = null;
        if (is_array($property) && !empty($property)) {
            $url = (string) $property[0];
        }

        return $url;
    }

    /**
     * Returns the ticket min price assigned to the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The ticket min price.
     */
    public function getEventTicketMinPrice($data)
    {
        $property = $data->xpath('//identified-content/price/@min');

        $minPrice = null;
        if (is_array($property) && !empty($property)) {
            $minPrice = (string) $property[0];
        }

        return $minPrice;
    }

    /**
     * Returns the type assigned to the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event type.
     */
    public function getEventTypeString($data)
    {
        $property = $data->xpath('//DescriptiveMetadata/Property[@FormalName="Tesauro"]');

        $eventType = null;
        if (is_array($property) && !empty($property)) {
            $eventType = (string) $property[0]->attributes()->Value;
        }

        return $eventType;
    }

    /**
     * Returns the type id assigned to the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event type.
     */
    public function getEventTypeId($data)
    {
        $property = $data->xpath('//identified-content/@type-id');

        $eventTypeId = null;
        if (is_array($property) && !empty($property)) {
            $eventTypeId = (string) $property[0];
        }

        return $eventTypeId;
    }

    /**
     * Returns the subtype id assigned to the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event type.
     */
    public function getEventSubtypeId($data)
    {
        $property = $data->xpath('//identified-content/@subtype-id');

        $eventSubtypeId = null;
        if (is_array($property) && !empty($property)) {
            $eventSubtypeId = (string) $property[0];
        }

        return $eventSubtypeId;
    }

    /**
     * Returns the event website in Taquilla
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event website.
     */
    public function getEventWebsite($data)
    {
        $website = $data->xpath('//identified-content/virtloc/@value');

        if (is_array($website) && !empty($website)) {
            return (string) $website[0];
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $eventStartDate = $this->getEventStartDate($data);

        $this->bag['event_start_date']     = $eventStartDate->format('d-m-Y');
        $this->bag['event_start_hour']     = $eventStartDate->format('h:i');
        $this->bag['event_website']        = $this->getEventWebsite($data);
        $this->bag['event_info']           = $this->getEventInfo($data);
        $this->bag['event_place']          = $this->getEventPlace($data);
        $this->bag['event_address']        = $this->getEventAddress($data);
        $this->bag['event_map_latitude']   = $this->getEventLatitude($data);
        $this->bag['event_map_longitude']  = $this->getEventLongitude($data);
        $this->bag['event_tickets_price']  = $this->getEventTicketMinPrice($data);
        $this->bag['event_tickets_link']   = $this->getEventTicketUrl($data);
        $this->bag['event_type_string']    = $this->getEventTypeString($data);
        $this->bag['event_type_id']        = $this->getEventTypeId($data);
        $this->bag['event_subtype_id']     = $this->getEventSubtypeId($data);
        $this->bag['event_organizer_name'] = $this->getEventOrganizerName($data);
        $this->bag['event_organizer_url']  = $this->getEventOrganizerUrl($data);
        $this->bag['canonicalurl']         = $this->getEventWebsite($data);

        // Parse taquilla id to Opennemas event type
        $this->bag['event_type'] = $this->getEventTypeParsed(
            $this->getEventTypeId($data)
        );

        return parent::parse($data);
    }

    /**
     * Maps the event type from Taquilla.com to Opennemas
     * We are only using types not subtypes
     *
     * https://api.taquilla.com/data/search/types?t10id=96008
     */
    protected function getEventTypeParsed($typeId)
    {
        $taquillaTypes = [
            // Tipos principales
            4  => 'espectaculos',
            2  => 'conciertos',
            3  => 'deportes',
            5  => 'museo',
            7  => 'otros',
            15 => 'cine',
        ];

        return $taquillaTypes[$typeId];
    }
}
