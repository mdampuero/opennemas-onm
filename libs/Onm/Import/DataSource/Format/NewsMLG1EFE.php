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
        $xpathExpresion = "//NewsItem/NewsComponent/NewsComponent[@Duid=\"multimedia_".$this->id.".multimedia.texts\"]";
        $contents = $this->getData()->xpath($xpathExpresion);

        $texts = null;
        if (isset($contents[0]) && $contents[0]->ContentItem) {
            $component = $contents[0]->ContentItem->DataContent;
            $nitf = new \Onm\Import\DataSource\Format\NewsMLG1Component\NITF($component);
            $texts []= $nitf;
        }

        return $texts;
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
                                    ->FirstCreated;
        $newDate = str_replace('+0000', '', $originalDate);

        return \DateTime::createFromFormat(
            'Ymd\THis',
            $newDate,
            new \DateTimeZone('Europe/Madrid')
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
        preg_match('@/([0-9a-zA-Z]+).xml@', $xmlFile, $id);

        $isEfe = count($data->xpath(
            "//NewsEnvelope/SentFrom/Party/Property[@Value=\"Agencia EFE\"]"
        )) > 0;

        if (!$isEfe) {
            throw new \Exception(sprintf(_('File %s is not a valid NewsMLEFE file'), $xmlFile));
        }

        $title = (string) $data->NewsItem->NewsComponent->NewsLines->HeadLine;

        if (!(string) $data->NewsEnvelope || empty($title)) {
            throw new \Exception(sprintf(_('File %s is not a valid NewsMLG1 file'), $xmlFile));
        }

        return true;
    }
}
