<?php
/**
 * Defines the Onm\Import\DataSource\Format\NewsMLG1Component\ResourceAbstract
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Import_DataSource_Format_NewsMLG1Component
 **/
namespace Onm\Import\DataSource\Format\Opennemas\Component;

/**
 * Handles all the common methods for Resources in NewsMLG1Components
 *
 * @package Onm_Import_DataSource_Format_NewsMLG1Component
 **/
class MultimediaResource
{
    /**
     * Internall data storage
     *
     * @var string
     **/
    private $data;

    /**
     * Instantiates the Photo DOM data from an SimpleXML object
     *
     * @param SimpleXmlElement $data the XML data fromt the file
     *
     * @return void
     **/
    public function __construct($data)
    {
        $this->data = $data;
        $this->data['created_time'] = \DateTime::createFromFormat(\DateTime::ISO8601, $this->data['created_time']);

        $this->load();
    }

    /**
     * Populates the object information
     *
     * @return Europapress the populated object
     **/
    public function load()
    {
        $this->id           = $this->getId();
        $this->title        = $this->getTitle();
        $this->created_time = $this->getCreatedTime();
        $this->name         = $this->getName();
        $this->file_type    = $this->getFileType();
        $this->file_path    = $this->getFilePath();

        return $this;
    }

    /**
     * Returns the element id
     *
     * @return string the element id
     **/
    public function getId()
    {
        return $this->getData()['id'];
    }

    /**
     * Returns the element name
     *
     * @return string the element name
     **/
    public function getName()
    {
        return $this->getData()['name'];
    }

    /**
     * Returns the element title
     *
     * @return string the element title
     **/
    public function getTitle()
    {
        return $this->getData()['title'];
    }

    /**
     * Returns the element created time
     *
     * @return DateTime the date time object
     **/
    public function getCreatedTime()
    {
        return $this->getData()['created_time'];
    }

    /**
     * Returns the element file type
     *
     * @return string the file type
     **/
    public function getFileType()
    {
        return $this->getData()['file_type'];
    }

    /**
     * Returns the element file path
     *
     * @return string the file path
     **/
    public function getFilePath()
    {
        return $this->getData()['file_path'];
    }

    /**
     * Magic method for translate properties into XML elements
     *
     * @param string $propertyName the name of the property to get
     */
    public function __get($propertyName)
    {
        switch ($propertyName)
        {
            case 'id':
                return $this->getData()->getId();

                break;
            case 'title':
                return $this->getData()->getTitle();

                break;
            case 'name':
                return $this->getData()->getName();

                break;
            case 'file_type':
                return $this->getData()->getFileType();

                break;
            case 'file_path':
                return $this->getData()->getFilePath();

                break;
            case 'created_time':
                return $this->getData()->getCreatedTime();

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
     * Converts the object to an array
     *
     * @return array the array of information
     **/
    public function toArray()
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'title'        => $this->title,
            'created_time' => $this->created_time->format(\DateTime::ISO8601),
            'file_type'    => $this->file_type,
            'file_path'    => $this->file_path,
        ];
    }
}
