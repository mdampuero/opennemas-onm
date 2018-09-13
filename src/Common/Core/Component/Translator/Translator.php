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

/**
 * The `Translator` class defines common properties and methods for all
 * translators.
 */
abstract class Translator
{
    /**
     * The language code to translate from.
     *
     * @var string
     */
    protected $from;

    /**
     * The list of parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * The langage code to translate to.
     *
     * @var string
     */
    protected $to;

    /**
    * Returns a list of parameter names required by the translator.
     *
     * @return array The list of parameter names.
     */
    abstract public function getRequiredParameters();

    /**
     * Translates a string.
     *
     * @param string $str  The string to translate.
     * @param string $from The language to translate from. This parameter
     *                     overrides the translator configuration.
     * @param string $to   The language to translate to. This parameter
     *                     overrides the translator configuration.
     *
     * @return string The translated string.
     */
    abstract public function translate($str, $from = null, $to = null);

    /**
     * Initializes the Translator.
     *
     * @param string $from   The language to translate from.
     * @param string $to     The language to translate to.
     * @param array  $params The list of parameters.
     *
     * @return type Description
     */
    public function __construct($from = null, $to = null, $params = [])
    {
        $this->from   = $from;
        $this->params = $params;
        $this->to     = $to;
    }

    /**
     * Gets the value of the property by name.
     *
     * @return mixed The property value.
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        return null;
    }

    /**
     * Checks if a property exists in class or in the array of parameters.
     *
     * @param string $name The property name.
     *
     * @return boolean True if the property exists in class or in the array of
     *                 parameters. False, otherwise.
     */
    public function __isset($name)
    {
        if (property_exists($this, $name)
            || array_key_exists($name, $this->params)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Sets the value of the property given a name and a value.
     *
     * @param string The property name.
     * @param mixed  The property value.
     *
     * @return void
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            return $this->{$name} = $value;
        }

        $this->params[$name] = $value;
    }
}
