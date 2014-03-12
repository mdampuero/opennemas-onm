<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Diéguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource\Format\NewsMLG1Component;

use Onm\Import\DataSource\FormatInterface;

/**
 * Handles all the operations for NITF data
 *
 * @package Onm_Import
 **/
class NITF implements FormatInterface
{
    /**
     * Instantiates the NITF DOM data from an SimpleXML object
     *
     * @return void
     **/
    public function __construct($data)
    {
        if (is_object($data)) {
            $this->data = $data;
        } elseif (is_string($data)) {
            $this->xmlFile = $data;

            if (file_exists($this->xmlFile)) {
                if (filesize($this->xmlFile) < 2) {
                    throw new \Exception(
                        sprintf(_("File '%d' can't be loaded."), $this->xmlFile)
                    );
                }

                $this->data = simplexml_load_file(
                    $this->xmlFile,
                    null,
                    LIBXML_NOERROR | LIBXML_NOWARNING
                );

                if (!$this->data) {
                    throw new \Exception(
                        sprintf(_("File '%d' can't be loaded."), $this->xmlFile)
                    );
                }

                $this->checkFormat($this->data, $this->xmlFile);
            } else {
                throw new \Exception(
                    sprintf(_("File '%d' doesn't exists."), $this->xmlFile)
                );
            }
        }

        $this->agencyName = 'Europapress';

        self::checkFormat($this->data, null);
    }

    /**
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
        }
    }

    /**
     * Returns the internal data, use with caution
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the name of the service that authored this element
     *
     * @return string the service name
     **/
    public function getServiceName()
    {
        return '';
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

        $title = (string) $titles[0];

        return iconv(mb_detect_encoding($title), "UTF-8", $title);
    }

    /**
     * Returns the pretitle for this NITF resource
     *
     * @return string the pretitle
     **/
    public function getPreTitle()
    {
        $pretitles = $this->getData()->xpath("//NewsLines/SubHeadLine");

        $pretitle = (string) $pretitles[0];

        return iconv(mb_detect_encoding($pretitle), "UTF-8", $pretitle);
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

        return iconv(mb_detect_encoding($summary), "UTF-8", $summary);
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

        return iconv(mb_detect_encoding($body), "UTF-8", $body);
    }

    /**
     * Returns the unique urn of the element
     *
     * @return string the urn
     **/
    public function getUrn()
    {
        return '';
    }

    /**
     * Returns an integer between 1 and 5 that represents the priority level
     *
     * @return int the priority level
     **/
    public function getPriority()
    {
        return 1;
    }

    /**
     * Returns the list of tags of this element
     *
     * @return int the priority level
     **/
    public function getTags()
    {
        return array();
    }

    /**
     * Returns the category of the element
     *
     * @return int the priority level
     **/
    public function getCategory()
    {
        return '';
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

    /**
     * Checks if the data provided could be handled by the class
     *
     * @return string
     **/
    public static function checkFormat($data, $xmlFile)
    {
        if (is_string($data)) {
            throw new \Exception(sprintf(_('Not a valid NITF file')));
        }

        $node = $data->xpath("//nitf");

        if (!is_array($node)) {
            throw new \Exception(sprintf(_('Not a valid NITF file')));
        }
        return true;
    }
}
