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
     * Returns the content from caption component.
     *
     * @param SimpleXMLObject $data The parsed data.
     *
     * @return Resource The content.
     */
    public function getContent($data)
    {
         $files = $data->xpath('/NewsComponent/ContentItem');

        if (empty($files)) {
            return null;
        }

        foreach ($files as $file) {
            $file = simplexml_load_string($file->asXML());

            // Ignore videos
            $caption = $file->xpath('/ContentItem/MediaType[@FormalName="Caption"]');

            if (!empty($caption)) {
                $caption = simplexml_load_string($caption->asXML());

                $parser = $this->factory->get($caption);
                return $parser->parse($caption);
            }
        }

        return null;
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
        $files = $data->xpath('/NewsComponent/ContentItem');

        if (empty($files)) {
            return '';
        }

        $height = 0;
        $i      = 0;
        $index  = 0;

        foreach ($files as $file) {
            $file = simplexml_load_string($file->asXML());

            // Ignore videos
            $video = $file->xpath('/ContentItem/MediaType[@FormalName="Video"]');

            if (empty($video)) {
                $h = $file->xpath('/ContentItem/Characteristics/Property[@FormalName="Height"]');

                if (!empty($h) && (string) $h[0]->attributes()->Value >= $height) {
                    $height   = (string) $h[0]->attributes()->Value;
                    $index = $i;
                }
            }

            $i++;
        }

        return simplexml_load_string($files[$index]->asXML());
    }

    /**
     * Returns the filename from the parsed data.
     *
     * @param SimpleXMLObject $data The parsed data.
     *
     * @return string The file name.
     */
    public function getFilename($data)
    {
        $filename = $data->xpath('/ContentItem/Characteristics/Property[@FormalName="EFE_Filename"]');

        if (!empty($filename)) {
            return (string) $filename[0]->attributes()->Value;
        }

        return '';
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
    public function getSummary($data)
    {
        $title = $data->xpath('/NewsComponent/NewsLines/HeadLine');
        if (empty($title)) {
            return '';
        }
        $title = (string) $title[0];
        return iconv(mb_detect_encoding($title), "UTF-8", $title);
    }

    /**
     * Returns the URL from the parsed data.
     *
     * @param SimpleXMLObject $data The parsed data.
     *
     * @return string The URL.
     */
    public function getUrl($data)
    {
        $url = $data->xpath('/ContentItem');

        if (!empty($url)) {
            return (string) $url[0]->attributes()->Href;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $this->bag['agency_name'] = $this->getAgencyName($data);

        $file = $this->getFile($data);

        $photo = new Resource();

        $photo->agency_name  = $this->bag['agency_name'];
        $photo->extension    = substr($file, strrpos($file, '.') + 1);
        $photo->file_path    = $this->getUrl($file);
        $photo->file_name    = $this->getFilename($file);
        $photo->id           = $this->getId($data);
        $photo->image_type   = 'image/' . $photo->extension;
        $photo->summary      = $this->getSummary($data);
        $photo->title        = $this->getTitle($file);
        $photo->type         = 'photo';

        $content = $this->getContent($data);

        if (is_object($content)) {
            $photo->merge($content);
        }

        return $photo;
    }
}
