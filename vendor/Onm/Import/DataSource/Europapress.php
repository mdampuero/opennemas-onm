<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Import\DataSource;

use Onm\Settings as s;

/**
 * Class that handles an Europapress news provider xml file
 *
 * @package Onm_Import_DataSource
 **/
class Europapress
{
    private $_data = null;

    private static $_priorityMap = array(
        '10'  => 4,
        '20' => 3,
        '25' => 2,
        '30' => 1,
        // From Pandora
        'U' => 4,
        'R' => 3,
        'B' => 2,
    );

    /*
     * __construct()
     * @param $xmlFile, the XML file that contains information about an EP new
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

            $this->_data = simplexml_load_file(
                $xmlFile,
                null,
                LIBXML_NOERROR | LIBXML_NOWARNING
            );

            if (!$this->_data) {
                throw new \Exception(
                    sprintf(_("File '%d' can't be loaded."), $xmlFile)
                );
            }

            $this->checkFileFormat();
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
                return (int) $this->_data->CODIGO;

                break;
            case 'agencyID':
                return (string) $this->_data->AGENCIA;

                break;
            case 'priority':
                return self::matchPriority((string) $this->_data->PRIORIDAD);

                break;
            case 'priorityNumber':
                return self::$_priorityMap[(string) $this->_data->PRIORIDAD];

                break;
            case 'service':
                return (string) $this->_data->SERVICIO;

                break;
            case 'category':
                return self::matchCategoryName((string) $this->_data->SECCION);

                break;
            case 'originalCategory':
                return (string) $this->_data->SECCION;

                break;
            case 'informationType':
                return (string) $this->_data->TIPOINFO;

                break;
            case 'key':
                return (string) $this->_data->CLAVE;

                break;
            case 'created_time':

                $dateFormat = 'd/m/Y H:i:s';
                $originalDate = $this->_data->FECHA.' '.$this->_data->HORA;
                $date = \DateTime::createFromFormat(
                    $dateFormat,
                    $originalDate,
                    new \DateTimeZone('Europe/Madrid')
                );

                return $date;

                break;
            case 'pretitle':
                return (string) $this->_data->ANTETITULO;

                break;
            case 'title':
                return (string) $this->_data->TITULAR;

                break;
            case 'body':
                return nl2br((string) $this->_data->CONTENIDO);

                break;
            case 'summary':
                return (string) $this->_data->ENTRADILLA;

                break;
            case 'photos':
                return (array) $this->_data->PHOTOS;

                break;
            case 'personajes':
                return (array) $this->_data->PERSONAJES;

                break;
            case 'photos':
                return (array) $this->_data->PHOTOS;

                break;
            case 'people':
                return (array) $this->_data->PERSONAJES;

                break;
            case 'place':
                return (array) $this->_data->LUGAR;

                break;
            case 'associatedDocs':
                return (array) $this->_data->DOCS;

                break;
            case 'categories':
                return (array) $this->_data->CATEGORIES;

                break;
            case 'dataCastID':
                return (array) $this->_data->DATACASTID;

                break;
            case 'level':
                return (array) $this->_data->LEVEL;

                break;
            case 'redactor':
                return (array) $this->_data->FIRMA;

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
        return $this->_data;
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
    public function checkFileFormat()
    {
        if (!(string) $this->_data->CODIGO) {
            throw new \Exception(sprintf(_('File %s is not a valid Europapress file'), $this->xmlFile));
        }
        return true;
    }
}
