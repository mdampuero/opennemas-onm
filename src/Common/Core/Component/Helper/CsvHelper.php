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

        if (empty($filename)) {
            $filename = 'report.csv';
        }

        $filename = trim($filename, '.csv') . '.csv';
        $data     = $this->parse($data);

        if (!empty($data)) {
            $writer->insertOne(array_keys($data[0]));
            $writer->insertAll($data);
        }

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
        return array_map(function ($a) {
            return $a->csvSerialize();
        }, $data);
    }
}
