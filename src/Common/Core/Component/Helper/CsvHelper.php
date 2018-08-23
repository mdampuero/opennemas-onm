<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Common\Data\Serialize\CsvSerializable;
use League\Csv\Writer;

class CsvHelper
{
    /**
     * Returns a response with a CSV report from a list of data and headers.
     *
     * @param array  $data The list of contents.
     *
     * @return mixed The CSV file content.
     */
    public function getReport($data)
    {
        $writer = $this->getWriter();

        list($headers, $data) = $this->parse($data);

        $writer->insertOne($headers);
        $writer->insertAll($data);

        return $writer->__toString();
    }

    /**
     * Returns the CSV writer.
     *
     * @return Writer The CSV writer.
     */
    protected function getWriter()
    {
        $writer = Writer::createFromFileObject(new \SplTempFileObject());

        $writer->setDelimiter(';');

        return $writer;
    }

    /**
     * Parses a list of contents and returns a valid list of contents ready to
     * include in a CSV file.
     *
     * @param array $data The list of contents to parse.
     *
     * @return array The parsed information.
     */
    protected function parse($data)
    {
        if (!is_array($data)) {
            return [ [], $data ];
        }

        $data = array_map(function ($a) {
            if ($a instanceof CsvSerializable) {
                return $a->csvSerialize();
            }

            return $a;
        }, $data);

        $headers = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $headers = array_merge($headers, array_keys($item));
            }
        }

        return [ array_values(array_unique($headers)), $data ];
    }
}
