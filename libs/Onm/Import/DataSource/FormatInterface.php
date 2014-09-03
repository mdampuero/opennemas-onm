<?php
/**
 * Defines the Onm\Import\DataSource\Format\FormatInterface interface class
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Import_DataSource
 **/
namespace Onm\Import\DataSource;

/**
 * Defines the list of methods that a datasource format should implemente
 *
 * @package Onm_Import_DataSource
 **/
interface FormatInterface
{
    /**
     * Returns the name of the service that authored this element
     *
     * @return string the service name
     **/
    public function getServiceName();

    /**
     * Returns the id of the element
     *
     * @return string the title
     **/
    public function getId();

    /**
     * Returns the title of the element
     *
     * @return string the title
     **/
    public function getTitle();

    /**
     * Returns the pretitle of the element
     *
     * @return string the pretitle
     **/
    public function getPretitle();

    /**
     * Returns the summary of the element
     *
     * @return string the summary
     **/
    public function getSummary();

    /**
     * Returns the body of the element
     *
     * @return string the body
     **/
    public function getBody();

    /**
     * Returns the unique urn of the element
     *
     * @return string the urn
     **/
    public function getUrn();

    /**
     * Returns an integer between 1 and 5 that represents the priority level
     *
     * @return int the priority level
     **/
    public function getPriority();

    /**
     * Returns the list of tags of this element
     *
     * @return int the priority level
     **/
    public function getTags();

    /**
     * Returns the category of the element
     *
     * @return int the priority level
     **/
    public function getCategory();

    /**
     * Returns the creation datetime of this element
     *
     * @return DateTime the datetime of the element
     **/
    public function getCreatedTime();

    /**
     * Populates the object data
     *
     * @return FormatInterface the object populated
     **/
    public function load();

    /**
     * Checks if the data provided could be handled by the class
     *
     * @param SimpleXMLElement $data the XML data
     * @param string $xmlFile the file path
     *
     * @return string
     **/
    public static function checkFormat($data, $xmlFile);
}
