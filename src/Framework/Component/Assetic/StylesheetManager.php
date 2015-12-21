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

        foreach ($filters as $filter) {
            switch ($filter) {
                case 'cssrewrite':
                    $fm->set('cssrewrite', new CssRewriteFilter());
                    break;

                case 'uglifycss':
                    $fm->set(
                        'uglifycss',
                        new UglifyCssFilter(
                            $this->config['filters']['uglifycss']['bin'],
                            $this->config['filters']['uglifycss']['node']
                        )
                    );
                    break;
                case 'less':
                    $fm->set(
                        'less',
                        new LessFilter(
                            $this->config['filters']['less']['node'],
                            $this->config['filters']['less']['node_paths']
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
