<?php
namespace Framework\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * OpennemasDataCollector.
 */
class OpennemasBrandCollector extends DataCollector
{
    protected $app;

    public function __construct()
    {
    }

    public function getName()
    {
        return 'brand_collector';
    }

    /**
     * Collect the date for the Toolbar item.
     *
     * @param Request    $request
     * @param Response   $response
     * @param \Exception $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [
            'version'       => \Onm\Common\Version::VERSION,
            'payoff'        => 'The best CMS for online jornalism',
            'branding'      => null,
        ];
    }

    /**
     * Getter for version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->data['version'];
    }

    /**
     * Getter for branding.
     *
     * @return string
     */
    public function getBranding()
    {
        return $this->data['branding'];
    }
}
