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

class NewsMLG1EFE extends NewsMLG1
{
    /**
     * Returns the available text in this multimedia package
     *
     * @return void
     **/
    public function getTexts()
    {
        $contents = $this->getData()->xpath(
            "//NewsItem/NewsComponent"
            ."[@Duid=\"text_".$this->id.".text\"]"
        );

        $texts = null;
        if (isset($contents[0]) && $contents[0]->ContentItem) {
            $component = $contents[0]->ContentItem->DataContent;
            $nitf = new \Onm\Import\DataSource\Format\NewsMLG1Component\NITF($component);
            $texts []= $nitf;
        }

        return $texts;
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
        preg_match('@/([0-9]+).xml@', $xmlFile, $id);
        $contents = $data->xpath(
            "//NewsItem/NewsComponent"
            ."[@Duid=\"text_".$id[1].".text\"]"
        );

        if (count($contents) == 0 || $data->NewsItem->count() <= 0) {
            throw new \Exception(sprintf(_('File %s is not a valid NewsMLEFE file'), $xmlFile));
        }

        $title = (string) $data->NewsItem->NewsComponent->NewsLines->HeadLine;

        if (!(string) $data->NewsEnvelope || empty($title)) {
            throw new \Exception(sprintf(_('File %s is not a valid NewsMLG1 file'), $xmlFile));
        }

        return true;
    }
}
