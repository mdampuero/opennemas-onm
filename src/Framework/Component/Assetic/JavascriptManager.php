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
                    $fm->set(
                        'uglifyjs',
                        new UglifyJsFilter(
                            $this->config['filters']['uglifyjs']['bin'],
                            $this->config['filters']['uglifyjs']['node']
                        )
                    );
                    break;
                case 'uglifyjs2':
                    $fm->set(
                        'uglifyjs2',
                        new UglifyJs2Filter(
                            $this->config['filters']['uglifyjs2']['bin'],
                            $this->config['filters']['uglifyjs2']['node']
                        )
                    );
                    break;
                default:
                    throw new FilterException();
            }
        }

        return $fm;
    }
}
