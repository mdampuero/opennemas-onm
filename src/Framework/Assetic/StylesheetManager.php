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
use Assetic\Filter\CssRewriteFilter;
use Assetic\Filter\LessFilter;
use Framework\Assetic\Filter\LessNonCachedFilter;
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
    public function addAsset($asset)
    {
        if (preg_match('/.*\.(css|scss|less)/', $asset)) {
            $this->assets[] = realpath($asset);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function initFilters()
    {
        $this->fm = new FilterManager();

        foreach ($this->filters as $filter) {
            switch ($filter) {
                case 'cssrewrite':
                    $this->fm->set('cssrewrite', new CssRewriteFilter());
                    break;

                case 'uglifycss':
                    $this->fm->set(
                        'uglifycss',
                        new UglifyCssFilter(
                            $this->config['filters']['uglifycss']['bin'],
                            $this->config['filters']['uglifycss']['node']
                        )
                    );
                    break;
                case 'less':
                    $filter = new LessFilter(
                        $this->config['filters']['less']['node'],
                        $this->config['filters']['less']['node_paths']
                    );

                    $this->fm->set('less', $filter);
                    break;
                default:
                    throw new FilterException();
            }
        }
    }
}
