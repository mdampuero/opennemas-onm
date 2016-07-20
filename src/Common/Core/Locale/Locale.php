<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Locale;

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
    protected $timezone = 'UTC';

    /**
     * Initializes the Locale.
     *
     * @param array $locales The available locales.
     */
    public function __construct($locales, $path)
    {
        $this->locales = $locales;
        $this->path    = $path;
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
        return \Locale::getDisplayLanguage($this->locale);
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

        bindtextdomain($domain, $this->path);
        textdomain($domain);
    }

    /**
     * Changes the timezone.
     *
     * @param integer $timezone The timezone id.
     */
    public function setTimeZone($timezone)
    {
        $timezones = \DateTimeZone::listIdentifiers();

        if (array_key_exists($timezone, $timezones)) {
            $this->timezone = $timezones[$timezone];
        }

        date_default_timezone_set($this->timezone);
    }
}
