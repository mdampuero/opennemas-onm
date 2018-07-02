<?php

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/libs')
    ->exclude('adodb5');

//$versions = GitVersionCollection::create($dir)
    //->add('develop', 'develop')
    //->add('master', 'master');

$options = [
    'theme'     => 'default',
    'build_dir' => __DIR__ . '/build/docs/php/core/',
    'cache_dir' => __DIR__ . '/tmp/cache/docs/',
    'title'     => 'Opennemas API Documentation',
    //'build_dir' => __DIR__ . '/build/docs/core/%version%',
    //'versions'  => $versions,
    // 'cache_dir' => __DIR__ . '/tmp/cache/doc/core/%version%'
];

return new Sami($iterator, $options);
