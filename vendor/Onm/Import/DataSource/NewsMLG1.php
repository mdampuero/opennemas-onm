<?php
/**
 *  Copyright (C) 2011 by OpenHost S.L.
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 **/
/**
 * (c) Copyright Mér Mai 25 17:07:03 2011 Fran Diéguez. All Rights Reserved.
*/
/*
 * class Europapress
 */
namespace Onm\Import\DataSource;
use Onm\Settings as s;

class NewsMLG1 {


    private $data = null;

    /**
    * Ensures that we always get one single instance
    *
    * @return object, the unique instance object
    * @author Fran Dieguez <fran@openhsot.es>
    **/
    static public function getInstance($config)
    {

        if ((!self::$instance instanceof self) or
            (count(array_diff($this->config, $config)) > 0))
        {
            self::$instance = new self($config);
        }

        return self::$instance;

    }

    /*
     * __construct()
     * @param $xmlFile, the XML file that contains information about an EP new
     */
    public function __construct($xmlFile) {

        $this->xmlFile = basename($xmlFile);

        $baseAgency = s::get('site_agency');
        $this->agencyName = $baseAgency.' | Europapress';

        if(file_exists($xmlFile)) {
            if (filesize($xmlFile) < 2) {
                throw new \Exception(sprintf(_("File '%d' can't be loaded."), $xmlFile));
            }

            $this->data = simplexml_load_file($xmlFile, NULL, LIBXML_NOERROR | LIBXML_NOWARNING);
            if (!$this->data) {
                throw new \Exception(sprintf(_("File '%d' can't be loaded."), $xmlFile));
            }
        } else {
            throw new \Exception(sprintf(_("File '%d' doesn't exists."), $xmlFile));
        }

        return $this;

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
                return (string)$this->getData()->NewsItem->Identification
                                    ->NewsIdentifier->NewsItemId;
                break;

            case 'urn':
                return (string)$this->getData()->NewsItem->Identification
                                    ->NewsIdentifier->PublicIdentifier;
                break;

            case 'title':
                return (string)$this->getData()->NewsItem->NewsComponent
                                    ->NewsLines->HeadLine;
                break;

            case 'priority':
                $rawUrgency =  $this->getData()
                                    ->xpath("//NewsItem/NewsManagement/Urgency");

                return (int)$rawUrgency[0]->attributes()->FormalName;
                break;

            case 'tags':
                $rawCategory = $this->getData()->NewsItem->NewsComponent
                                    ->DescriptiveMetadata->xpath("//Property[@FormalName=\"Tesauro\"]");
                $rawTags = (string)$rawCategory[0]->attributes()->Value;
                $tagGroups = explode(";", $rawTags);

                $tags = array();
                foreach ($tagGroups as $group) {
                    preg_match('@(.*):(.*)@', $group, $matches);
                    $tags [$matches[1]]= $matches[2];
                }

                return $tags;
                break;

            case 'created_time':
                $originalDate = (string)$this->getData()->NewsItem->NewsManagement
                                                        ->ThisRevisionCreated;

                // ISO 8601 doesn't match this date 20111211T103900+0000
                $originalDate = preg_replace('@\+(\d){4}$@', '', $originalDate);


                return \DateTime::createFromFormat(
                    'Ymd\THis',
                    $originalDate
                );
                break;

            case 'body':
                if (count($this->texts) > 0) {
                    return $this->texts[0]->body;
                }

                return;
                break;

            case 'agency_name':
                $rawAgencyName = $this->getData()->NewsEnvelope
                                      ->SentFrom->Party->xpath("//Property[@FormalName=\"Organization\"]");

                return (string)$rawAgencyName[0]->attributes()->Value;
                break;

            case 'texts':
            case 'photos':
            case 'videos':
            case 'audios':
            case 'moddocs':
            case 'files':
                return $this->{'get'.ucfirst($propertyName)}();
                break;

        }
    }

    /**
     * Returns the available text in this multimedia package
     *
     * @return void
     * @author
     **/
    public function getTexts()
    {
        $contents = $this->getData()->xpath("//NewsItem/NewsComponent/NewsComponent[@Duid=\"multimedia_".$this->id.".multimedia.texts\"]");

        $texts = null;
        if (isset($contents[0]) && $contents[0]->NewsComponent) {
            foreach ($contents[0]->NewsComponent as $component) {
                $nitf = new \Onm\Import\DataSource\NITF($component);
                $texts []= $nitf;
            }
        }

        return $texts;
    }

    /**
     * Returns the available photos in this multimedia package
     *
     * @return void
     * @author
     **/
    public function getPhotos()
    {
        if (!isset($this->photos)) {
            $contents = $this->getData()->xpath("//NewsItem/NewsComponent/NewsComponent[@Duid=\"multimedia_".$this->id.".multimedia.photos\"]");

            if (count($contents) > 0) {
                $this->photos = array();
                foreach ($contents[0] as $componentName => $component) {
                    if ($componentName == 'NewsComponent') {
                        $photoComponent = new \Onm\Import\DataSource\NewsMLG1Component\Photo($component);
                        $this->photos[$photoComponent->id] = $photoComponent;
                    }
                }
            } else {
                $this->photos = array();
            }
        }

        return $this->photos;
    }

    /**
     * Checks if this news component has photos
     *
     * @return boolean
     **/
    public function hasPhotos()
    {
        return count($this->getPhotos()) > 0;
    }

    /**
     * Returns the available images in this multimedia package
     *
     * @return void
     * @author
     **/
    public function getVideos()
    {
    if (!isset($this->videos)) {
        $contents = $this->getData()->xpath("//NewsItem/NewsComponent/NewsComponent[@Duid=\"multimedia_".$this->id.".multimedia.videos\"]");

        if (count($contents) > 0) {
            $this->videos = array();
            foreach ($contents[0] as $componentName => $component) {
                if ($componentName == 'NewsComponent') {
                    $videoComponent = new \Onm\Import\DataSource\NewsMLG1Component\Video($component);
                    $this->videos[$videoComponent->id] = $videoComponent;
                }
            }
        } else {
            $this->videos = array();
        }
    }

        return $this->videos;
    }

    /**
     * Checks if this news component has photos
     *
     * @return boolean
     **/
    public function hasVideos()
    {
        return count($this->getVideos()) > 0;
    }

    /**
     * Returns the available audios in this multimedia package
     *
     * @return void
     * @author
     **/
    public function getAudios()
    {
        $contents = $this->getData()->xpath("//NewsItem/NewsComponent/NewsComponent[@Duid=\"multimedia_".$this->id.".multimedia.audios\"]");

        return $contents;
    }

    /**
     * Returns the available Documentary modules in this multimedia package
     *
     * @return void
     * @author
     **/
    public function getModdocs()
    {
        $contents = $this->getData()->xpath("//NewsItem/NewsComponent/NewsComponent[@Duid=\"multimedia_".$this->id.".multimedia.moddocs\"]");

        return $contents;
    }

    /**
     * Returns the available files in this multimedia package
     *
     * @return void
     * @author
     **/
    public function getFiles()
    {
        $contents = $this->getData()->xpath("//NewsItem/NewsComponent/NewsComponent[@Duid=\"multimedia_".$this->id.".multimedia.files\"]");

        return $contents;
    }

    /**
     * Returns the available categories present in available news
     *
     * @return array
     **/
    static public function getOriginalCategories()
    {
        return array();
    }

    /*
     * Returns the internal data, use with caution
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Finds a regexp inside the title and content
     *
     * @return boolean
     **/
    public function hasContent($needle)
    {
        $needle = strtolower(\Onm\StringUtils::normalize($needle));
        $title = strtolower(\Onm\StringUtils::normalize($this->title));

        if (preg_match("@".$needle."@", $title)) {
            return true;
        }
        $body = strtolower(\Onm\StringUtils::normalize($this->body));
        if (preg_match("@".$needle."@", $body)) {
            return true;
        }

        return false;
    }


}
