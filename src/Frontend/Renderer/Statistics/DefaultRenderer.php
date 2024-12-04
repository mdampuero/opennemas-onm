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
     * {@inheritdoc}
     */
    protected function getParameters($content = null)
    {
        return [
            'content' => $content,
            'params'  => [ 'common' => 1, 'src' => '/onm/jquery.onm-stats.js' ],
            'id'      => (int) $this->frontend->getValue('item')->pk_content
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        $request = $this->global->getRequest();

        if (empty($request)
            || preg_match('@^/admin@', $request->getUri())
            || preg_match('@/preview$@', $request->getUri())
            || preg_match('@^/ext@', $request->getUri())
            || !$this->frontend->getValue('item')->pk_content
        ) {
            return false;
        }

        return true;
    }
}
