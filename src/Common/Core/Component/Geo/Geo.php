<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Geo;

use Symfony\Component\Intl\Intl;

/**
 * The Geo class provides countries and regions basing on the current locale.
 */
class Geo
{
    /**
     * The list of regions by country
     *
     * @var array
     */
    protected $regions = [
        'ES' => [
            'Álava', 'Albacete', 'Alicante/Alacant', 'Almería', 'Asturias',
            'Ávila', 'Badajoz', 'Barcelona', 'Burgos', 'Cáceres', 'Cádiz',
            'Cantabria', 'Castellón/Castelló', 'Ceuta', 'Ciudad Real',
            'Córdoba', 'Cuenca', 'Girona', 'Las Palmas', 'Granada',
            'Guadalajara', 'Guipúzcoa', 'Huelva', 'Huesca', 'Illes Balears',
            'Jaén', 'A Coruña', 'La Rioja', 'León', 'Lleida', 'Lugo', 'Madrid',
            'Málaga', 'Melilla', 'Murcia', 'Navarra', 'Ourense', 'Palencia',
            'Pontevedra', 'Salamanca', 'Segovia', 'Sevilla', 'Soria',
            'Tarragona', 'Santa Cruz de Tenerife', 'Teruel', 'Toledo',
            'Valencia/València', 'Valladolid', 'Vizcaya', 'Zamora', 'Zaragoza'
        ]
    ];

    /**
     * Initializes the Geo service.
     *
     * @param Locale $locale The locale service.
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Returns the list of countries.
     *
     * @return array The list of countries.
     */
    public function getCountries()
    {
        $countries = Intl::getRegionBundle()
            ->getCountryNames($this->locale->getLocale());

        asort($countries);

        return $countries;
    }

    /**
     * Returns the list of regions for the given country.
     *
     * @param string $country The country code.
     *
     * @return array The list of regions by country.
     */
    public function getRegions($country)
    {
        if (array_key_exists($country, $this->regions)) {
            return $this->regions[$country];
        }

        return [];
    }
}
