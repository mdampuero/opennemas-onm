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

class DefaultRenderer extends StatisticsRenderer
{
    /**
     * Returns if default is correctly configured or not.
     *
     * @return boolean True if the default code needs to be displayed, False otherwise.
     */
    public function validate()
    {
        $request = $this->global->getRequest();

        if (empty($request)
            || preg_match('@^/admin@', $request->getUri())
            || preg_match('@/preview$@', $request->getUri())
            || preg_match('@^/ext@', $request->getUri())
            || !$this->smarty->hasValue('item')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Return parameters needed to generate default code.
     *
     * @return array The array of parameters for default code.
     */
    public function prepareParams()
    {
        return [
            'params' => [ 'common' => 1, 'src' => '/onm/jquery.onm-stats.js' ],
            'item' => $this->smarty->getValue('item')
        ];
    }
}
