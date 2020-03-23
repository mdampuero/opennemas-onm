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

class ComscoreRenderer extends StatisticsRenderer
{
    /**
     * Get code of google analytics for amp pages
     */
    public function getAmp()
    {
        return $this->tpl->fetch('statistics/helpers/Comscore/amp.tpl', []);
    }

    /**
     * Get script code for google analytics
     */
    public function getScript()
    {
        return $this->tpl->fetch('statistics/helpers/Comscore/script.tpl', []);
    }

    /**
     * Get image code for google analytics
     */
    public function getImage()
    {
        return $this->tpl->fetch('statistics/helpers/Comscore/image.tpl', []);
    }

    /**
     * Return if comscore is correctly configured or not
     */
    public function validate()
    {
        return true;
    }
}
