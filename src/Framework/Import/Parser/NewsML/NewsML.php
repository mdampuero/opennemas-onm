<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Parser\NewsML;

use Framework\Import\Parser\Parser;

/**
 * Parses XML files in NewsML format.
 */
class NewsML extends Parser
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }

        $node = $data->xpath('/NewsML');

        if (!is_array($node) || count($node) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Returns the agency name from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The agency name.
     */
    public function getAgencyName($data)
    {
        $agency = $data->xpath('//AdministrativeMetadata/Provider/Party');

        if (is_array($agency) && count($agency) > 0) {
            return (string) $agency[0]->attributes()->FormalName;
        }

        return '';
    }

    /**
     * Returns the body from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The body.
     */
    public function getBody($data)
    {
        $bodies = $data->xpath('//p');

        if (empty($bodies)) {
            return '';
        }

        $body = '';
        foreach ($bodies as $child) {
            $body .= '<p>' . (string) $child . '</p>';
        }

        return iconv(mb_detect_encoding($body), "UTF-8", $body);
    }

    /**
     * Returns the created time from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return \DateTime The created time.
     */
    public function getCreatedTime($data)
    {
        $date = $data->xpath('//FirstCreated');

        if (is_array($date) && !empty($date)) {
            $date = \DateTime::createFromFormat('Ymd\THisP', $date[0]);
            $date->setTimezone(new \DateTimeZone('UTC'));

            return $date;
        }

        $date = $this->getFromBag('created_time');

        if (!empty($date)) {
            return new \Datetime($date);
        }

        return new \DateTime('now');
    }

    /**
     * Returns the id from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The id.
     */
    public function getId($data)
    {
        $id = $data->xpath('//NewsItemId');

        if (is_array($id) && count($id) > 0) {
            return (string) $id[0];
        }

        $id = $this->getFromBag('id');

        if (!empty($id)) {
            return $id;
        }

        return uniqid();
    }

    /**
     * Returns the pretitle from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The pretitle.
     */
    public function getPretitle($data)
    {
        $pretitle = $data->xpath('//SubHeadLine');

        if (is_array($pretitle) && count($pretitle) > 0) {
            $pretitle = $pretitle[0];

            return iconv(mb_detect_encoding($pretitle), "UTF-8", $pretitle);
        }

        return '';
    }

    /**
     * Returns the priority from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return integer The priority level.
     */
    public function getPriority($data)
    {
        $priority = $data->xpath("//NewsItem/NewsManagement/Urgency");

        if (empty($priority)) {
            return 5;
        }

        $priority = (string) $priority[0]->attributes()->FormalName;

        if (array_key_exists($priority, $this->priorities)) {
            return $this->priorities[$priority];
        }

        return $priority;
    }

    /**
     * Returns the type from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The type.
     */
    public function getType($data)
    {
        $type = $data->xpath("//MediaType");

        if (is_array($type) && count($type) > 0) {
            return (string) $type[0]->attributes()->FormalName;
        }

        return '';
    }

    /**
     * Returns the title from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The title.
     */
    public function getTitle($data)
    {
        $title = $data->xpath('//HeadLine');

        if (is_array($title) && count($title) > 0) {
            $title = (string) $title[0];
            return iconv(mb_detect_encoding($title), "UTF-8", $title);
        }

        return '';
    }

    /**
     * Returns the unique urn from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     * @param string          The resource type.
     *
     * @return string The URN.
     */
    public function getUrn($data, $type = 'text')
    {
        $classname = get_class($this);
        $classname = substr($classname, strrpos($classname, '\\') + 1);

        $resource = strtolower($classname);
        $agency   = strtolower(\Onm\StringUtils::generateSlug($this->getAgencyName($data)));

        $date = $this->getCreatedTime($data);
        $id   = $this->getId($data);

        if (!empty($date)) {
            $date = $date->format('YmdHis');
        }

        return "urn:$resource:$agency:$date:$type:$id";
    }

    /**
     * Returns the list of tags from teh parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The tag list.
     */
    public function getTags($data)
    {
        $tags = $data->xpath('//DescriptiveMetadata/OfInterestTo');

        if (is_array($tags) && count($tags) > 0) {
            return (string) $tags[0]->attributes()->FormalName;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $this->bag['agency_name']  = $this->getAgencyName($data);
        $this->bag['id']           = $this->getId($data);
        $this->bag['created_time'] = $this->getCreatedTime($data)
            ->format('Y-m-d H:i:s');

        $items = $data->xpath('/NewsML/NewsItem');

        $contents = [];
        foreach ($items as $item) {
            $items    = simplexml_load_string($item->asXML());
            $contents = array_merge($contents, $this->parseItem($item));
        }

        foreach ($contents as &$content) {
            $content->merge($this->bag);
        }

        return $contents;
    }

    /**
     * Parses a NewsComponent.
     *
     * @param SimpleXMLObject $data The component to parse.
     */
    public function parseComponent($data)
    {
        $parser = $this->factory->get($data, $this);
        $parser->setBag($this->bag);

        $parsed = $parser->parse($data);

        foreach ($parser->getBag() as $key => $value) {
            if (!array_key_exists($key, $this->bag)
                || empty($this->bag[$key])
            ) {
                $this->bag[$key] = $value;
            }
        }

        return $parsed;
    }

    /**
     * Parses a NewsItem.
     *
     * @param SimpleXMLObject $item The item to parse.
     */
    public function parseItem($item)
    {
        $components = $item->xpath('NewsComponent');

        $contents = [];
        foreach ($components as $component) {
            $component = simplexml_load_string($component->asXML());

            $parsed = $this->parseComponent($component);

            if (is_object($parsed)) {
                $parsed = [ $parsed ];
            }

            $contents = array_merge($contents, $parsed);
        }

        foreach ($contents as $content) {
            $content->merge($this->bag);
        }

        return $contents;
    }
}
