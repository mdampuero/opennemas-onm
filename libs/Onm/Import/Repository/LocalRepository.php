<?php
/**
 * Defines the Onm\Import\Repository class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Import
 **/
namespace Onm\Import\Repository;

use Onm\Import\DataSource\DataSourceFactory;
use Onm\Import\DataSource\Format\Opennemas\Binary;
use Onm\Import\Synchronizer\Synchronizer;
use Onm\Import\Synchronizer\Exception as SynchronizerException;
use Onm\Import\Compiler\Compiler;

/**
 * Class to import news from any news Agency FTP / HTTP
 *
 * @package    Onm_Import
 */
class LocalRepository
{
    public $syncPath = '';

    /**
     * Initializes the object and initializes configuration
     *
     * @return void
     */
    public function __construct($config = array())
    {
        $this->config = $config;

        $this->syncPath = implode(
            DIRECTORY_SEPARATOR,
            array(CACHE_PATH, 'importers')
        );
    }

    /**
     * Gets an array of news from Efe
     *
     * @return array, the array of objects with news from Efe
     */
    public function findAll($params = array())
    {
        $filesSynced = Synchronizer::getLocalFileList($this->syncPath);

        $elements = array();
        foreach ($filesSynced as $file) {
            $fileParts = explode('/', $file);
            $sourceId = (int) $fileParts[0];

            if ($params['source'] != '*'
                && $sourceId != $params['source']
            ) {
                continue;
            }

            $filePath = $this->syncPath.DIRECTORY_SEPARATOR.$file;
            if (@filesize($filePath) <= 0) {
                continue;
            }
            try {
                $element = DataSourceFactory::get($filePath);
            } catch (\Exception $e) {
                continue;
            }

            if (is_null($element)) {
                continue;
            }

            if ($params['title'] != '*'
                && !($element->hasContent($params['title']))
            ) {
                continue;
            }

            $element->source_id = $sourceId;

            $elements []= $element;
        }

        $counTotalElements = count($elements);
        if (array_key_exists('items_page', $params)
            && array_key_exists('page', $params)
        ) {
            $files = array_slice(
                $elements,
                $params['items_page'] * ($params['page']-1),
                $params['items_page']
            );
        } else {
            $files = $elements;
        }

        return array($counTotalElements, $files);
    }

    /**
     * Gets an array of news from Efe
     *
     * @return array, the array of objects with news from Efe
     */
    public function findAllFromCompile($params = array())
    {
        $compiler = new Compiler($this->syncPath);
        $sourceElements = $compiler->getElementsFromCompileFile();

        $elements = [];
        foreach ($sourceElements as $element) {
            $sourceId = (int) $element['source_id'];
            if ($params['source'] != '*'
                && $sourceId != $params['source']
            ) {
                continue;
            }

            if (!is_array($element)) {
                continue;
            }

            if ($params['title'] != '*'
                && !($this->matchContent($element, $params['title']))
            ) {
                continue;
            }

            $element = new Binary($element);
            $elements []= $element;
        }

        $counTotalElements = count($elements);
        if (array_key_exists('items_page', $params)
            && array_key_exists('page', $params)
        ) {
            $files = array_slice(
                $elements,
                $params['items_page'] * ($params['page']-1),
                $params['items_page']
            );
        } else {
            $files = $elements;
        }

        return array($counTotalElements, $files);
    }

    /**
     * Fetches a Onm\Import\DataSource\FormatInterface compatible object from id
     *
     * @param string $sourceId the source id to search in
     * @param string $xmlFile  the element id
     *
     * @return \Onm\Import\DataSource\FormatInterface the article object
     */
    public function findByID($sourceId, $id)
    {
        $compiler = new Compiler($this->syncPath);
        $sourceElements = $compiler->getElementsFromCompileFile();

        $element = null;
        foreach ($sourceElements as $sourceElement) {
            if ($sourceElement['source_id'] == $sourceId && $sourceElement['id'] == $id) {
                $element = $sourceElement;
                break;
            }
        }

        if (is_null($element)) {
            throw new \Exception();
        } else {
            $element = new Binary($element);
        }

        return  $element;
    }

    /**
     * Fetches a  Onm\Import\DataSource\FormatInterface object from file name
     *
     * @param string $sourceId the source id to search in
     * @param string $xmlFile  the element file name
     *
     * @return  Onm\Import\DataSource\FormatInterface the article object
     */
    public function findByFileName($sourceId, $xmlFile)
    {
        $compiler = new Compiler($this->syncPath);
        $sourceElements = $compiler->getElementsFromCompileFile();

        $element = null;
        foreach ($sourceElements as $sourceElement) {
            if ($sourceElement['source_id'] == $sourceId && $sourceElement['xml_file'] == $xmlFile) {
                $element = $sourceElement;
                break;
            }
        }

        if (is_null($element)) {
            throw new \Exception();
        } else {
            $element = new Binary($element);
        }

        return  $element;
    }

    /**
     * Matches the element contents against a filter
     *
     * @param array $element the source element information
     * @param array $filter the filter to use
     * @return boolean true if the element matches the filter
     **/
    public function matchContent($element, $filter)
    {
        $needle   = strtolower(\Onm\StringUtils::normalize($filter));
        $title    = strtolower(\Onm\StringUtils::normalize($element['title']));

        if (preg_match("@".$needle."@i", $title)) {
            return true;
        }

        $pretitle = strtolower(\Onm\StringUtils::normalize($element['pretitle']));
        if (preg_match("@".$needle."@i", $pretitle)) {
            return true;
        }

        $body = strtolower(\Onm\StringUtils::normalize($element['body']));
        if (preg_match("@".$needle."@i", $body)) {
            return true;
        }

        return false;
    }
}
