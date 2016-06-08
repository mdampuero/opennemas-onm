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
class NewsMLComponentPhoto extends NewsML
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }

        if ($this->checkPhoto($data)) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the given data contains a photo.
     *
     * @param SimpleXMLObject $data The data to check.
     *
     * @return boolean True if the given data contains a photo. Otherwise,
     * return false.
     */
    public function checkPhoto($data)
    {
        $count    = 0;
        $elements = [];

        $elements['main'] = $data
            ->xpath('/NewsComponent/NewsComponent/Role[@FormalName="Main"]');

        $elements['caption'] = $data
            ->xpath('/NewsComponent/NewsComponent/Role[@FormalName="Caption"]');

        $elements['preview'] = $data
            ->xpath('/NewsComponent/NewsComponent/Role[@FormalName="Preview"]');

        $elements['quicklook'] = $data
            ->xpath('/NewsComponent/NewsComponent/Role[@FormalName="Quicklook"]');

        $elements['thumbnail'] = $data
            ->xpath('/NewsComponent/NewsComponent/Role[@FormalName="Thumbnail"]');

        foreach ($elements as $value) {
            if (count($value) === 1) {
                $count++;
            }
        }

        return $count > 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody($data)
    {
        $bodies = $data->xpath('//ContentItem');

        $body = '';
        if (is_array($bodies)
            && !empty($bodies)
            && !empty($bodies[0]->DataContent)
            && !empty($bodies[0]->DataContent->p)
        ) {
            foreach ($bodies[0]->DataContent->p as $p) {
                $body .= $p;
            }
        }

        return $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getAgencyName($data)
    {
        $agency = $data->xpath('/NewsComponent/AdministrativeMetadata/Provider/Party');

        if (is_array($agency) && count($agency) > 0) {
            return (string) $agency[0]->attributes()->FormalName;
        }

        return $this->getFromBag('agency_name');
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
        $files = $data->xpath('/NewsComponent/NewsComponent');

        if (empty($files)) {
            return null;
        }

        foreach ($files as $file) {
            $file = simplexml_load_string($file->asXML());

            // Ignore videos
            $caption = $file->xpath('Role[@FormalName="Caption"]');

            if (!empty($caption)) {
                $caption = simplexml_load_string($file->asXML());

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
        $files = $data->xpath('/NewsComponent/NewsComponent/ContentItem');

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
        $filename = $data->xpath('/ContentItem');

        if (!empty($filename)) {
            return (string) $filename[0]->attributes()->Href;
        }

        return $this->getFromBag('filename');
    }

    /**
     * Returns the photo height.
     *
     * @param SimpleXMLObject $data The parsed data.
     *
     * @return string The photo height.
     */
    public function getHeight($data)
    {
        $height = $data->xpath('/ContentItem/Characteristics/Property[@FormalName="Height"]');

        if (!empty($height)) {
            return (string) $height[0]->attributes()->Value;
        }

        return $this->getFromBag('height');
    }

    /**
     * {@inheritdoc}
     */
    public function getId($data)
    {
        $id = $data->xpath('/NewsComponent');

        if (is_array($id) && !empty($id)) {
            $id = (string) $id[0]->attributes()->Duid;
            $filename = $this->getFilename($data);
            return md5($filename).iconv(mb_detect_encoding($id), "UTF-8", $id);
        }

        return $this->getFromBag('id');
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary($data)
    {
        $summaries = $data->xpath('/NewsComponent//p');

        $summary = '';
        if (is_array($summaries) && !empty($summaries)) {
            foreach ($summaries as $s) {
                $summary .= '<p>' . (string) $s . '</p>';
            }

            $summary = iconv(mb_detect_encoding($summary), "UTF-8", $summary);
        }

        return $summary;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle($data)
    {
        $title = $data->xpath('/NewsComponent/NewsLines/HeadLine');

        if (is_array($title) && !empty($title)) {
            $title = (string) $title[0];
            return iconv(mb_detect_encoding($title), "UTF-8", $title);
        }

        return $this->getFromBag('title');
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

        return $this->getFromBag('url');
    }

    /**
     * Returns the photo width.
     *
     * @param SimpleXMLObject $data The parsed data.
     *
     * @return string The photo width.
     */
    public function getWidth($data)
    {
        $width = $data->xpath('/ContentItem/Characteristics/Property[@FormalName="Width"]');

        if (!empty($width)) {
            return (string) $width[0]->attributes()->Value;
        }

        return $this->getFromBag('width');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $this->bag['agency_name'] = $this->getAgencyName($data);
        $this->bag['id']          = $this->getId($data);

        $file     = $this->getFile($data);
        $filename = $this->getFilename($file);

        $photo = new Resource();

        $photo->agency_name  = $this->bag['agency_name'];
        $photo->extension    = substr($filename, strrpos($filename, '.') + 1);
        $photo->file_name    = $filename;
        $photo->file_path    = $this->getUrl($file);
        $photo->height       = $this->getHeight($file);
        $photo->id           = $this->getId($data);
        $photo->image_type   = 'image/' . $photo->extension;
        $photo->summary      = $this->getSummary($data);
        $photo->title        = $this->getTitle($file);
        $photo->type         = 'photo';
        $photo->urn          = $this->getUrn($file, 'photo');
        $photo->width        = $this->getWidth($file);

        $content = $this->getContent($data);

        if (is_object($content)) {
            $photo->merge((array) $content);
        }

        $photo->merge($this->bag);

        return $photo;
    }

    /**
     * Checks possible photo xpaths.
     *
     * @param SimpleXMLObject The data to search in.
     *
     * @return string The photo xpath.
     */
    protected function getPhotoPath($data)
    {
        // Check order
        $queries = [
            '//Role[@FormalName="Preview"]',
            '//Role[@FormalName="Quicklook"]',
            '//Role[@FormalName="Thumbnail"]'
        ];

        foreach ($queries as $q) {
            $file = $data->xpath($q);

            if (!empty($file)) {
                return $q;
            }
        }
    }
}
