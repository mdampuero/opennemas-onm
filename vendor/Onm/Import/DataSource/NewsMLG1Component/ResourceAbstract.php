<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Import\DataSource\NewsMLG1Component;

/**
 * Handles all the common methods for Resources in NewsMLG1Components
 *
 * @package Onm
 * @subpackage Import_DataSource_NewsMLG1Component
 **/
abstract class ResourceAbstract
{

    /**
     * Instantiates the Photo DOM data from an SimpleXML object
     *
     * @return void
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
                $attributes = $this->getData()->attributes();

                return (string) $attributes->Euid;
                break;

            case 'title':
                $content =
                    $this->getData()
                    ->NewsComponent->ContentItem->DataContent
                    ->xpath('//body.content');

                return (string) $content[1]->p;
                break;

            case 'file_type':
                $fileType =
                    $this->getData()
                    ->NewsComponent->ContentItem->MimeType
                    ->attributes()->FormalName;

                return (string) $fileType;
                break;

            case 'file_path':
                $filePath =
                    $this->getData()
                    ->NewsComponent->ContentItem
                    ->attributes()->Href;

                return (string) $filePath;
                break;

            case 'created_time':
                $originalDate =
                    (string) $this->getData()
                    ->DescriptiveMetadata->DateLineDate;
                // ISO 8601 doesn't match this date 20111211T103900+0000
                $originalDate = preg_replace('@\+(\d){4}$@', '', $originalDate);

                return \DateTime::createFromFormat('Ymd\THis', $originalDate);
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

} // END class ResourceAbstract
