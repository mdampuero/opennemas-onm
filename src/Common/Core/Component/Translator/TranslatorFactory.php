<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Translator;

use Symfony\Component\Finder\Finder;

/**
 * The `TranslatorFactory` class provides methods to create and configure
 * translators basing on name and parameters.
 */
class TranslatorFactory
{
    /**
     * Returns a translator by name.
     *
     * @param string $name   The translator name.
     * @param string $from   The language to translate from.
     * @param string $to     The language to translate to.
     * @param array  $params The list of parameters.
     *
     * @return Translator The translator.
     *
     * @throws InvalidArgumentException If no translator found.
     */
    public function get($name, $from = '', $to = '', $params = [])
    {
        $translators = $this->getTranslators();

        if (in_array($name, array_keys($translators))) {
            $class = '\\' . __NAMESPACE__ . '\\' . $translators[$name];
            return new $class($from, $to, $params);
        }

        throw new \InvalidArgumentException();
    }

    /**
     * Returns a list of available translators.
     *
     * @return array A list of available translators.
     */
    public function getAvailableTranslators()
    {
        return array_keys($this->getTranslators());
    }

    /**
     * Returns a list with all translator data.
     *
     * @return array A list of translator data
     */
    public function getTranslatorsData()
    {
        return array_map(function ($a) {
            return [
                'translator' => $a,
                'parameters' => $this->get($a)->getRequiredParameters()
            ];
        }, $this->getAvailableTranslators());
    }

    /**
     * Returns a new Finder in the configured paths.
     *
     * @return Finder A Finder component.
     */
    protected function getFinder()
    {
        $finder = new Finder();

        $finder->in(__DIR__);

        return $finder;
    }

    /**
     * Returns a list of available translators and their classnames.
     *
     * @return array A list of available translators and their classnames.
     */
    protected function getTranslators()
    {
        $finder      = $this->getFinder();
        $translators = [];

        foreach ($finder->name('/.+Translator.php/')->files() as $file) {
            $name = str_replace('Translator.php', '', $file->getFilename());
            $name = strtolower($name);

            $translators[$name] = str_replace(
                [  '.php', '/' ],
                [ '', '\\' ],
                $file->getRelativePathName()
            );
        }

        return $translators;
    }
}
