<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent;

/**
 * Parses NewsComponent of text type from NewsML custom format for EFE.
 */
class NewsMLComponentTextEfe extends NewsMLComponentText
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!parent::checkFormat($data)) {
            return false;
        }

        if (empty($data->xpath('/NewsComponent/ContentItem/Format[@FormalName="NITF"]'))) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        // Get only nitf
        $data = $data->xpath('//nitf');
        $data = $data[0];

        // Discard extra elements
        $data = simplexml_load_string(
            $data->asXML(),
            null,
            LIBXML_NOERROR | LIBXML_NOWARNING
        );

        return $this->factory->get($data, $this)->parse($data);
    }
}
