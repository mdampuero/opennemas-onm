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

use Frontend\Renderer\StatisticsRenderer;

class OjdRenderer extends StatisticsRenderer
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
     * The template
     *
     * @var Template
     */
    protected $tpl;

    /**
     * Initializes the GAnalyticsRenderer
     *
     * @param RequestStack    $stack The request stack.
     * @param GlobalVariables $global The global variables
     * @param Template        $tpl The template
     */
    public function __construct($stack, $global, $tpl)
    {
        $this->stack  = $stack;
        $this->global = $global;
        $this->tpl    = $tpl;
    }

    /**
     * Get code of google analytics for amp pages
     */
    public function getAmp()
    {
        return $this->tpl->fetch('statistics/helpers/Ojd/amp.tpl', []);
    }

    /**
     * Get script code for google analytics
     */
    public function getScript()
    {
        return $this->tpl->fetch('statistics/helpers/Ojd/script.tpl', []);
    }

    /**
     * Get image code for google analytics
     */
    public function getImage()
    {
        return $this->tpl->fetch('statistics/helpers/Ojd/image.tpl', []);
    }

    /**
     * Return if ojd is correctly configured or not
     */
    public function validate()
    {
        return true;
    }
}
