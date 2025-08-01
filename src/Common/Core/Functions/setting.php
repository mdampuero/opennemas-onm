<?php
/**
 * Returns the logo item
 *
 * @param string $format The logo format (default|simple|favico|embed).
 *
 * @return object The logo image object.
 */
function get_logo($format = 'default') : ?object
{
    return getService('core.helper.setting')->getLogo($format);
}

/**
 * Checks if the logo for the provided format is configured.
 *
 * @param string $format The logo format (default|simple|favico|embed).
 *
 * @return bool True if the logo for the provided format is configured. False
 *              otherwise.
 */
function has_logo($format = 'default') : bool
{
    return getService('core.helper.setting')->hasLogo($format);
}
