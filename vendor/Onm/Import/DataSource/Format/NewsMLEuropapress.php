<?php
/**
 * Defines the Onm\Import\DataSource\NewsMLEuropapress class
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Import\DataSource\Format;

/**
 * Implements the handler for the Europapress NewsML format
 *
 * @package Onm\Import\DataSource
 **/
class NewsMLEuropapress extends NewsMLG1
{
    /**
     * Magic method for translate properties into XML elements
     *
     * @param string $propertyName the name of the property to get
     */
    public function __get($name)
    {
        return parent::__get($name);
    }

    /**
     * Returns the title of the element
     *
     * @return string the title
     **/
    public function getTitle()
    {
        return (string) $this->getData()->NewsItem->NewsComponent->NewsComponent->NewsLines->HeadLine;
    }

    /**
     * Returns the creation datetime of this element
     *
     * @return DateTime the datetime of the element
     **/
    public function getCreatedTime()
    {
        $originalDate = (string) $this->getData()->NewsEnvelope->DateAndTime;

        // ISO 8601 doesn't match this date 20111211T103900+0000
        $originalDate = preg_replace('@\+(\d){4}$@', '', $originalDate);

        return \DateTime::createFromFormat(
            'Y-m-d\TH:i:s',
            $originalDate
        );
    }

    /**
     * Returns the list of tags of this element
     *
     * @return int the priority level
     **/
    public function getTags()
    {

        $topics = $this->getData()->NewsItem->NewsComponent->TopicSet->Topic;

        $tags = array();
        foreach ($topics as $topic) {
            $tag = (string) $topic->Description;
            $tag = ucwords(strtolower($tag));

            if (stripos($tag, 'Servicio') !== 0) {
                $tags []= $tag;
            }
        }
        $tags = array_unique($tags);

        return $tags;
    }

    /**
     * Returns the body of the element
     *
     * @return string the body
     **/
    public function getBody()
    {
        $rawContent = (string) $this->getData()->NewsItem->NewsComponent->NewsComponent->ContentItem->DataContent;

        preg_match('@<body[^>]*>(.*?)<\/body>@is', $rawContent, $matches);

        $body = '';
        if (array_key_exists(1, $matches)) {
            $body = $matches[1];
        }

        return $body;

    }

    /**
     * Checks if a XML file could be handled by this class
     *
     * @param SimpleXmlElement $file the XML file to parse
     *
     * @return boolean true if the file could be handle by this class
     **/
    public static function checkFormat($data, $xmlFile)
    {
        $provider = (string) $data->NewsItem->Identification->NewsIdentifier->ProviderId;

        if ($provider != 'Europa Press') {
            throw new \Exception(sprintf(_('File %s is not a valid NewsMLEuropapres file'), $xmlFile));
        }

        return true;
    }
}
