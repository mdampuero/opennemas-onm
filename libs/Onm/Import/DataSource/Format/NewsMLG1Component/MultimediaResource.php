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
namespace Onm\Import\DataSource\Format\NewsMLG1Component;

/**
 * Handles all the common methods for Resources in NewsMLG1Components
 *
 * @package Onm_Import_DataSource_Format_NewsMLG1Component
 **/
class MultimediaResource
{
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
        $this->name         = $this->name;
        $this->title        = $this->title;
        $this->created_time = $this->created_time;
        $this->file_type    = $this->file_type;
        $this->file_path    = $this->file_path;
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
                $attributes = $this->getData()->attributes();

                return (string) $attributes->Euid;

                break;
            case 'title':
                $contentTitle =
                    $this->getData()
                    ->NewsComponent[1]->ContentItem->DataContent
                    ->nitf->body->{'body.content'}->p;

                return (string) $contentTitle;

                break;
            case 'name':
                $content =
                    $this->getData()
                    ->NewsComponent->ContentItem->Characteristics
                    ->xpath("//Property[contains(@FormalName,'Filename')]");

                if (stripos($content[0]->attributes()->FormalName, 'EFE') !== false) {
                    foreach ($content as $key => $image) {
                        if ($key % 4 == 1) {
                            $imageName = (string) $image->attributes()->Value;
                        }
                    }
                } else {
                    foreach ($content as $image) {
                        $imageName = (string) $image->attributes()->Value;
                    }
                }

                return $imageName;

                break;
            case 'file_type':
                $fileType = $this->getData()->NewsComponent;

                if (count($fileType->ContentItem) > 1) {
                    $fileType = (string) $fileType->ContentItem[1]->MimeType->attributes()->FormalName;
                } else {
                    if (count($fileType->ContentItem->MimeType) > 1) {
                        $fileType = (string) $fileType->ContentItem->MimeType->attributes()->FormalName;
                    } else {
                        $fileType = (string) $fileType->ContentItem->attributes()->Href;

                        $info = pathinfo($fileType);
                        $extension = $info['extension'];

                        $fileType = 'image/'.$extension;
                    }
                }

                return $fileType;

                break;
            case 'file_path':

                $imageContentItems = $this->getData()->NewsComponent->ContentItem;

                if (count($imageContentItems) > 1) {
                    $filePath =  $imageContentItems[1]->attributes()->Href;
                } else {
                    $filePath =  $imageContentItems->attributes()->Href;
                }

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

    /**
     * Returns the internal data, use with caution
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function toArray()
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'title'        => $this->title,
            'created_time' => $this->created_time->format(\DateTime::RFC2822),
            'file_type'    => $this->file_type,
            'file_path'    => $this->file_path,
        ];
    }
}
