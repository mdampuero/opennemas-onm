<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource\Parser;

class NewsMLComponentList extends NewsML
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }

        $node = $data->xpath('/NewsComponent/NewsComponent');

        if (!is_array($node) || count($node) == 0) {
            return false;
        }

        if ($this->checkPhoto($data)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $list = $data->xpath('/NewsComponent/NewsComponent');

        $contents = [];
        foreach ($list as $item) {
            $item = simplexml_load_string($item->asXML());
            $contents[] = $this->parseComponent($item);
        }

        return $contents;
    }
}
