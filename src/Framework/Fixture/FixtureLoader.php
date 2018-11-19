<?php

namespace Framework\Fixture;

use Symfony\Component\Yaml\Yaml;

class FixtureLoader
{
    /**
     * Initializes the FixtureLoader.
     *
     * @param TestCase $testCase The test case.
     */
    public function __construct($testCase)
    {
        $this->path     = substr(__DIR__, 0, strpos(__DIR__, '/src') + 5);
        $this->testCase = $testCase;
    }

    /**
     * Loads a fixture from file.
     *
     * @param string $path The path to fixture file.
     *
     * @return mixed The fixture loaded from file.
     */
    public function loadObject($path)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException();
        }

        $data = Yaml::parse($path);

        if (!array_key_exists('target', $data)) {
            throw new \InvalidArgumentException();
        }

        $builder = new PHPUnit_Framework_MockObject_MockBuilder($this->testCase, $data['target']);

        if (array_key_exists('constructor', $data)
            && $data['constructor'] === 'disabled'
        ) {
            $builder->disableOriginalConstructor();
        }
    }

    /**
     * Loads data from file.
     *
     * @param string $path The path to fixture file.
     *
     * @return array The data loaded from file.
     */
    public function loadData($path)
    {
        $path = $this->path . $path;

        if (!file_exists($path)) {
            throw new \InvalidArgumentException();
        }

        return Yaml::parse($path);
    }
}
