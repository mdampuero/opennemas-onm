<?php
/**
 * This file is part of the Onm package.
 *
 * (c) OpenHost S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Assetic;

use Assetic\FilterManager;
use Assetic\Exception\FilterException;
use Assetic\Filter\UglifyJsFilter;
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

        foreach ($filters as $filter) {
            switch ($filter) {
                case 'uglifyjs':
                    $config = $this->config['filters'][$filter];
                    $filter = new UglifyJs2Filter($config['bin'], $config['node']);

                    if (array_key_exists('options', $config)
                        && !empty($config['options'])
                    ) {
                        foreach ($config['options'] as $key => $value) {
                            $method = 'set' . ucfirst($key);
                            $filter->{$method}($value);
                        }
                    }

                    $fm->set('uglifyjs', $filter);

                    break;
                default:
                    throw new FilterException();
            }
        }

        return $fm;
    }
}
