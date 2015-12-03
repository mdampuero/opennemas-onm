<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\VAT;

use Sparkling\VATBundle\Service\VATService as BaseVATService;

class VATService
{
    /**
     * The list of countries and taxes.
     *
     * @var array
     */
    protected $taxes = [
        'GB' => [ 'name' => 'United Kingdom', 'value' => 20 ],
        'BE' => [ 'name' => 'Belgium', 'value' => 21 ],
        'BG' => [ 'name' => 'Bulgaria', 'value' => 20 ],
        'CZ' => [ 'name' => 'Czech Republic ', 'value' => 21 ],
        'DK' => [ 'name' => 'Denmark', 'value' => 25 ],
        'DE' => [ 'name' => 'Germany', 'value' => 19 ],
        'EE' => [ 'name' => 'Estonia', 'value' => 20 ],
        'EL' => [ 'name' => 'Greece', 'value' => 23 ],
        'ES' => [ 'name' => 'Spain', 'value' => 21 ],
        'FR' => [ 'name' => 'France', 'value' => 20 ],
        'HR' => [ 'name' => 'Croatia', 'value' => 25 ],
        'IE' => [ 'name' => 'Ireland', 'value' => 23 ],
        'IT' => [ 'name' => 'Italy', 'value' => 22 ],
        'CY' => [ 'name' => 'Cyprus', 'value' => 19 ],
        'LV' => [ 'name' => 'Latvia', 'value' => 21 ],
        'LT' => [ 'name' => 'Lithuania', 'value' => 21 ],
        'LU' => [ 'name' => 'Luxembourg', 'value' => 15 ],
        'HU' => [ 'name' => 'Hungary', 'value' => 27 ],
        'MT' => [ 'name' => 'Malta', 'value' => 18 ],
        'NL' => [ 'name' => 'Netherlands', 'value' => 21 ],
        'AT' => [ 'name' => 'Austria', 'value' => 20 ],
        'PL' => [ 'name' => 'Poland', 'value' => 23 ],
        'PT' => [ 'name' => 'Portugal', 'value' => 23 ],
        'RO' => [ 'name' => 'Romania', 'value' => 24 ],
        'SI' => [ 'name' => 'Slovenia', 'value' => 22 ],
        'SK' => [ 'name' => 'Slovakia', 'value' => 20 ],
        'FI' => [ 'name' => 'Finland', 'value' => 24 ],
        'SE' => [ 'name' => 'Sweden', 'value' => 25 ],
    ];

    /**
     * Initializes the VAT service.
     *
     * @param BaseVATService $validator The VAT validator service.
     */
    public function __construct(BaseVATService $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Returns the VAT value from the coutry code.
     *
     * @param string $code The coutry code.
     *
     * @return integer The VAT value.
     */
    public function getVatFromCode($code)
    {
        if (!array_key_exists($code, $this->taxes)) {
            return 0;
        }

        return $this->taxes[$code]['value'];
    }

    /**
     * Returns the VAT value from the coutry code.
     *
     * @param string $code The country code.
     *
     * @return integer The VAT value.
     */
    public function getVatFromCountry($country)
    {
        foreach ($this->taxes as $tax) {
            if ($tax['name'] === $country) {
                return $tax['value'];
            }
        }

        return 0;
    }

    /**
     * Validates the VAT number.
     *
     * @param string $country   The country code.
     * @param string $vatNumber The VAT number to validate.
     *
     * @return boolean True if the given VAT number is valid. Othewise, returns
     *                 false.
     */
    public function validate($country, $vatNumber)
    {
        return $this->validator->validate($country, $vatNumber);
    }
}
