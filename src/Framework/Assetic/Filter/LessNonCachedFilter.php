<?php

namespace Framework\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\LessFilter;

class LessNonCachedFilter extends LessFilter
{
    public function filterLoad(AssetInterface $asset)
    {
        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        $filename = realpath($root . '/' . $path);

        if (preg_match('/main\.less$/', $filename) && file_exists($filename)) {
            touch($filename);
        }

        parent::filterLoad($asset);
    }
}
