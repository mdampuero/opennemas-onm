<?php

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Content;
use Symfony\Component\DependencyInjection\Container;

/**
* Perform searches in Database related with one company.
*/
class CompanyHelper
{
    /**
     * The services container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Initializes the CompanyHelper.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container     = $container;
        $this->contentHelper = $container->get('core.helper.content');
        $this->relatedHelper = $container->get('core.helper.related');
    }

    /**
     * Returns the logo for the company.
     *
     * @param Content $item The company.
     *
     * @return Content The logo of the company.
     */
    public function getCompanyLogo($item) : ?array
    {
        $related = $this->relatedHelper->getRelated($this->contentHelper->getContent($item), 'logo');

        if (empty($related)) {
            return null;
        }

        return array_shift($related);
    }

    /**
     * Checks if the company has a logo.
     *
     * @param Content $item The company.
     *
     * @return bool True if the company has a logo. False otherwise.
     */
    public function hasCompanyLogo($item) : bool
    {
        return !empty($this->getCompanyLogo($item));
    }

    /**
     * Returns the related products of the company.
     *
     * @param Content $item   The item to get the products from.
     * @param int     $number The number of products to get from the company.
     *
     * @return array The array of products.
     */
    public function getProducts($item, $number = null) : ?array
    {
        $products = $this->relatedHelper->getRelated($this->contentHelper->getContent($item), 'photo');

        return array_slice($products, 0, $number);
    }

    /**
     * Returns true if the company has products.
     *
     * @param mixed $item The item to check if has products or not.
     *
     * @return Content True if the content has products, false otherwise.
     */
    public function hasProducts($item) : bool
    {
        return !empty($this->getProducts($item));
    }

    /**
     * Returns the array of social media configured for the company.
     *
     * @param Content $item The company to get the social media from.
     *
     * @return array The array of social media configured for the company.
     */
    public function getSocialMedia($item) : array
    {
        return array_filter([
            'whatsapp'  => $item->whatsapp,
            'instagram' => $item->instagram,
            'facebook'  => $item->facebook,
            'twitter'   => $item->twitter,
            'phone'     => $item->phone,
            'email'     => $item->email
        ]);
    }

    /**
     * Returns true if the company has social media.
     *
     * @param Content $item The company to check if has social media or not.
     *
     * @retun bool True if the company has social media, false otherwise.
     */
    public function hasSocialMedia($item) : bool
    {
        return !empty($this->getSocialMedia($item));
    }

    /**
     * Returns the address of the company.
     *
     * @param Content $item The company to get the address from.
     *
     * @return string The address of the company.
     */
    public function getAddress($item) : ?string
    {
        return $item->address;
    }

    /**
     * Returns true if the company has an address.
     *
     * @param Content $item The company to get the address from.
     *
     * @return bool True if the company has an address, false otherwise.
     */
    public function hasAddress($item) : bool
    {
        return !empty($this->getAddress($item));
    }

    /**
     * Returns the sector of the company.
     *
     * @param Content $item The company to get the sector from.
     *
     * @return string The sector of the company.
     */
    public function getSector($item) : ?string
    {
        if (!$item->sector) {
            return '';
        }
        return _(ucfirst(implode(' ', explode('_', $item->sector))));
    }

    /**
     * Returns true if the company has a sector.
     *
     * @param Content $item The company to get the sector from.
     *
     * @return bool True if the company has a sector, false otherwise.
     */
    public function hasSector($item) : bool
    {
        return !empty($this->getSector($item));
    }

    /**
     * Returns the whatsapp of the company.
     *
     * @param Content $item The company to get the whatsapp from.
     *
     * @return string The whatsapp of the company.
     */
    public function getWhatsapp($item) : ?string
    {
        return $item->whatsapp;
    }

    /**
     * Returns true if the company has whatsapp.
     *
     * @param Content $item The company to get the whatsapp from.
     *
     * @return bool True if the company has a whatsapp, false otherwise.
     */
    public function hasWhatsapp($item) : bool
    {
        return !empty($this->getWhatsapp($item));
    }

    /**
     * Returns the twitter of the company.
     *
     * @param Content $item The company to get the twitter from.
     *
     * @return string The twitter of the company.
     */
    public function getTwitter($item) : ?string
    {
        return $item->twitter;
    }

    /**
     * Returns true if the company has twitter.
     *
     * @param Content $item The company to get the twitter from.
     *
     * @return bool True if the company has a twitter, false otherwise.
     */
    public function hasTwitter($item) : bool
    {
        return !empty($this->getTwitter($item));
    }

    /**
     * Returns the facebook of the company.
     *
     * @param Content $item The company to get the facebook from.
     *
     * @return string The facebook of the company.
     */
    public function getFacebook($item) : ?string
    {
        return $item->facebook;
    }

    /**
     * Returns true if the company has facebook.
     *
     * @param Content $item The company to get the facebook from.
     *
     * @return bool True if the company has a facebook, false otherwise.
     */
    public function hasFacebook($item) : bool
    {
        return !empty($this->getFacebook($item));
    }

    /**
     * Returns the instagram of the company.
     *
     * @param Content $item The company to get the instagram from.
     *
     * @return string The instagram of the company.
     */
    public function getInstagram($item) : ?string
    {
        return $item->instagram;
    }

    /**
     * Returns true if the company has instagram.
     *
     * @param Content $item The company to get the instagram from.
     *
     * @return bool True if the company has a instagram, false otherwise.
     */
    public function hasInstagram($item) : bool
    {
        return !empty($this->getInstagram($item));
    }

    /**
     * Returns the phone of the company.
     *
     * @param Content $item The company to get the phone from.
     *
     * @return string The phone of the company.
     */
    public function getPhone($item) : ?string
    {
        return $item->phone;
    }

    /**
     * Returns true if the company has phone.
     *
     * @param Content $item The company to get the phone from.
     *
     * @return bool True if the company has a phone, false otherwise.
     */
    public function hasPhone($item) : bool
    {
        return !empty($this->getPhone($item));
    }

    /**
     * Returns the email of the company.
     *
     * @param Content $item The company to get the email from.
     *
     * @return string The email of the company.
     */
    public function getEmail($item) : ?string
    {
        return $item->email;
    }

    /**
     * Returns true if the company has email.
     *
     * @param Content $item The company to get the email from.
     *
     * @return bool True if the company has a email, false otherwise.
     */
    public function hasEmail($item) : bool
    {
        return !empty($this->getEmail($item));
    }

    /**
     * Returns the timetable of the company.
     *
     * @param Content $item The company to get the timetable from.
     *
     * @return array The array with the timetable.
     */
    public function getTimetable($item) : array
    {
        if (empty($item->timetable)) {
            return [];
        }

        return array_filter($item->timetable, function ($day) {
            return !empty($day['enabled']);
        });
    }

    /**
     * Returns true if the company has timetable.
     *
     * @param Content $item The company to check if has timetable or not.
     *
     * @return bool True if the company has timetable, false otherwise.
     */
    public function hasTimetable($item) : bool
    {
        return !empty($this->getTimetable($item));
    }
}
