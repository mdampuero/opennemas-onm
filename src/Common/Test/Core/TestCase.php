<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Test\Core;

use Symfony\Component\Finder\Finder;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Returns a finder.
     *
     * @return Finder A Finder component.
     */
    protected function getFinder()
    {
        return new Finder();
    }

    /**
     * Returns the content of a fixture file.
     *
     * @param string $fixture The fixture file name.
     *
     * @return string The file content.
     */
    protected function loadFixture($fixture)
    {
        $finder = $this->getFinder();

        $class     = new \ReflectionClass($this);
        $directory = dirname($class->getFileName());
        $directory = str_replace('tests', 'fixtures', $directory);

        $finder->in($directory)->files()->name('/^' . $fixture . '$/');

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                return $file->getContents();
            }
        }

        return '';
    }
}
