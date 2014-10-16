<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Diéguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource\Format\Opennemas;

use Onm\Import\DataSource\FormatInterface;
use Onm\Import\DataSource\FormatAbstract;

/**
 * Handles all the operations for Binary data
 *
 * @package Onm_Import
 **/
class Binary extends FormatAbstract implements FormatInterface
{
    /**
     * undocumented class variable
     *
     * @var string
     **/
    private $data;

    /**
     * Instantiates the NITF DOM data from an SimpleXML object
     *
     * @return void
     **/
    public function __construct($data)
    {
        $this->data = $data;
        $this->data['created_time'] = \DateTime::createFromFormat(\DateTime::ISO8601, $this->data['created_time']);
        $this->data['author'] = json_decode($this->data['author']);

        if (array_key_exists('photos', $this->data)) {
            foreach ($this->data['photos'] as &$photo) {
                $photo = new Component\MultimediaResource($photo);
            }
        }
        if (array_key_exists('videos', $this->data)) {
            foreach ($this->data['videos'] as &$photo) {
                $photo = new Component\MultimediaResource($photo);
            }
        }
        $this->load($data);
    }

    /**
     * Populates the object information
     *
     * @return Europapress the populated object
     **/
    public function load()
    {
        $this->id           = $this->getId();
        $this->urn          = $this->getUrn();
        $this->pretitle     = $this->getPretitle();
        $this->title        = $this->getTitle();
        $this->priority     = $this->getPriority();
        $this->tags         = $this->getTags();
        $this->created_time = $this->getCreatedTime();
        $this->body         = $this->getBody();
        $this->agency_name  = $this->getServiceName();
        $this->photos       = $this->getPhotos();
        $this->author       = $this->getRightsOwner();
        $this->author_img   = $this->getRightsOwnerPhoto();
        $this->videos       = $this->getVideos();
        $this->source_id    = $this->getSourceId();
        $this->xml_file     = $this->getXmlFile();
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
            case 'author':
                return $this->getRightsOwner();

                break;
            case 'author_img':
                return $this->getRightsOwnerPhoto();

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
        return $this->getData()['agency_name'];
    }

    /**
     * Returns the id for this NITF resource
     *
     * @return int the id
     **/
    public function getId()
    {
        return $this->getData()['id'];
    }

    /**
     * Returns the title for this NITF resource
     *
     * @return string the title
     **/
    public function getTitle()
    {
        return $this->getData()['title'];
    }

    /**
     * Returns the pretitle for this NITF resource
     *
     * @return string the pretitle
     **/
    public function getPreTitle()
    {
        return $this->getData()['pretitle'];
    }

    /**
     * Returns the summary for this NITF resource
     *
     * @return string the summary
     **/
    public function getSummary()
    {
        return $this->getData()['summary'];
    }

    /**
     * Returns the body for this NITF resource
     *
     * @return string the body
     **/
    public function getBody()
    {
        return $this->getData()['body'];
    }

    /**
     * Returns the unique urn of the element
     *
     * @return string the urn
     **/
    public function getUrn()
    {
        return $this->getData()['urn'];
    }

    /**
     * Returns an integer between 1 and 5 that represents the priority level
     *
     * @return int the priority level
     **/
    public function getPriority()
    {
        return $this->getData()['priority'];
    }

    /**
     * Returns the list of tags of this element
     *
     * @return int the priority level
     **/
    public function getTags()
    {
        return $this->getData()['tags'];
    }

    /**
     * Returns the category of the element
     *
     * @return int the priority level
     **/
    public function getCategory()
    {
        return $this->getData()['category'];
    }

    /**
     * Returns the created time for this NITF resource
     *
     * @return \DateTime the created time
     **/
    public function getCreatedTime()
    {
        return $this->getData()['created_time'];
    }

    /**
     * Returns the created time for this NITF resource
     *
     * @return \DateTime the created time
     **/
    public function getRightsOwner()
    {
        return $this->getData()['author'];
    }

    /**
     * Returns the created time for this NITF resource
     *
     * @return \DateTime the created time
     **/
    public function getRightsOwnerPhoto()
    {
        return $this->getData()['author_img'];
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getPhotos()
    {
        return $this->getData()['photos'];
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function hasPhotos()
    {
        return count($this->getPhotos()) > 0;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getVideos()
    {
        return $this->getData()['videos'];
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function hasVideos()
    {
        return count($this->getVideos()) > 0;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getMetaData()
    {
        return $this->getData()['opennemas'];
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getSourceId()
    {
        return $this->getData()['source_id'];
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getXmlFile()
    {
        return $this->getData()['xml_file'];
    }

    /**
     * Checks if the data provided could be handled by the class
     *
     * @param SimpleXMLElement $data the XML data
     * @param string $xmlFile the file path
     *
     * @return string
     **/
    public static function checkFormat($data, $xmlFile)
    {
        return false;
    }
}
