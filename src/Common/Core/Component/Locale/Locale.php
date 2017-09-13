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
     * Available locales to the frontend. This are the same as \ResourceBundle::getLocales(''), but excluding these:
     *
     *  ['ca_FR', 'ca_IT', 'en_CA', 'en_BE', 'en_ER', 'en_GH', 'en_IN', 'en_MG', 'en_MT', 'en_NA', 'en_RW', 'en_SD',
     * 'en_SS', 'en_TO', 'en_ZA', 'es_PH','en_PO', 'en_PR', 'es_US', 'et_EE', 'de_BE', 'de_CH', 'en_CM', 'ar_DZ',
     * 'fr_GQ', 'ar_KM', 'fr_CA', 'fr_LU', 'fr_MA', 'ar_AR', 'fr_CM', 'ar_DJ', 'fr_GN', 'fr_MG', 'ar_MR', 'fr_MR',
     * 'fr_MU', 'fr_RW', 'fr_SC', 'fr_SN', 'fr_SY', 'fr_TD', 'fr_TG', 'fr_TN', 'fr_DJ', 'en_VU', 'ga_IE', 'gd_GB',
     * 'gu_IN', 'it_CH', 'en_IN', 'kw_GB', 'de_LU', 'fr_CF', 'fr_CG', 'ki_KE', 'kn_IN', 'fr_CD', 'ml_IN', 'mr_IN',
     * 'ne_IN', 'en_SX', 'om_ET', 'om_KE', 'ru_RU', 'ps_AF', 'pt_AO', 'pt_BR', 'fr_BE', 'es_EC', 'pt_MO', 'qu_EC',
     * 'qu_PE', 'ru_BY', 'ru_MD', 'se_FI', 'se_NO', 'sg_CF', 'en_ZW', 'sn_ZW', 'fr_DG', 'so_ET', 'en_KE', 'sv_FI',
     * 'sw_KE', 'en_TZ', 'en_UG', 'sw_UG', 'ta_LK', 'en_MY', 'ta_SG', 'tr_CY', 'en_PK', 'en_NG', 'yo_NG', 'ti_ET',
     * 'sv_SE', 'lu_CD', 'da_GL', 'bo_IN', 'ar_SO']
     *
     */
    const AVAILABLE_LOCALES = ["bg" => "bg_BG", "de" => "de_DE", "es" => "es_ES", "fi" => "fi_FI", "fo" => "fo_FO",
        "fr" => "fr_FR", "hr" => "hr_HR", "hu" => "hu_HU", "id" => "id_ID", "is" => "is_IS", "it" => "it_IT",
        "lt" => "lt_LT", "lv" => "lv_LV", "mg" => "mg_MG", "mk" => "mk_MK", "mt" => "mt_MT", "nl" => "nl_NL",
        "pl" => "pl_PL", "pt" => "pt_PT", "ro" => "ro_RO", "rw" => "rw_RW", "se" => "se_SE", "sk" => "sk_SK",
        "so" => "so_SO", "th" => "th_TH", "to" => "to_TO", "tr" => "tr_TR", "na" => "af_NA", "za" => "af_ZA",
        "gh" => "ak_GH", "et" => "am_ET", "ae" => "ar_AE", "bh" => "ar_BH", "eg" => "ar_EG", "eh" => "ar_EH",
        "er" => "ar_ER", "il" => "ar_IL", "iq" => "ar_IQ", "jo" => "ar_JO", "kw" => "ar_KW", "lb" => "ar_LB",
        "ly" => "ar_LY", "ma" => "ar_MA", "om" => "ar_OM", "ps" => "ar_PS", "qa" => "ar_QA", "sa" => "ar_SA",
        "sd" => "ar_SD", "ss" => "ar_SS", "sy" => "ar_SY", "td" => "ar_TD", "tn" => "ar_TN", "ye" => "ar_YE",
        "in" => "as_IN", "by" => "be_BY", "bd" => "bn_BD", "bn" => "bn_IN", "cn" => "bo_CN", "br" => "br_FR",
        "ad" => "ca_AD", "ca" => "ca_ES", "cz" => "cs_CZ", "gb" => "cy_GB", "dk" => "da_DK", "at" => "de_AT",
        "li" => "de_LI", "bt" => "dz_BT", "ee" => "ee_GH", "tg" => "ee_TG", "cy" => "el_CY", "gr" => "el_GR",
        "ag" => "en_AG", "ai" => "en_AI", "as" => "en_AS", "au" => "en_AU", "bb" => "en_BB", "bm" => "en_BM",
        "bs" => "en_BS", "bw" => "en_BW", "bz" => "en_BZ", "cc" => "en_CC", "ck" => "en_CK", "cx" => "en_CX",
        "dg" => "en_DG", "dm" => "en_DM", "fj" => "en_FJ", "fk" => "en_FK", "fm" => "en_FM", "en" => "en_GB",
        "gd" => "en_GD", "gg" => "en_GG", "gi" => "en_GI", "gm" => "en_GM", "gu" => "en_GU", "gy" => "en_GY",
        "hk" => "en_HK", "ie" => "en_IE", "im" => "en_IM", "io" => "en_IO", "je" => "en_JE", "jm" => "en_JM",
        "ki" => "en_KI", "kn" => "en_KN", "ky" => "en_KY", "lc" => "en_LC", "lr" => "en_LR", "ls" => "en_LS",
        "mh" => "en_MH", "mo" => "en_MO", "mp" => "en_MP", "ms" => "en_MS", "mu" => "en_MU", "mw" => "en_MW",
        "nf" => "en_NF", "nr" => "en_NR", "nu" => "en_NU", "nz" => "en_NZ", "pg" => "en_PG", "ph" => "en_PH",
        "pn" => "en_PN", "pw" => "en_PW", "sb" => "en_SB", "sc" => "en_SC", "sg" => "en_SG", "sh" => "en_SH",
        "sl" => "en_SL", "sz" => "en_SZ", "tc" => "en_TC", "tk" => "en_TK", "tt" => "en_TT", "tv" => "en_TV",
        "um" => "en_UM", "us" => "en_US", "vc" => "en_VC", "vg" => "en_VG", "vi" => "en_VI", "ws" => "en_WS",
        "zm" => "en_ZM", "ar" => "es_AR", "bo" => "es_BO", "cl" => "es_CL", "co" => "es_CO", "cr" => "es_CR",
        "cu" => "es_CU", "do" => "es_DO", "ea" => "es_EA", "gq" => "es_GQ", "gt" => "es_GT", "hn" => "es_HN",
        "ic" => "es_IC", "mx" => "es_MX", "ni" => "es_NI", "pa" => "es_PA", "pe" => "es_PE", "pr" => "es_PR",
        "py" => "es_PY", "sv" => "es_SV", "uy" => "es_UY", "ve" => "es_VE", "eu" => "eu_ES", "af" => "fa_AF",
        "ir" => "fa_IR", "cm" => "ff_CM", "gn" => "ff_GN", "mr" => "ff_MR", "sn" => "ff_SN", "bf" => "fr_BF",
        "bi" => "fr_BI", "bj" => "fr_BJ", "bl" => "fr_BL", "ch" => "fr_CH", "ci" => "fr_CI", "dz" => "fr_DZ",
        "ga" => "fr_GA", "gf" => "fr_GF", "gp" => "fr_GP", "ht" => "fr_HT", "km" => "fr_KM", "mc" => "fr_MC",
        "mf" => "fr_MF", "ml" => "fr_ML", "mq" => "fr_MQ", "nc" => "fr_NC", "ne" => "fr_NE", "pf" => "fr_PF",
        "pm" => "fr_PM", "re" => "fr_RE", "vu" => "fr_VU", "wf" => "fr_WF", "yt" => "fr_YT", "fy" => "fy_NL",
        "gl" => "gl_ES", "gv" => "gv_IM", "he" => "he_IL", "hi" => "hi_IN", "ba" => "hr_BA", "am" => "hy_AM",
        "ng" => "ig_NG", "ii" => "ii_CN", "sm" => "it_SM", "jp" => "ja_JP", "ge" => "ka_GE", "kl" => "kl_GL",
        "kh" => "km_KH", "kp" => "ko_KP", "kr" => "ko_KR", "lu" => "lb_LU", "ug" => "lg_UG", "ao" => "ln_AO",
        "cd" => "ln_CD", "cf" => "ln_CF", "cg" => "ln_CG", "la" => "lo_LA", "mm" => "my_MM", "no" => "nb_NO",
        "sj" => "nb_SJ", "zw" => "nd_ZW", "np" => "ne_NP", "aw" => "nl_AW", "be" => "nl_BE", "bq" => "nl_BQ",
        "cw" => "nl_CW", "sr" => "nl_SR", "sx" => "nl_SX", "nn" => "nn_NO", "or" => "or_IN", "os" => "os_GE",
        "ru" => "os_RU", "cv" => "pt_CV", "gw" => "pt_GW", "mz" => "pt_MZ", "st" => "pt_ST", "tl" => "pt_TL",
        "qu" => "qu_BO", "rm" => "rm_CH", "rn" => "rn_BI", "md" => "ro_MD", "kg" => "ru_KG", "kz" => "ru_KZ",
        "ua" => "ru_UA", "lk" => "si_LK", "si" => "sl_SI", "dj" => "so_DJ", "ke" => "so_KE", "al" => "sq_AL",
        "sq" => "sq_MK", "xk" => "sq_XK", "ax" => "sv_AX", "sw" => "sw_CD", "tz" => "sw_TZ", "ta" => "ta_IN",
        "my" => "ta_MY", "te" => "te_IN", "ti" => "ti_ER", "uk" => "uk_UA", "ur" => "ur_IN", "pk" => "ur_PK",
        "vn" => "vi_VN", "yo" => "yo_BJ", "zu" => "zu_ZA"];

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
     * Recover de locale from the url lang
     *
     *  @param String $langId Id for the lang with only two characters
     *
     *  @return String Locale complete.
     */
    public function getLocaleFromUrlLangId($langId)
    {
        return self::AVAILABLE_LOCALES[$langId];
    }

    /**
     * Recover de url key for the locale
     *
     *  @param String $langId Id for the lang with only two characters
     *
     *  @return String url lang Id.
     */
    public function getURLLangId($locale)
    {
        $keys = explode('_', $locale);
        if (sizeof($keys) != 2) {
            return;
        }
        $tmpArray = self::AVAILABLE_LOCALES;
        if (isset($tmpArray[$keys[0]]) && $locale == self::AVAILABLE_LOCALES[$keys[0]]) {
            return $keys[0];
        }
        $lowerKey = strtolower($keys[1]);
        if (isset($tmpArray[$lowerKey]) && $locale == self::AVAILABLE_LOCALES[$lowerKey]) {
            return $lowerKey;
        }
    }

    /**
     * Returns the list of all available locales.
     *
     * @return array The list of all available locales.
     */
    public function getAvailableLocales($searchText = null)
    {
        $codes   = self::AVAILABLE_LOCALES;
        $locales = [];

        $method = (empty($searchText))?
        function ($code) use (&$locales) {
            $locales[$code] = [
                'name' => ucfirst(\Locale::getDisplayName(self::AVAILABLE_LOCALES[$code])),
                'locale' => self::AVAILABLE_LOCALES[$code]
            ];
        }:
        function ($code) use (&$locales, $searchText) {
            $name = ucfirst(\Locale::getDisplayName(self::AVAILABLE_LOCALES[$code]));
            if (strpos(strtolower($name), strtolower($searchText)) !== false) {
                $locales[$code] = ['name' => $name, 'locale' => self::AVAILABLE_LOCALES[$code]];
            }
        };

        array_map($method, array_keys(self::AVAILABLE_LOCALES));

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
