<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Parser\Nitf;

use Common\NewsAgency\Component\Parser\Parser;
use Common\NewsAgency\Component\Resource\ExternalResource;

/**
 * Parses XML files in NITF format.
 */
class Nitf extends Parser
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

        if (!is_array($node) || empty($node)) {
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

        if (is_array($service) && !empty($service)) {
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

        if (empty($bodies)) {
            return '';
        }

        $body = '';

        if (!empty($bodies[0]->children())) {
            foreach ($bodies[0]->children() as $child) {
                $body .= '<p>' . str_replace("\n", '<br>', $child) . '</p>';
            }
        }

        if (empty($body) && !empty((string) $bodies[0])) {
            $body = trim(trim(str_replace(
                "\n",
                '<br>',
                html_entity_decode((string) $bodies[0])
            ), '<br>'));
        }

        return iconv(mb_detect_encoding($body), "UTF-8", $body);
    }

    /**
     * Returns the category assigned to the resouce
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The category.
     */
    public function getCategory($data)
    {
        $category = $data->xpath('//head/meta[@name="categoria"]');

        if (empty($category)) {
            return '';
        }

        return (string) $category[0]->attributes()->content;
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

        if (empty($date)) {
            return new \DateTime();
        }

        $value = (string) $date[0]->attributes()->norm[0];
        $date  = \DateTime::createFromFormat('Ymd\THisP', $value);

        if (!$date) {
            $date = \DateTime::createFromFormat('Ymd\THis', $value);
        }

        $date->setTimezone(new \DateTimeZone('UTC'));

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
        $id = $data->xpath('//doc-id');

        if (empty($id)) {
            return '';
        }

        $value = (string) $id[0];

        if (!empty($id[0]->attributes())
            && !empty($id[0]->attributes()->{'id-string'})
        ) {
            $value = (string) $id[0]->attributes()->{'id-string'}[0];
        }

        return $value;
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
        $priority = $data->xpath('//head/docdata/urgency');

        if (!empty($priority)) {
            return (int) $priority[0]->attributes()->{'ed-urg'};
        }

        return !empty($this->getFromBag('priority'))
            ? (int) $this->getFromBag('priority')
            : 5;
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
        $summary   = '';

        if (empty($summaries)) {
            return $summary;
        }

        foreach ($summaries[0]->children() as $child) {
            $summary .= '<p>' . (string) $child . '</p>';
        }

        if (empty($summary) && !empty((string) $summaries[0])) {
            $summary = trim(trim(str_replace(
                "\n",
                '<br>',
                html_entity_decode((string) $summaries[0])
            ), '<br>'));
        }

        return iconv(mb_detect_encoding($summary), 'UTF-8', $summary);
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

        if (empty($title)) {
            return '';
        }

        $title = (string) $title[0];

        return iconv(mb_detect_encoding($title), "UTF-8", $title);
    }

    /**
     * Returns the href from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The href.
     */
    public function getHref($data)
    {
        return '';
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

        $resource = strtolower($classname);
        $agency   = str_replace(
            ' ',
            '_',
            strtolower($this->getAgencyName($data))
        );

        $date = $this->getCreatedTime($data)->format('YmdHis');
        $id   = $this->getId($data);

        return "urn:$resource:$agency:$date:text:$id";
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $data = $this->clean($data);

        $content = new ExternalResource();

        $content->agency_name  = $this->getAgencyName($data);
        $content->body         = $this->getBody($data);
        $content->category     = $this->getCategory($data);
        $content->created_time = $this->getCreatedTime($data)
            ->format('Y-m-d H:i:s');
        $content->id           = $this->getId($data);
        $content->pretitle     = $this->getPretitle($data);
        $content->priority     = $this->getPriority($data);
        $content->summary      = $this->getSummary($data);
        $content->tags         = $this->getTags($data);
        $content->title        = $this->getTitle($data);
        $content->type         = 'text';
        $content->urn          = $this->getUrn($data);
        $content->href         = $this->getHref($data);

        return $content;
    }
}
