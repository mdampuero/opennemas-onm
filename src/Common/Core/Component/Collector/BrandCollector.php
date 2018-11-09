<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Collector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * The BrandCollector class displays brand-related information in profiler.
 */
class BrandCollector extends DataCollector
{
    /**
     * The path to the json file to read
     *
     * @var string
     */
    public $path;

    /**
     * Initiliazates the BrandCollector.
     *
     * @param string $root The path to kernel root directory.
     */
    public function __construct($root)
    {
        $this->path = $root . '/../package.json';
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $metadata = $this->getMetadata();

        $this->data = [
            'name'        => $metadata['name'],
            'homepage'    => $metadata['homepage'],
            'version'     => $metadata['version'],
            'description' => $metadata['description'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'core.collector.brand';
    }

    /**
     * Returns brand-related data.
     *
     * @return string The brand-related data.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the information in package.json
     *
     * @return array The information in package.json
     *
     * @codeCoverageIgnore
     */
    protected function getMetadata()
    {
        return json_decode(file_get_contents($this->path), true);
    }

    public function reset()
    {
        $this->data = [];
    }
}
