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

use Onm\Import\DataSource\FormatInterface;
use Onm\Import\DataSource\FormatAbstract;
use Onm\Settings as s;

/**
 * Class that handles an Europapress news provider xml file
 *
 * @package Onm_Import_DataSource
 **/
class Europapress extends FormatAbstract implements FormatInterface
{
    private $data = null;
    /**
     * Initializes the object instance
     *
     * @param string $xmlFile the XML file that contains information about an EP new
     *
     * @return FormatInterface
     */
    public function __construct($xmlFile)
    {
        $this->xmlFile = basename($xmlFile);

        $baseAgency       = s::get('site_agency');
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
     * Populates the object information
     *
     * @return Europapress the populated object
     **/
    public function load()
    {
        $this->id           = $this->getId();
        $this->urn          = $this->getUrn();
        $this->pretitle     = $this->getPretitle();
        $this->title        = $this->getTitle();
        $this->priority     = $this->getPriority();
        $this->tags         = $this->getTags();
        $this->created_time = $this->getCreatedTime();
        $this->body         = $this->getBody();
        $this->agency_name  = $this->getServiceName();
        $this->texts        = $this->getTexts();
        $this->photos       = $this->getPhotos();
        $this->videos       = $this->getVideos();
        // $this->audios       = $this->getAudios();
        // $this->files        = $this->getFiles();
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
                return $this->getId();

                break;
            case 'urn':
                return $this->getUrn();

                break;
            case 'pretitle':
                return $this->getPretitle();

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
            case 'agency_name':
                return $this->getServiceName();

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

    public function getTexts()
    {
        return null;
    }

    /*
     * Return an array of localized categories
     *
     * @param $arg
     */
    public static function getOriginalCategories()
    {
        return array(
            'ACE' => _('Society'),
            'AGR' => _('Agriculture'),
            'AYU' => _('Council'),
            'CUL' => _('Society (CUL)'),
            'CYA' => _('Event CYA'),
            'CYT' => _('Society CYT'),
            'DCG' => _('Disturbs'),
            'DEP' => _('Sports'),
            'ECO' => _('Economy'),
            'EDU' => _('Society Education EDU'),
            'GAL' => _('GALICIA'),
            'JEI' => _('JUSTICIA'),
            'MEA' => _('Environment'),
            'OPI' => _('Opinion'),
            'OPL' => _('Politics OPL'),
            'POL' => _('Politics POL'),
            'PRT' => _('TV Guide'),
            'REL' => _('Society REL'),
            'SAN' => _('Society SAN'),
            'SOC' => _('Society SOC'),
            'SUC' => _('Event SUC'),
            'SYS' => _('Society SYS'),
            'TOR' => _('Society Toros'),
            'TRI' => _('Justice'),
        );
    }

    /*
     * Retrieves a localized string of category from identifier
     *
     * @param string $categoryName the category identifier
     */
    public static function matchCategoryName($categoryName)
    {
        if (empty($categoryName)) {
            throw new \ArgumentException();
        }

        $categories = self::getOriginalCategories();
        if (array_key_exists($categoryName, $categories)) {
            $category = $categories[(string) $categoryName];
        } else {
            $category = $categoryName;
        }

        return $category;
    }

    /*
     * Return an array of localized priorities
     *
     * @param $arg
     */
    public static function getOriginalPriorities()
    {
        return array(
            '10' => 1,
            '20' => 2,
            '25' => 3,
            '30' => 4,
            // From Pandora
            'U'  => 4,
            'R'  => 3,
            'B'  => 2,
        );
    }

    /*
     * Retrives a localized string for the priority from identifier
     *
     * @param string $priority the priority identifier
     */
    public static function matchPriority($priority)
    {
        if (empty($priority)) {
            $priority = '30';
        }
        $priorities = self::getOriginalPriorities();
        if (array_key_exists($priority, $priorities)) {
            $priority = $priorities[(string) $priority];
        } else {
            $priority = $priority;
        }

        return $priority;
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

    /**
     * Returns the name of the service that authored this element
     *
     * @return string the service name
     **/
    public function getServiceName()
    {
        return 'Europa Press';
    }

    /**
     * Returns the name of the service that authored this element
     *
     * @return string the service name
     **/
    public function getServicePartyName()
    {
        return 'Europa Press';
    }

    /**
     * Returns the id of the element
     *
     * @return string the title
     **/
    public function getId()
    {
        return (int) $this->data->CODIGO;
    }

    /**
     * Returns the title of the element
     *
     * @return string the title
     **/
    public function getTitle()
    {
        return (string) iconv(mb_detect_encoding($this->data->TITULAR), "UTF-8", $this->data->TITULAR);
    }

    /**
     * Returns the pretitle of the element
     *
     * @return string the pretitle
     **/
    public function getPretitle()
    {
        return (string) iconv(mb_detect_encoding($this->data->ANTETITULO), "UTF-8", $this->data->ANTETITULO);
    }

    /**
     * Returns the summary of the element
     *
     * @return string the summary
     **/
    public function getSummary()
    {
        return (string) iconv(mb_detect_encoding($this->data->ENTRADILLA), "UTF-8", $this->data->ENTRADILLA);
    }

    /**
     * Returns the body of the element
     *
     * @return string the body
     **/
    public function getBody()
    {
        $title = (string) $this->data->CONTENIDO;

        $titleClean = nl2br($title);

        return iconv(mb_detect_encoding($titleClean), "UTF-8", $titleClean);
    }

    /**
     * Returns the unique urn of the element
     *
     * @return string the urn
     **/
    public function getUrn()
    {
        $createdTime = $this->getCreatedTime();

        return 'urn:newsml:europapress.es:'
            . $createdTime->format('Ymd\THisP00').':'
            .$this->getId().':2';
    }

    /**
     * Returns an integer between 1 and 5 that represents the priority level
     *
     * @return int the priority level
     **/
    public function getPriority()
    {
        return self::matchPriority((string) $this->data->PRIORIDAD);
    }

    /**
     * Returns the list of tags of this element
     *
     * @return int the priority level
     **/
    public function getTags()
    {
        return array();
    }

    /**
     * Returns the category of the element
     *
     * @return int the priority level
     **/
    public function getCategory()
    {
        return self::matchCategoryName((string) $this->data->SECCION);
    }

    /**
     * Returns the creation datetime of this element
     *
     * @return DateTime the datetime of the element
     **/
    public function getCreatedTime()
    {
        $dateFormat = 'd/m/Y H:i:s';
        $originalDate = $this->data->FECHA.' '.$this->data->HORA;
        $date = \DateTime::createFromFormat(
            $dateFormat,
            $originalDate,
            new \DateTimeZone('Europe/Madrid')
        );

        $date = \DateTime::createFromFormat('d/m/Y H:i:s P', $date->format('d/m/Y H:i:s P'));

        return $date;
    }

    /**
     * Checks if the data provided could be handled by the class
     *
     * @param SimpleXMLElement $data the XML data
     * @param string $xmlFile the file path
     *
     * @return string
     **/
    public static function checkFormat($data, $xmlFile)
    {
        if ($data->CODIGO->count() <= 0) {
            throw new \Exception(sprintf(_('File %s is not a valid Europapress file'), $xmlFile));
        }

        return true;
    }

    /**
     * Returns the available photos in this multimedia package
     *
     * @return void
     **/
    public function getPhotos()
    {
        if (!isset($this->photos)) {
            $this->photos = array();
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
            $this->videos = array();
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
        return array();
    }

    /**
     * Returns the available Documentary modules in this multimedia package
     *
     * @return void
     **/
    public function getModdocs()
    {
        return array();
    }

    /**
     * Returns the available files in this multimedia package
     *
     * @return void
     **/
    public function getFiles()
    {
        return array();
    }

    /**
     * Returns the opennemas metadata
     *
     * @return string the property value
     **/
    public function getOpennemasData($property)
    {
        return null;
    }
}
