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
use Assetic\Filter\UglifyJs2Filter;

/**
 * Asset manager to handle stylesheets assets.
 */
class JavascriptManager extends AssetManager
{
    /**
     * Default extension.
     *
     * @var string
     */
    protected $extension = 'js';

    /**
     * {@inheritDoc}
     */
    protected function getFilterManager($filters)
    {
        $fm = new FilterManager();

        foreach ($filters as $name) {
            $filter = false;

            switch ($name) {
                case 'uglifyjs':
                    $filter = new UglifyJs2Filter(
                        $this->config['filters']['uglifyjs']['bin'],
                        $this->config['filters']['uglifyjs']['node']
                    );
                    break;
            }

            if ($filter !== false) {
                $options = $this->config['filters'][$name]['options'];

                foreach ($options as $key => $value) {
                    $method = 'set' . ucfirst($key);
                    $filter->{$method}($value);
                }

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

        // Only handle *.js files
        if ($ext !== 'js') {
            return [];
        }

        // Uglify only on production
        if ($this->debug()) {
            $filters = array_diff($filters, [ 'uglifyjs' ]);
        }

        return array_values(array_intersect($filters, [ 'uglifyjs' ]));
    }
}
