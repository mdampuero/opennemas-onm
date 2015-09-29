<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Parser\NewsML\NewsMLComponent;

use Framework\Import\Parser\NewsML\NewsML;
use Framework\Import\Resource\Resource;

/**
 * Parses NewsComponent that represent a photo resource from NewsML files.
 */
class NewsMLComponentPhotoEFE extends NewsML
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }
        $node = $data->xpath(
            '/NewsComponent/ContentItem/MediaType[@FormalName="Photo"]'
        );
        if (!empty($node)) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAgencyName($data)
    {
        $agency = $data
            ->xpath('/NewsComponent/AdministrativeMetadata/Provider/Party');
        if (is_array($agency) && count($agency) > 0) {
            return (string) $agency[0]->attributes()->FormalName;
        }
        return '';
    }

    /**
     * Return the file form the parsed data.
     *
     * @param SimpleXMLObject $data The parsed data.
     *
     * @return string The file name.
     */
    public function getFile($data)
    {
        $file = $data->xpath('/NewsComponent/ContentItem/Characteristics/Property[@FormalName="EFE_Filename"]');

        if (!empty($file)) {
            return (string) $file[0]->attributes()->Value;
        }

        $file = $data->xpath('/NewsComponent/ContentItem');
        if (empty($file)) {
            return '';
        }

        return (string) $file[0]->attributes()->Href;
    }

    /**
     * {@inheritdoc}
     */
    public function getId($data)
    {
        $id = $data->xpath('/NewsComponent');
        if (is_array($id) && !empty($id)) {
            $id = (string) $id[0]->attributes()->Duid;
            return iconv(mb_detect_encoding($id), "UTF-8", $id);
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle($data)
    {
        $title = $data->xpath('/NewsComponent/NewsLines/HeadLine');
        if (empty($title)) {
            return '';
        }
        $title = (string) $title[0];
        return iconv(mb_detect_encoding($title), "UTF-8", $title);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $this->bag['agency_name'] = $this->getAgencyName($data);
        $photo = new Resource();
        $photo->agency_name  = $this->bag['agency_name'];
        $photo->file         = $this->getFile($data);
        $photo->id           = $this->getId($data);
        $photo->summary      = $this->getTitle($data);
        $photo->title        = $this->getFile($data);
        $photo->type         = 'photo';
        return $photo;
    }
}
