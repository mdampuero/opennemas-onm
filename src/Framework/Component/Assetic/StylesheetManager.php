<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Assetic;

use Assetic\FilterManager;
use Assetic\Filter\CssRewriteFilter;
use Assetic\Filter\LessFilter;
use Assetic\Filter\UglifyCssFilter;

/**
 * Asset manager to handle stylesheets assets.
 */
class StylesheetManager extends AssetManager
{
    /**
     * Default extension for stylesheets.
     *
     * @var string
     */
    protected $extension = 'css';

    /**
     * {@inheritDoc}
     */
    protected function getFilterManager($filters)
    {
        $fm = new FilterManager();

        foreach ($filters as $name) {
            $filter = false;

            switch ($name) {
                case 'cssrewrite':
                    $filter = new CssRewriteFilter();
                    break;

                case 'uglifycss':
                    $filter = new UglifyCssFilter(
                        $this->config['filters']['uglifycss']['bin'],
                        $this->config['filters']['uglifycss']['node']
                    );
                    break;

                case 'less':
                    $filter = new LessFilter(
                        $this->config['filters']['less']['node'],
                        $this->config['filters']['less']['node_paths']
                    );

                    if ($this->debug()) {
                        $options = $this->config['filters'][$name]['options'];

                        foreach ($options as $key => $value) {
                            $filter->addTreeOption($key, $value);
                        }
                    }
                    break;
            }

            if ($filter !== false) {
                $fm->set($name, $filter);
            }
        }

        return $fm;
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilters($asset, $filters)
    {
        $ext = pathinfo($asset, PATHINFO_EXTENSION);

        // Only handle *.css and *.less files
        if ($ext !== 'css' && $ext !== 'less') {
            return [];
        }

        // Less only for *.less files
        if ($ext !== 'less') {
            $filters = array_diff($filters, [ 'less' ]);
        }

        // Uglify only on production
        if ($this->debug()) {
            $filters = array_diff($filters, [ 'uglifycss' ]);
        }

        return array_values(
            array_intersect($filters, [ 'cssrewrite', 'less', 'uglifycss' ])
        );
    }
}
