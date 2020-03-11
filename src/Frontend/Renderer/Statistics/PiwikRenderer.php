<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Renderer\Statistics;

class PiwikRenderer
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    protected $stack;


    /**
     * The global variables
     *
     * @var GlobalVariables
     */
    protected $global;

    /**
     * Initializes the GAnalyticsRenderer
     *
     * @param RequestStack $stack The request stack.
     * @param GlobalVariables $global The global variables
     */
    public function __construct($stack, $global)
    {
        $this->stack  = $stack;
        $this->global = $global;
    }

    /**
     * Get code of google analytics for amp pages
     */
    public function getAmp()
    {
        return '';
    }

    /**
     * Get script code for google analytics
     */
    public function getScript()
    {
        return '';
    }

    /**
     * Get image code for google analytics
     */
    public function getImage()
    {
        return '';
    }

    /**
     * Return if piwik is correctly configured or not
     */
    public function validate()
    {
        return true;
    }
}
