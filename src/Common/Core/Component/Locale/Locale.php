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
     * Locale auto-correction values.
     *
     * @var array
     */
    protected $fixes = [ 'en' => 'en_US', 'gl' => 'gl_ES' ];

    /**
     * The current local.
     *
     * @var string
     */
    protected $locale = 'en_US';

    /**
     * The path to locales.
     *
     * @var string
     */
    protected $path;

    /**
     * The current timezone.
     *
     * @var string
     */
    protected $timezone;

    /**
     * Initializes the Locale.
     *
     * @param array  $locales The available locales.
     * @param string $path    The available locales.
     */
    public function __construct($locales, $path)
    {
        $this->locales  = $locales;
        $this->path     = $path;
        $this->timezone = new \DateTimeZone('UTC');
    }

    /**
     * Adds a new text domain.
     *
     * @param string $domain Text domain name.
     * @param string $path   Path to text domain.
     */
    public function addTextDomain($domain, $path)
    {
        bindtextdomain($domain, $path);
    }

    /**
     * Returns the list of all available locales.
     *
     * @return array The list of all available locales.
     */
    public function getAvailableLocales()
    {
        $codes   = \ResourceBundle::getLocales('');
        $locales = [];

        foreach ($codes as $code) {
            $locales[$code] = ucfirst(\Locale::getDisplayName($code));
        }

        return $locales;
    }

    /**
     * Returns the current locale.
     *
     * @return string The current locale.
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Returns the list of available locales.
     *
     * @return array The list of available locales.
     */
    public function getLocales()
    {
        foreach ($this->locales as $locale) {
            $locales[$locale] = ucfirst(\Locale::getDisplayLanguage($locale));
        }

        return $locales;
    }

    /**
     * Returns the current locale name.
     *
     * @return string The current locale name.
     */
    public function getLocaleName()
    {
        return ucfirst(\Locale::getDisplayLanguage($this->locale));
    }

    /**
     * Returns the current locale without region.
     *
     * @return string The current locale without region.
     */
    public function getLocaleShort()
    {
        return explode('_', $this->locale)[0];
    }

    /**
     * Get the timezone.
     *
     * @return string The current timezone.
     */
    public function getTimeZone()
    {
        return $this->timezone;
    }

    /**
     * Changes the system locale.
     *
     * @param string $locale The locale.
     */
    public function setLocale($locale)
    {
        // Try to auto-correct the locale
        if (array_key_exists($locale, $this->fixes)) {
            $locale = $this->fixes[$locale];
        }

        if (in_array($locale, $this->locales)) {
            $this->locale = $locale;
        }

        \Locale::setDefault($this->locale);

        // Set locale for gettext
        setlocale(LC_ALL, $this->locale . '.UTF-8');
        setlocale(LC_NUMERIC, 'C');

        $domain = 'messages';

        $this->addTextDomain($domain, $this->path);
        textdomain($domain);
    }

    /**
     * Changes the timezone.
     *
     * @param integer $timezone The timezone id.
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
            $this->timezone = new \DateTimeZone($timezone);
        }

        date_default_timezone_set($this->timezone->getName());
    }
}
