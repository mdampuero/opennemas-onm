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
     * Locale configuration.
     *
     * @var array
     */
    protected $config;

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
    protected $fixes = [ 'en' => 'en_US', 'gl' => 'gl_ES', 'es' => 'es_ES', 'fr' => 'fr_FR' ];

    /**
     * The locale for the current request.
     *
     * @var string
     */
    protected $requestLocale;

    /**
     * The path to locales.
     *
     * @var string
     */
    protected $path;

    /**
     * Initializes the Locale.
     *
     * @param array  $config The locale config.
     * @param string $path   The path to locales.
     */
    public function __construct($config, $path)
    {
        $this->path   = $path;
        $this->config = $config;
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
        $locale = $this->getRequestLocale();

        $this->changeLocale($locale);
        $this->changeTimeZone($this->getTimeZone()->getName());
    }

    /**
     * Configures the Locale service.
     *
     * @param array $config The locale configuration.
     *
     * @return Locale
     */
    public function configure($configs)
    {
        if (empty($configs) || !is_array($configs)) {
            return $this;
        }

        foreach ($configs as $context => $config) {
            // Convert a timezone id to a timezone name
            if (array_key_exists('timezone', $config)) {
                $this->config[$context]['timezone'] =
                    $this->getTimeZoneName($config['timezone']);
            }

            foreach ($this->config[$context]['language'] as $key => &$value) {
                if (array_key_exists($key, $config['language'])) {
                    $value = $config['language'][$key];
                }
            }
        }

        return $this;
    }

    /**
     * Returns the list of available locales for context.
     *
     * @param string $context The context to get available locale for.
     *
     * @return array The list of available locales for context.
     */
    public function getAvailableLocales($context = null)
    {
        $locales = [];

        foreach ($this->config[$this->getContext($context)]['language']['available'] as $locale) {
            $locales[$locale] = ucfirst(\Locale::getDisplayName($locale));
        }

        return $locales;
    }

    /**
     * Return the current context.
     *
     * @param string $context The explicit context.
     *
     * @return string The current context.
     */
    public function getContext($context = null)
    {
        if (!empty($context)) {
            return $context;
        }

        return $this->context;
    }

    /**
     * Returns the current locale for context.
     *
     * @param string $context The context to get locale for.
     *
     * @return string The current locale for context.
     */
    public function getLocale($context = null)
    {
        return $this->config[$this->getContext($context)]['language']['selected'];
    }

    /**
     * Returns the current locale name.
     *
     * @param string $context The context to get locale name for.
     *
     * @return string The current locale name.
     */
    public function getLocaleName($context = null)
    {
        return ucfirst(\Locale::getDisplayName($this->getLocale($context)));
    }

    /**
     * Returns the current locale without region.
     *
     * @param string $context The context to get short locale for.
     *
     * @return string The current locale without region.
     */
    public function getLocaleShort($context = null)
    {
        return explode('_', $this->getLocale($context))[0];
    }

    /**
     * Returns the locale for the current request.
     *
     * @param string $context The context to get request locale for.
     *
     * @return string The locale for the current request.
     */
    public function getRequestLocale($context = null)
    {
        if (empty($this->requestLocale)) {
            return $this->getLocale($context);
        }

        return $this->requestLocale;
    }

    /**
     * Returns the locale for the current request without region.
     *
     * @param string $context The context to get request locale for.
     *
     * @return string The current request locale without region.
     */
    public function getRequestLocaleShort($context = null)
    {
        return explode('_', $this->getRequestLocale($context))[0];
    }

    /**
     * Returns the locale slug for the current request..
     *
     * @param string $context The context to get request locale for.
     *
     * @return string The locale slug for the current request.
     */
    public function getRequestSlug($context = null)
    {
        $slugs = $this->config[$this->getContext($context)]['language']['slug'];

        if (!array_key_exists($this->getRequestLocale(), $slugs)) {
            return $this->getRequestLocale();
        }

        return $slugs[$this->getRequestLocale()];
    }

    /**
     * Returns the slugs for the current locale.
     *
     * @param string $context The context to get slugs for.
     *
     * @return string The slug for the current locale.
     */
    public function getSlug($context = null)
    {
        $slugs = $this->config[$this->getContext($context)]['language']['slug'];

        if (!array_key_exists($this->getLocale(), $slugs)) {
            return $this->getLocale();
        }

        return $slugs[$this->getLocale($context)];
    }

    /**
     * Returns the list of slugs for the locales.
     *
     * @param string $context The context to get slugs for.
     *
     * @return array The list of slugs for the locales.
     */
    public function getSlugs($context = null)
    {
        return $this->config[$this->getContext($context)]['language']['slug'];
    }

    /**
     * Returns the list of all available locales.
     *
     * @param string $context The context to get supported locales for.
     *
     * @return array The list of all available locales.
     */
    public function getSupportedLocales($context = null)
    {
        $codes   = $this->config['backend']['language']['available'];
        $locales = [];

        if ($this->getContext($context) === 'frontend') {
            $codes = \ResourceBundle::getLocales('');
        }

        foreach ($codes as $code) {
            if (strlen($code) <= 2) {
                continue;
            }

            $locales[$code] = ucfirst(\Locale::getDisplayName($code));
        }

        return $locales;
    }

    /**
     * Get the timezone.
     *
     * @return string The current timezone.
     */
    public function getTimeZone($context = null)
    {
        try {
            return new \DateTimeZone($this->config[$this->getContext($context)]['timezone']);
        } catch (\Exception $e) {
            return new \DateTimeZone('UTC');
        }
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
        $this->context = 'frontend';

        if (empty($context)) {
            return $this;
        }

        // Remove when more contexts supported
        if (strpos($context, 'admin') !== false
            || strpos($context, 'backend') !== false
            || strpos($context, 'manager') !== false
        ) {
            $this->context = 'backend';
        }

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
    public function setLocale($locale)
    {
        if (empty($locale)) {
            return $this;
        }

        if (in_array($locale, $this->config[$this->getContext()]['language']['available'])) {
            $this->config[$this->getContext()]['language']['selected'] = $locale;
        }

        return $this;
    }

    /**
     * Changes the locale for the current request.
     *
     * @param string $locale  The locale for the current request.
     *
     * @return Locale
     */
    public function setRequestLocale($locale)
    {
        $this->requestLocale = $locale;

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
     * $param string $locale The locale to change to.
     *
     * @codeCoverageIgnore
     */
    protected function changeLocale($locale)
    {
        if (array_key_exists($locale, $this->fixes)) {
            $locale = $this->fixes[$locale];
        }

        \Locale::setDefault($locale);

        // Set locale for gettext
        setlocale(LC_ALL, $locale . '.UTF-8');
        setlocale(LC_NUMERIC, 'C');

        $domain = 'messages';

        $this->addTextDomain($domain, $this->path);
        textdomain($domain);
    }

    /**
     * Changes the PHP timezone basing on configuration.
     *
     * @param string $name The timezone name.
     *
     * @codeCoverageIgnore
     */
    protected function changeTimeZone($name)
    {
        date_default_timezone_set($name);
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
