<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Diéguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource;
/**
 * Handles all the operations for NITF data
 *
 * @package Onm
 * @author
 **/
class NITF
{
    /**
     * Instantiates the NITF DOM data from an SimpleXML object
     *
     * @return void
     * @author
     **/
    public function __construct($data)
    {
        $this->data = $data;
    }

    /*
     * Magic method for translate properties into XML elements
     *
     * @param string $propertyName the name of the property to get
     */
    public function __get($propertyName)
    {
        switch ($propertyName) {

            case 'id':
                return $this->getId();
                break;

            case 'title':
                return $this->getTitle();
                break;

            case 'pretitle':
                return $this->getPreTitle();
                break;

            case 'summary':
                return $this->getSummary();
                break;

            case 'body':
                return $this->getBody();
                break;

            case 'created_time':
                return $this->getCreatedTime();
                break;

            case 'body':
                if (count($this->texts) > 0) {
                    return $this->texts[0];
                }

                return;
                break;
        }
    }

    /*
     * Returns the internal data, use with caution
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the id for this NITF resource
     *
     * @return int the id
     **/
    public function getId()
    {
        $attributes = $this->getData()->attributes();

        return (string) $attributes->Euid;
    }

    /**
     * Returns the title for this NITF resource
     *
     * @return string the title
     **/
    public function getTitle()
    {
        $titles = $this->getData()->xpath("//NewsLines/HeadLine");

        return (string) $titles[0];
    }

    /**
     * Returns the pretitle for this NITF resource
     *
     * @return string the pretitle
     **/
    public function getPreTitle()
    {
        $pretitles = $this->getData()->xpath("//NewsLines/SubHeadLine");

        return (string) $pretitles[0];
    }

    /**
     * Returns the summary for this NITF resource
     *
     * @return string the summary
     **/
    public function getSummary()
    {
        $summaries = $this->getData()->xpath(
            "//nitf/body/body.head/abstract"
        );
        $summary   = "";
        foreach ($summaries[0]->children() as $child) {
          $summary .= "<p>".sprintf("%s", $child)."</p>";
        }

        return $summary;
    }

    /**
     * Returns the body for this NITF resource
     *
     * @return string the body
     **/
    public function getBody()
    {
        $bodies = $this->getData()->xpath(
            "//nitf/body/body.content"
        );
        $body = "";
        foreach ($bodies[0]->children() as $child) {
          $body .= "<p>".sprintf("%s", $child)."</p>\n";
        }

        return $body;
    }

    /**
     * Returns the created time for this NITF resource
     *
     * @return \DateTime the created time
     **/
    public function getCreatedTime()
    {
        $originalDate =
            (string) $this->getData()->DescriptiveMetadata->DateLineDate;

        return \DateTime::createFromFormat(
            \DateTime::ISO8601,
            $originalDate
        );
    }

} // END class NITF
