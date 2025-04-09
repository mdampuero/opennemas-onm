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
     * Returns the type assigned to the event
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The event type.
     */
    public function getEventType($data)
    {
        $property = $data->xpath('//DescriptiveMetadata/Property[@FormalName="Tesauro"]');

        $eventType = null;
        if (is_array($property) && !empty($property)) {
            $eventType = (string) $property[0]->attributes()->Value;
        }

        return $eventType;
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
     * {@inheritdoc}
     */
    public function parse($data)
    {
        // event_organizer_name
        // event_organizer_url

        $this->bag['event_start_date']    = $this->getTags($data);
        $this->bag['event_start_hour']    = $this->getTags($data);
        $this->bag['event_end_date']      = $this->getTags($data);
        $this->bag['event_end_hour']      = $this->getTags($data);
        $this->bag['event_website']       = $this->getTags($data);
        $this->bag['event_place']         = $this->getEventPlace($data);
        $this->bag['event_address']       = $this->getEventAddress($data);
        $this->bag['event_tickets_price'] = $this->getEventTicketMinPrice($data);
        $this->bag['event_tickets_link']  = $this->getEventTicketUrl($data);

        return parent::parse($data);
    }
}
