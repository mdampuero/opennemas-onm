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
        $q = '/NewsComponent/NewsComponent';

        // Check if NewsMLComponentPhoto
        $count = 0;
        $count += count($data->xpath($q . '/Role[@FormalName="Caption"]'));
        $count += count($data->xpath($q . '/Role[@FormalName="Preview"]'));
        $count += count($data->xpath($q . '/Role[@FormalName="Quicklook"]'));
        $count += count($data->xpath($q . '/Role[@FormalName="Thumbnail"]'));

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
                $body .= "<p>$p</p>";
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
        $components = $data->xpath('/NewsComponent/NewsComponent');

        $search = $this->getPhotoPath($data);

        if (empty($search)) {
            return '';
        }

        foreach ($components as $component) {
            $component = simplexml_load_string($component->asXML());

            $file = $component->xpath($search);

            if (!empty($file)) {
                $file = $component->xpath('//ContentItem');

                if (!empty($file)) {
                    return (string) $file[0]->attributes()->Href;
                }
            }
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

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $this->bag['agency_name'] = $this->getAgencyName($data);

        $photo = new Resource();

        $file = $this->getFile($data);

        $photo->agency_name  = $this->bag['agency_name'];
        $photo->extension    = substr($file, strrpos($file, '.') + 1);
        $photo->file         = $file;
        $photo->id           = $this->getId($data);
        $photo->summary      = $this->getSummary($data);
        $photo->title        = $this->getTitle($data);
        $photo->type         = 'photo';

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
