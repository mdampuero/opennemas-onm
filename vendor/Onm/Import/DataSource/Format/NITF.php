<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Diéguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource\Format;

use Onm\Import\DataSource\FormatInterface;

/**
 * Handles all the operations for NITF data
 *
 * @package Onm_Import
 **/
class NITF extends NewsMLG1
{
    /**
     * Instantiates the NITF DOM data from an SimpleXML object
     *
     * @return void
     **/
    public function __construct($xmlFile)
    {
        $this->xmlFile = basename($xmlFile);

        if (file_exists($xmlFile)) {
            if (filesize($xmlFile) < 2) {
                throw new \Exception(
                    sprintf(_("File '%d' can't be loaded."), $xmlFile)
                );
            }

            $this->data = simplexml_load_file(
                $xmlFile,
                null,
                LIBXML_NOERROR | LIBXML_NOWARNING
            );

            if (!$this->data) {
                throw new \Exception(
                    sprintf(_("File '%d' can't be loaded."), $xmlFile)
                );
            }

            $this->checkFormat($this->data, $xmlFile);
        } else {
            throw new \Exception(
                sprintf(_("File '%d' doesn't exists."), $xmlFile)
            );
        }

        return $this;
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
            case 'urn':
                return $this->getUrn();

                break;
            case 'pretitle':
                return $this->getPretitle();

                break;
            case 'title':
                return $this->getTitle();

                break;
            case 'priority':
                return $this->getPriority();

                break;
            case 'tags':
                return $this->getTags();

                break;
            case 'created_time':
                return $this->getCreatedTime();

                break;
            case 'body':
                return $this->getBody();

                break;
            case 'agency_name':
                return $this->getServiceName();

                break;
            case 'texts':
            case 'photos':
            case 'videos':
            case 'audios':
            case 'moddocs':
            case 'files':
                return array();

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
        $docId = $this->getData()->body->{'body.head'}->rights->{'rights.owner'};

        return (string) $docId;
    }

    /**
     * Returns the id for this NITF resource
     *
     * @return int the id
     **/
    public function getId()
    {
        $docId = $this->getData()->head->docdata->xpath('//doc-id');
        $docId = $docId[0];
        $attributtes = $docId->attributes();

        return (string) $attributtes->{'id-string'};
    }

    /**
     * Returns the title for this NITF resource
     *
     * @return string the title
     **/
    public function getTitle()
    {
        return (string) $this->getData()->head->title;
    }

    /**
     * Returns the pretitle for this NITF resource
     *
     * @return string the pretitle
     **/
    public function getPreTitle()
    {
        return '';
    }

    /**
     * Returns the summary for this NITF resource
     *
     * @return string the summary
     **/
    public function getSummary()
    {
        return '';
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
     * Returns the unique urn of the element
     *
     * @return string the urn
     **/
    public function getUrn()
    {
        $date = (string) $this->getData()->body->{'body.head'}->dateline->{'story.date'};
        $id = $this->getId();
        return 'urn:newsml:multimedia.efeservicios.com:'.$date.':'.$id.':2';
    }

    /**
     * Returns an integer between 1 and 5 that represents the priority level
     *
     * @return int the priority level
     **/
    public function getPriority()
    {
        $priority = $this->getData()->xpath('//head/meta[@name="prioridad"]');
        $priority = $priority[0]->attributes();
        $priority = (string) $priority->content;

        $priorities = array(
            '10' => 1,
            '20' => 2,
            '25' => 3,
            '30' => 4,
            // From Pandora
            'U'  => 4,
            'R'  => 3,
            'B'  => 2,
        );

        if (array_key_exists($priority, $priorities)) {
            return $priorities[$priority];
        } else {
            return 1;
        }
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
        $attributes =
            (string) $this->getData()->body->{'body.head'}->dateline->{'story.date'};


        // '20130315T150100+0000'
        return \DateTime::createFromFormat(
            'Ymd\THisP',
            $attributes
        );
    }

    /**
     * Checks if the data provided could be handled by the class
     *
     * @return string
     **/
    public static function checkFormat($data, $xmlFile)
    {
        $node = $data->xpath("/nitf");

        if (!is_array($node) || empty($node)) {
            throw new \Exception(sprintf(_('Not a valid NITF file')));
        }
        return true;
    }
}
