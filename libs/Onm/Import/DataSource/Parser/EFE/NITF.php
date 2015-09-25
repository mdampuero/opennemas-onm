<?php
/*
 * This file is part of the Onm package.
 *
 * (c) OpenHost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource\Parser;

/**
 * Parses XML files in NITF format.
 */
class NITF extends Parser
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }

        $node = $data->xpath('/nitf');

        if (!is_array($node) || count($node) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Cleans and returns a SimpleXMLObject from the given data.
     *
     * @param SimpleXMLObject data The source object.
     *
     * @return SimpleXMLObject The cleaned object.
     */
    public function clean($data)
    {
        // Get only nitf
        $data = $data->xpath('//nitf');
        $data = $data[0];

        // Discard extra elements
        return simplexml_load_string(
            $data->asXML(),
            null,
            LIBXML_NOERROR | LIBXML_NOWARNING
        );
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
        $service = $data->xpath('//rights.owner');

        if (is_array($service) && count($service) > 0) {
            return (string) $service[0];
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
        $bodies = $data->xpath('//body/body.content');

        $body = '';
        foreach ($bodies[0]->children() as $child) {
            $body .= "<p>$child</p>\n";
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
        $date = $data->xpath('//body/body.head/dateline/story.date');
        $date = $date[0];
        $date = \DateTime::createFromFormat('Ymd\THisP', $date);

        $date->setTimezone(new \DateTimeZone('Europe/Madrid'));

        return $date;
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
        $docId = $data->xpath('//doc-id');
        $docId = $docId[0];
        $attributtes = $docId->attributes();

        return (string) $attributtes->{'id-string'};
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
        $priority = $data->xpath('//head/meta[@name="prioridad"]');

        if (!empty($priority)) {
            $priority = (string) $priority[0]->attributes()->content;

            if (array_key_exists($priority, $this->priorities)) {
                return $this->priorities[$priority];
            }
        }

        return 1;
    }

    /**
     * Returns the summary from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The summary.
     */
    public function getSummary($data)
    {
        $summaries = $data->xpath('//body/body.head/abstract');

        $summary = "";
        foreach ($summaries[0]->children() as $child) {
            $summary .= "<p>" . sprintf("%s", $child) . "</p>";
        }

        return iconv(mb_detect_encoding($summary), "UTF-8", $summary);
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
        $title = $data->xpath('//head/title');

        $title = (string) $title[0];

        return iconv(mb_detect_encoding($title), "UTF-8", $title);
    }

    /**
     * Returns the unique urn from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The URN.
     */
    public function getUrn($data)
    {
        $classname = get_class($this);
        $classname = substr($classname, strrpos($classname, '\\') + 1);

        $tokens = [
            strtolower($classname),
            $this->getAgencyName($data),
            $this->getCreatedTime($data)->format('YmdHis'),
            $this->getId($data)
        ];

        return 'urn:' . implode(':', $tokens);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $data = $this->clean($data);

        return [
            [
                'agency_name'  => $this->getAgencyName($data),
                'body'         => $this->getBody($data),
                'category'     => $this->getCategory($data),
                'created_time' => $this->getCreatedTime($data),
                'id'           => $this->getId($data),
                'pretitle'     => $this->getPretitle($data),
                'priority'     => $this->getPriority($data),
                'related'      => [],
                'summary'      => $this->getSummary($data),
                'tags'         => $this->getTags($data),
                'title'        => $this->getTitle($data),
                'type'         => 'text',
                'urn'          => $this->getUrn($data)
            ]
        ];
    }
}
