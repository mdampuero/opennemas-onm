<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Import\DataSource\Format;

use Onm\Settings as s;
use Onm\Import\DataSource\FormatInterface;
use Onm\Import\DataSource\Format\NewsMLG1Component\Video;
use Onm\Import\DataSource\Format\NewsMLG1Component\Photo;

class NewsMLG1 implements FormatInterface
{
    private $data = null;

    /**
     * Initializes the class given an xmlFile path
     *
     * @param string $xmlFile the XML file path
     *
     * @return NewsMLG1
     */
    public function __construct($xmlFile)
    {
        $this->xmlFile = basename($xmlFile);

        $baseAgency = s::get('site_agency');
        $this->agencyName = $baseAgency.' | Europapress';

        if (file_exists($xmlFile)) {
            if (filesize($xmlFile) < 2) {
                throw new \Exception(
                    sprintf(_("File '%d' can't be loaded."), $xmlFile)
                );
            }

            $this->data = simplexml_load_file(
                $xmlFile,
                null,
                LIBXML_NOERROR | LIBXML_NOWARNING
            );
            if (!$this->data) {
                throw new \Exception(
                    sprintf(_("File '%d' can't be loaded."), $xmlFile)
                );
            }

            $this->checkFormat($this->data, $xmlFile);
        } else {
            throw new \Exception(
                sprintf(_("File '%d' doesn't exists."), $xmlFile)
            );
        }

        return $this;
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
            case 'summary':
                return $this->getSummary();

                break;
            case 'pretitle':
                return $this->getPretitle();

                break;
            case 'agency_name':
                $this->getServiceName();

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
     * Returns the name of the service that authored this element
     *
     * @return string the service name
     **/
    public function getServiceName()
    {
        $rawAgencyName = $this->getData()
            ->NewsEnvelope->SentFrom->Party
            ->xpath("//Property[@FormalName=\"Organization\"]");

        return (string) $rawAgencyName[0]->attributes()->Value;
    }

    /**
     * Returns the name of the service that authored this element
     *
     * @return string the service name
     **/
    public function getServicePartyName()
    {
        $agencyName = $this->getData()
            ->NewsEnvelope->SentFrom->Party->attributes()->FormalName;

        return (string) $agencyName;
    }

    /**
     * Returns the id of the element
     *
     * @return string the title
     **/
    public function getId()
    {
        return (string) $this->getData()->NewsItem->Identification->NewsIdentifier->NewsItemId;
    }


    /**
     * Returns the title of the element
     *
     * @return string the title
     **/
    public function getTitle()
    {
        return (string) $this->getData()->NewsItem->NewsComponent->NewsLines->HeadLine;
    }

    /**
     * Returns the pretitle of the element
     *
     * @return string the pretitle
     **/
    public function getPretitle()
    {
        return (string) $this->getData()->NewsItem->NewsComponent->NewsLines->SubHeadLine;
    }

    /**
     * Returns the summary of the element
     *
     * @return string the summary
     **/
    public function getSummary()
    {
        $summaries = $this->getData()->xpath(
            "//nitf/body/body.head/abstract"
        );
        $summary   = "";
        foreach ($summaries[0]->children() as $child) {
            $summary .= "<p>".sprintf("%s", $child)."</p>";
        }

        return $summary;
    }

    /**
     * Returns the body of the element
     *
     * @return string the body
     **/
    public function getBody()
    {
        if (count($this->texts) > 0) {
            return $this->texts[0]->body;
        }
    }

    /**
     * Returns the unique urn of the element
     *
     * @return string the urn
     **/
    public function getUrn()
    {
        return (string) $this->getData()->NewsItem->Identification
                                    ->NewsIdentifier->PublicIdentifier;
    }

    /**
     * Returns an integer between 1 and 5 that represents the priority level
     *
     * @return int the priority level
     **/
    public function getPriority()
    {
        $rawUrgency =  $this->getData()->xpath("//NewsItem/NewsManagement/Urgency");

        return (int) $rawUrgency[0]->attributes()->FormalName;
    }

    /**
     * Returns the list of tags of this element
     *
     * @return int the priority level
     **/
    public function getTags()
    {
        $rawCategory =
            $this->getData()->NewsItem->NewsComponent->DescriptiveMetadata
            ->xpath("//Property[@FormalName=\"Tesauro\"]");

        if (empty($rawCategory)) {
            return array();
        }
        $rawTags = (string) $rawCategory[0]->attributes()->Value;
        $tagGroups = explode(";", $rawTags);

        $tags = array();
        foreach ($tagGroups as $group) {
            preg_match('@(.*):(.*)@', $group, $matches);
            $tags [$matches[1]]= $matches[2];
        }

        return $tags;
    }

    /**
     * Returns the category of the element
     *
     * @return int the priority level
     **/
    public function getCategory()
    {
        return '';
    }

    /**
     * Returns the creation datetime of this element
     *
     * @return DateTime the datetime of the element
     **/
    public function getCreatedTime()
    {
        $originalDate = (string) $this->getData()
                                    ->NewsItem->NewsManagement
                                    ->ThisRevisionCreated;

        // ISO 8601 doesn't match this date 20111211T103900+0000
        $originalDate = preg_replace('@\+(\d){4}$@', '', $originalDate);

        return \DateTime::createFromFormat(
            'Ymd\THis',
            $originalDate
        );
    }

    /**
     * Checks if the data provided could be handled by the class
     *
     * @param SimpleXmlElement $file the XML file to parse
     * @param string           $xmlFile the path to the xml file
     *
     * @return string
     **/
    public static function checkFormat($data = null, $xmlFile = null)
    {
        $contents = $data->xpath(
            "//NewsItem/NewsComponent/NewsComponent[@Duid]"
        );

        if (count($contents) == 0 || $data->NewsItem->count() <= 0) {
            throw new \Exception(sprintf(_('File %s is not a valid NewsMLEuropapres file'), $xmlFile));
        }
        $title = (string) $data->NewsItem->NewsComponent->NewsLines->HeadLine;

        if (!(string) $data->NewsEnvelope || empty($title)) {
            throw new \Exception(sprintf(_('File %s is not a valid NewsMLG1 file'), $xmlFile));
        }

        return true;
    }

    /**
     * Returns the available text in this multimedia package
     *
     * @return void
     **/
    public function getTexts()
    {
        $contents = $this->getData()->xpath(
            "//NewsItem/NewsComponent/NewsComponent"
            ."[@Duid=\"multimedia_".$this->id.".multimedia.texts\"]"
        );

        $texts = null;
        if (isset($contents[0]) && $contents[0]->NewsComponent) {
            foreach ($contents[0]->NewsComponent as $component) {
                $nitf = new \Onm\Import\DataSource\Format\NewsMLG1Component\NITF($component);
                $texts []= $nitf;
            }
        }

        return $texts;
    }

    /**
     * Returns the available photos in this multimedia package
     *
     * @return void
     **/
    public function getPhotos()
    {
        if (!isset($this->photos)) {
            $contents = $this->getData()->xpath(
                "//NewsItem/NewsComponent/NewsComponent"
                ."[@Duid=\"multimedia_".$this->id.".multimedia.photos\"]"
            );

            if (count($contents) > 0) {
                $this->photos = array();
                foreach ($contents[0] as $componentName => $component) {
                    if ($componentName == 'NewsComponent') {
                        $photoComponent = new Photo($component);
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
     **/
    public function getVideos()
    {
        if (!isset($this->videos)) {
            $contents = $this->getData()->xpath(
                "//NewsItem/NewsComponent/NewsComponent"
                ."[@Duid=\"multimedia_".$this->id.".multimedia.videos\"]"
            );

            if (count($contents) > 0) {
                $this->videos = array();
                foreach ($contents[0] as $componentName => $component) {
                    if ($componentName == 'NewsComponent') {
                        $videoComponent = new Video($component);
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
     **/
    public function getAudios()
    {
        $contents = $this->getData()->xpath(
            "//NewsItem/NewsComponent/NewsComponent"
            ."[@Duid=\"multimedia_".$this->id.".multimedia.audios\"]"
        );

        return $contents;
    }

    /**
     * Returns the available Documentary modules in this multimedia package
     *
     * @return void
     **/
    public function getModdocs()
    {
        $contents = $this->getData()->xpath(
            "//NewsItem/NewsComponent/NewsComponent"
            ."[@Duid=\"multimedia_".$this->id.".multimedia.moddocs\"]"
        );

        return $contents;
    }

    /**
     * Returns the available files in this multimedia package
     *
     * @return void
     **/
    public function getFiles()
    {
        $contents = $this->getData()->xpath(
            "//NewsItem/NewsComponent/NewsComponent"
            ."[@Duid=\"multimedia_".$this->id.".multimedia.files\"]"
        );

        return $contents;
    }

    /**
     * Returns the available categories present in available news
     *
     * @return array
     **/
    public static function getOriginalCategories()
    {
        return array();
    }

    /**
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
