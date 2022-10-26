<?php

/**
 * Returns the logo for an item of type company.
 *
 * @param mixed $item The item to get the logo from.
 *
 * @return Content The photo object for the logo.
 */
function get_company_logo($item = null) : ?array
{
    return getService('core.helper.company')->getCompanyLogo($item);
}

/**
 * Returns true if the company has a logo.
 *
 * @param mixed $item The item to check if has logo or not.
 *
 * @return Content True if the content has a logo, false otherwise.
 */
function has_company_logo($item = null) : bool
{
    return getService('core.helper.company')->hasCompanyLogo($item);
}

/**
 * Returns the related products of the company.
 *
 * @param Content $item   The item to get the products from.
 * @param int     $number The number of products to get from the company.
 *
 * @return array The array of products.
 */
function get_products($item, $number = null) : ?array
{
    return getService('core.helper.company')->getProducts($item, $number);
}

/**
 * Returns true if the company has products.
 *
 * @param mixed $item The item to check if has products or not.
 *
 * @return Content True if the content has products, false otherwise.
 */
function has_products($item) : bool
{
    return getService('core.helper.company')->hasProducts($item);
}

/**
 * Returns the array of social media configured for the company.
 *
 * @param Content $item The company to get the social media from.
 *
 * @return array The array of social media configured for the company.
 */
function get_social_media($item) : array
{
    return getService('core.helper.company')->getSocialMedia($item);
}

/**
 * Returns true if the company has social media.
 *
 * @param Content $item The company to check if has social media or not.
 *
 * @retun bool True if the company has social media, false otherwise.
 */
function has_social_media($item) : bool
{
    return getService('core.helper.company')->hasSocialMedia($item);
}

/**
 * Returns the address of the company.
 *
 * @param Content $item The company to get the address from.
 *
 * @return string The address of the company.
 */
function get_address($item) : ?string
{
    return getService('core.helper.company')->getAddress($item);
}

/**
 * Returns true if the company has an address.
 *
 * @param Content $item The company to get the address from.
 *
 * @return bool True if the company has an address, false otherwise.
 */
function has_address($item) : bool
{
    return getService('core.helper.company')->hasAddress($item);
}

/**
 * Returns true if the company has a sector.
 *
 * @param Content $item The company to get the sector from.
 *
 * @return bool True if the company has a sector, false otherwise.
 */
function has_sector($item) : bool
{
    return getService('core.helper.company')->hasSector($item);
}

/**
 * Returns the sector of the company.
 *
 * @param Content $item The company to get the sector from.
 *
 * @return string The sector of the company.
 */
function get_whatsapp($item) : ?string
{
    return getService('core.helper.company')->getWhatsapp($item);
}

/**
 * Returns true if the company has whatsapp.
 *
 * @param Content $item The company to get the whatsapp from.
 *
 * @return bool True if the company has a whatsapp, false otherwise.
 */
function has_whatsapp($item) : bool
{
    return getService('core.helper.company')->hasWhatsapp($item);
}

/**
 * Returns the twitter of the company.
 *
 * @param Content $item The company to get the twitter from.
 *
 * @return string The twitter of the company.
 */
function get_twitter($item) : ?string
{
    return getService('core.helper.company')->getTwitter($item);
}

/**
 * Returns true if the company has twitter.
 *
 * @param Content $item The company to get the twitter from.
 *
 * @return bool True if the company has a twitter, false otherwise.
 */
function has_twitter($item) : bool
{
    return getService('core.helper.company')->hasTwitter($item);
}

/**
 * Returns the facebook of the company.
 *
 * @param Content $item The company to get the facebook from.
 *
 * @return string The facebook of the company.
 */
function get_facebook($item) : ?string
{
    return getService('core.helper.company')->getFacebook($item);
}

/**
 * Returns true if the company has facebook.
 *
 * @param Content $item The company to get the facebook from.
 *
 * @return bool True if the company has a facebook, false otherwise.
 */
function has_facebook($item) : bool
{
    return getService('core.helper.company')->hasFacebook($item);
}

/**
 * Returns the instagram of the company.
 *
 * @param Content $item The company to get the instagram from.
 *
 * @return string The instagram of the company.
 */
function get_instagram($item) : ?string
{
    return getService('core.helper.company')->getInstagram($item);
}

/**
 * Returns true if the company has instagram.
 *
 * @param Content $item The company to get the instagram from.
 *
 * @return bool True if the company has a instagram, false otherwise.
 */
function has_instagram($item) : bool
{
    return getService('core.helper.company')->hasInstagram($item);
}

/**
 * Returns the instagram of the company.
 *
 * @param Content $item The company to get the instagram from.
 *
 * @return string The instagram of the company.
 */
function get_linkedin($item) : ?string
{
    return getService('core.helper.company')->getLinkedin($item);
}

/**
 * Returns true if the company has instagram.
 *
 * @param Content $item The company to get the instagram from.
 *
 * @return bool True if the company has a instagram, false otherwise.
 */
function has_linkedin($item) : bool
{
    return getService('core.helper.company')->hasLinkedin($item);
}

/**
 * Returns the instagram of the company.
 *
 * @param Content $item The company to get the instagram from.
 *
 * @return string The instagram of the company.
 */
function get_youtube($item) : ?string
{
    return getService('core.helper.company')->getYoutube($item);
}

/**
 * Returns true if the company has instagram.
 *
 * @param Content $item The company to get the instagram from.
 *
 * @return bool True if the company has a instagram, false otherwise.
 */
function has_youtube($item) : bool
{
    return getService('core.helper.company')->hasYoutube($item);
}

/**
 * Returns the instagram of the company.
 *
 * @param Content $item The company to get the instagram from.
 *
 * @return string The instagram of the company.
 */
function get_tiktok($item) : ?string
{
    return getService('core.helper.company')->getTiktok($item);
}

/**
 * Returns true if the company has instagram.
 *
 * @param Content $item The company to get the instagram from.
 *
 * @return bool True if the company has a instagram, false otherwise.
 */
function has_tiktok($item) : bool
{
    return getService('core.helper.company')->hasTiktok($item);
}
/**
 * Returns the phone of the company.
 *
 * @param Content $item The company to get the phone from.
 *
 * @return string The phone of the company.
 */
function get_phone($item) : ?string
{
    return getService('core.helper.company')->getPhone($item);
}

/**
 * Returns true if the company has phone.
 *
 * @param Content $item The company to get the phone from.
 *
 * @return bool True if the company has a phone, false otherwise.
 */
function has_phone($item) : bool
{
    return getService('core.helper.company')->hasPhone($item);
}

/**
 * Returns the email of the company.
 *
 * @param Content $item The company to get the email from.
 *
 * @return string The email of the company.
 */
function get_email($item) : ?string
{
    return getService('core.helper.company')->getEmail($item);
}

/**
 * Returns true if the company has email.
 *
 * @param Content $item The company to get the email from.
 *
 * @return bool True if the company has a email, false otherwise.
 */
function has_email($item) : bool
{
    return getService('core.helper.company')->hasEmail($item);
}

/**
 * Returns the timetable of the company.
 *
 * @param Content $item The company to get the timetable from.
 *
 * @return array The array with the timetable.
 */
function get_timetable($item) : array
{
    return getService('core.helper.company')->getTimetable($item);
}

/**
 * Returns true if the company has timetable.
 *
 * @param Content $item The company to check if has timetable or not.
 *
 * @return bool True if the company has timetable, false otherwise.
 */
function has_timetable($item) : bool
{
    return getService('core.helper.company')->hasTimetable($item);
}
