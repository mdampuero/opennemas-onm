<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Diéguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 namespace Onm\Import\DataSource;
 /**
  * Handles all the operations for NITF data
  *
  * @package Onm
  * @author
  **/
 class NITF
 {
    /**
     * Instantiates the NITF DOM data from an SimpleXML object
     *
     * @return void
     * @author
     **/
    public function __construct($data)
    {
        $this->data = $data;
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
                $attributes = $this->getData()->attributes();

                return (string)$attributes->Euid;
                break;

            case 'title':
                $titles = $this->getData()->xpath("//NewsLines/HeadLine");

                return (string)$titles[0];
                break;

            case 'pretitle':
                $pretitles = $this->getData()->xpath("//NewsLines/SubHeadLine");

                return (string)$pretitles[0];
                break;

            case 'summary':
                $summaries = $this->getData()->xpath("//nitf/body/body.head/abstract");
                $summary = "";
                foreach($summaries[0]->children() as $child) {
                  $summary .= "<p>".sprintf("%s", $child)."</p>";
                }

                return $summary;
                break;

            case 'body':
                $summaries = $this->getData()->xpath("//nitf/body/body.content");
                $summary = "";
                foreach($summaries[0]->children() as $child) {
                  $summary .= "<p>".sprintf("%s", $child)."</p>\n";
                }

                return $summary;
                break;

            case 'created_time':
                $originalDate = (string)$this->getData()->DescriptiveMetadata->DateLineDate;
                var_dump($originalDate);die();


                return \DateTime::createFromFormat(
                    \DateTime::ISO8601,
                    $originalDate
                );
                break;

            case 'body':
                if (count($this->texts) > 0) {
                    return $this->texts[0];
                }

                return;
                break;

        }
    }


    /*
     * Returns the internal data, use with caution
     */
    public function getData()
    {
        return $this->data;
    }

} // END class NITF
