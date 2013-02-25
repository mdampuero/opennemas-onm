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

/**
 * Class that handles an Europapress news provider xml file
 *
 * @package Onm_Import_DataSource
 **/
class Europapress
 // implements FormatInterface
{
    private $data = null;

    private static $priorityMap = array(
        '10'  => 4,
        '20' => 3,
        '25' => 2,
        '30' => 1,
        // From Pandora
        'U' => 4,
        'R' => 3,
        'B' => 2,
    );

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

    /*
     * Magic method for translate properties into XML elements
     *
     * @param string $propertyName the name of the property to get
     */
    public function __get($propertyName)
    {
        switch ($propertyName) {
            case 'id':
                return (int) $this->data->CODIGO;

                break;
            case 'agencyID':
                return (string) $this->data->AGENCIA;

                break;
            case 'priority':
                return self::matchPriority((string) $this->data->PRIORIDAD);

                break;
            case 'priorityNumber':
                return self::$priorityMap[(string) $this->data->PRIORIDAD];

                break;
            case 'service':
                return (string) $this->data->SERVICIO;

                break;
            case 'category':
                return self::matchCategoryName((string) $this->data->SECCION);

                break;
            case 'originalCategory':
                return (string) $this->data->SECCION;

                break;
            case 'informationType':
                return (string) $this->data->TIPOINFO;

                break;
            case 'key':
                return (string) $this->data->CLAVE;

                break;
            case 'created_time':

                $dateFormat = 'd/m/Y H:i:s';
                $originalDate = $this->data->FECHA.' '.$this->data->HORA;
                $date = \DateTime::createFromFormat(
                    $dateFormat,
                    $originalDate,
                    new \DateTimeZone('Europe/Madrid')
                );

                return $date;

                break;
            case 'pretitle':
                return (string) $this->data->ANTETITULO;

                break;
            case 'title':
                return (string) $this->data->TITULAR;

                break;
            case 'body':
                return nl2br((string) $this->data->CONTENIDO);

                break;
            case 'summary':
                return (string) $this->data->ENTRADILLA;

                break;
            case 'photos':
                return (array) $this->data->PHOTOS;

                break;
            case 'personajes':
                return (array) $this->data->PERSONAJES;

                break;
            case 'photos':
                return (array) $this->data->PHOTOS;

                break;
            case 'people':
                return (array) $this->data->PERSONAJES;

                break;
            case 'place':
                return (array) $this->data->LUGAR;

                break;
            case 'associatedDocs':
                return (array) $this->data->DOCS;

                break;
            case 'categories':
                return (array) $this->data->CATEGORIES;

                break;
            case 'dataCastID':
                return (array) $this->data->DATACASTID;

                break;
            case 'level':
                return (array) $this->data->LEVEL;

                break;
            case 'redactor':
                return (array) $this->data->FIRMA;

                break;
        }
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
            '10' => _('Flash'),
            '20' => _('Urgent'),
            '25' => _('General'),
            '30' => _('Normal'),
            // From Pandora
            'U'  => _('Urgent'),
            'R'  => _('Normal'),
            'B'  => _('General'),
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
     * Checks if the file loaded is an article with Europapress format
     *
     * @return boolean true if the format
     * @throws Exception If the format is not valid
     **/
    public static function checkFormat($data, $path)
    {
        if (!(string) $data->CODIGO) {
            throw new \Exception(sprintf(_('File %s is not a valid Europapress file'), $path));
        }
        return true;
    }
}
