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
namespace Onm\Import\DataSource\Format\EuropaPress\Component;

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

        $this->load();
    }

    /**
     * Populates the object information
     *
     * @return Europapress the populated object
     **/
    public function load()
    {
        $this->id           = $this->id;
        $this->title        = $this->title;
        $this->created_time = $this->created_time;
        $this->name         = $this->name;
        $this->file_type    = $this->file_type;
        $this->file_path    = $this->file_path;
        $this->media_type   = $this->media_type;

        return $this;
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
                return (string) $this->getData()->CODIGO;

                break;
            case 'title':
                return (string) $this->getData()->FOTO->PIE;

                break;
            case 'name':
                return (string) $this->getData()->FOTO->NOMBRE;

                break;
            case 'file_type':
                return 'image/'.substr($this->getData()->FOTO->EXTENSION, 1);

                break;
            case 'file_path':
                return (string) $this->getData()->FOTO->NOMBRE;

                break;
            case 'media_type':
                return substr($this->getData()->FOTO->EXTENSION, 1);

                break;
            case 'created_time':
                $dateFormat = 'd/m/Y H:i:s';
                $originalDate = $this->data->FECHA.' '.$this->data->HORA;
                $date = \DateTime::createFromFormat(
                    $dateFormat,
                    $originalDate,
                    new \DateTimeZone('Europe/Madrid')
                );

                $date = \DateTime::createFromFormat('d/m/Y H:i:s P', $date->format('d/m/Y H:i:s P'));

                return $date;

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
            'media_type'   => $this->media_type,
        ];
    }
}
