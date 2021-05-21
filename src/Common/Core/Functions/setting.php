<?php
/**
 * Returns the logo item
 *
 * @param string $format The logo format(site_logo|mobile_logo|favico|sn_default_img).
 *
 * @return object The logo image object.
 */
function get_logo($format = 'site_logo') : ?object
{
    return getService('core.helper.setting')->getLogo($format);
}

/**
 * Checks if the logo for the provided format is configured.
 *
 * @param string $format The logo format (site_logo|mobile_logo|favico|sn_default_img).
 *
 * @return bool True if the logo for the provided format is configured. False
 *              otherwise.
 */
function has_logo($format = 'site_logo') : bool
{
    return getService('core.helper.setting')->hasLogo($format);
}
