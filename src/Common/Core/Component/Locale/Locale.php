<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Locale;

/**
 * The Locale class manages the system locale and timezone.
 */
class Locale
{
    /**
     * The default locale configuration.
     *
     * @var array
     */
    public $config = [
        'backend' => [
            'language' => [ 'selected'  => 'en_US' ],
            'timezone' => 'UTC'
        ],
        'frontend' => [
            'language' => [ 'selected'  => 'en_US' ],
            'timezone' => 'UTC'
        ],
    ];

    /**
     * The current context.
     *
     * @var string
     */
    protected $context = 'backend';

    /**
     * The default values for contexts.
     *
     * @var array
     */
    protected $default = [
        'language' => [
            'available' => [],
            'selected'  => 'en',
        ],
        'timezone' => 'UTC'
    ];

    /**
     * Locale auto-correction values.
     *
     * @var array
     */
    protected $fixes = [ 'en' => 'en_US', 'gl' => 'gl_ES' ];

    /**
     * The path to locales.
     *
     * @var string
     */
    protected $path;

    /**
     * Initializes the Locale.
     *
     * @param array  $available The available locales for backend.
     * @param string $path      The path to locales for backend.
     */
    public function __construct($available, $path)
    {
        $this->config['backend']['language']['available'] = $available;

        $this->path = $path;

        // Update contexts adding missing default values
        foreach ($this->config as $context => $config) {
            $this->config[$context] = array_replace_recursive(
                $this->default,
                $this->config[$context]
            );
        }
    }

    /**
     * Adds a new text domain.
     *
     * @param string $domain Text domain name.
     * @param string $path   Path to text domain.
     *
     * @codeCoverageIgnore
     */
    public function addTextDomain($domain, $path)
    {
        bindtextdomain($domain, $path);
    }

    /**
     * Applies the current configuration.
     */
    public function apply()
    {
        $this->changeLocale();
        $this->changeTimeZone();
    }

    /**
     * Configures the Locale service.
     *
     * @param array $config The locale configuration.
     */
    public function configure($configs)
    {
        if (empty($configs) || !is_array($configs)) {
            return $this;
        }

        foreach ($configs as $context => $config) {
            // Convert a timezone id to a timezone name
            if (array_key_exists('timezone', $config)) {
                $config['timezone'] =
                    $this->getTimeZoneName($config['timezone']);
            }

            $this->config[$context] = array_replace_recursive(
                $this->config[$context],
                $config
            );
        }

        return $this;
    }

    /**
     * Returns the list of available locales for context.
     *
     * @return array The list of available locales for context.
     */
    public function getAvailableLocales()
    {
        $locales = [];

        foreach ($this->config[$this->context]['language']['available'] as $locale) {
            $locales[$locale] = ucfirst(\Locale::getDisplayName($locale));
        }

        return $locales;
    }

    /**
     * Return the current context.
     *
     * @return string The current context.
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Returns the current locale for context.
     *
     * @return string The current locale for context.
     */
    public function getLocale()
    {
        return $this->config[$this->context]['language']['selected'];
    }

    /**
     * Returns the current locale name.
     *
     * @return string The current locale name.
     */
    public function getLocaleName()
    {
        return ucfirst(\Locale::getDisplayName($this->getLocale()));
    }

    /**
     * Returns the current locale without region.
     *
     * @return string The current locale without region.
     */
    public function getLocaleShort()
    {
        return explode('_', $this->getLocale())[0];
    }

    /**
     * Returns the list of all available locales.
     *
     * @return array The list of all available locales.
     */
    public function getSupportedLocales()
    {
        $codes   = $this->config['backend']['language']['available'];
        $locales = [];

        if ($this->context === 'frontend') {
            $codes = \ResourceBundle::getLocales('');
        }

        foreach ($codes as $code) {
            $locales[$code] = ucfirst(\Locale::getDisplayName($code));
        }

        return $locales;
    }

    /**
     * Get the timezone.
     *
     * @return string The current timezone.
     */
    public function getTimeZone()
    {
        return new \DateTimeZone($this->config[$this->context]['timezone']);
    }

    /**
     * Changes the context.
     *
     * @param string $context The context name.
     *
     * @return Locale The current locale service.
     */
    public function setContext($context)
    {
        // Remove when more contexts supported
        $this->context = $context === 'frontend' ? $context : 'backend';

        if (!array_key_exists($this->context, $this->config)) {
            $this->config[$this->context] = $this->default;
        }

        return $this;
    }

    /**
     * Changes the system locale.
     *
     * @param string $locale The locale.
     *
     * @return Locale The current locale service.
     */
    public function setLocale($locale = null)
    {
        // Try to auto-correct the locale
        if (!empty($locale)) {
            if (array_key_exists($locale, $this->fixes)) {
                $locale = $this->fixes[$locale];
            }

            if (in_array($locale, $this->config[$this->context]['language']['available'])) {
                $this->config[$this->context]['language']['selected'] = $locale;
            }
        }

        return $this;
    }

    /**
     * Changes the timezone.
     *
     * @param integer $timezone The timezone id.
     *
     * @return Locale The current locale service.
     */
    public function setTimeZone($timezone)
    {
        $timezone  = is_numeric($timezone) ? (int) $timezone : $timezone;
        $timezones = \DateTimeZone::listIdentifiers();

        // Convert timezone id to timezone name
        if (is_numeric($timezone) && array_key_exists($timezone, $timezones)) {
            $timezone = $timezones[(int) $timezone];
        }

        // Change timezone if name valid
        if (in_array($timezone, $timezones)) {
            $this->config[$this->context]['timezone'] = $timezone;
        }

        return $this;
    }

    /**
     * Changes the PHP locale basing on configuration.
     *
     * @codeCoverageIgnore
     */
    protected function changeLocale()
    {
        \Locale::setDefault($this->getLocale());

        // Set locale for gettext
        setlocale(LC_ALL, $this->getLocale() . '.UTF-8');
        setlocale(LC_NUMERIC, 'C');

        $domain = 'messages';

        $this->addTextDomain($domain, $this->path);
        textdomain($domain);

    }

    /**
     * Changes the PHP timezone basing on configuration.
     *
     * @codeCoverageIgnore
     */
    protected function changeTimeZone()
    {
        date_default_timezone_set($this->getTimeZone()->getName());
    }

    /**
     * Converts a timezone id to a timezone name.
     *
     * @param mixed $timezone The timezone value.
     *
     * @return string The timezone name.
     */
    protected function getTimeZoneName($timezone)
    {
        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = is_numeric($timezone) ? (int) $timezone : $timezone;

        if (!is_numeric($timezone) && in_array($timezone, $timezones)) {
            return $timezone;
        }

        if (is_numeric($timezone) && array_key_exists($timezone, $timezones)) {
            return $timezones[(int) $timezone];
        }

        return null;
    }
}
