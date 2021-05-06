<?php

namespace Common\Core\EventListener;

use Common\Core\Component\Core\GlobalVariables;
use \Detection\MobileDetect;

/**
 * The DeviceDetectorListener class defines an event listener to detect device type.
 * It uses the User-Agent string combined with specific HTTP headers.
 */
class DeviceDetectorListener
{
    /**
     * The mobile detector.
     *
     * @var MobileDetect
     */
    protected $detector;

    /**
     * The global variables service.
     *
     * @var GlobalVariables
     */
    protected $globals;

    /**
     * Initializes the DeviceDetectorListener.
     *
     * @param GlobalVariables $globals The global variables service.
     */
    public function __construct(GlobalVariables $globals)
    {
        $this->globals  = $globals;
        $this->detector = new MobileDetect();
    }

    /**
     * This event will fire during any controller call and
     * sets the device type on Globals Variables
     */
    public function onKernelController()
    {
        $device = 'desktop';
        if ($this->detector->isTablet()) {
            $device = 'tablet';
        } elseif ($this->detector->isMobile()) {
            $device = 'mobile';
        }

        $this->globals->setDevice($device);
    }
}
