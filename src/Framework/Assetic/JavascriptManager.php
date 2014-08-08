<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\Assetic;

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
    public function initFilters($filters)
    {
        $this->fm = new FilterManager();

        foreach ($filters as $filter) {
            switch ($filter) {
                case 'uglifyjs':
                    $this->fm->set(
                        'uglifyjs',
                        new UglifyJsFilter(
                            $filters['uglifyjs']['bin'],
                            $filters['uglifyjs']['node']
                        )
                    );
                    break;
                case 'uglifyjs2':
                    $this->fm->set(
                        'uglifyjs2',
                        new UglifyJs2Filter(
                            $filters['uglifyjs2']['bin'],
                            $filters['uglifyjs2']['node']
                        )
                    );
                    break;
                default:
                    throw new FilterException();
            }
        }
    }
}
