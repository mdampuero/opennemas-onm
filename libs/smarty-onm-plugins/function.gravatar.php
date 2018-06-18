<?php
/**
 * Smarty plugin
 * Returns the gravatar image url given a set of parameters
 *
 * {gravatar email='example@example.com' image=true default_image='path/to/image.png' size=16}
 *
 */
function smarty_function_gravatar($params)
{
    if (!array_key_exists('email', $params)) {
        return '';
    }

    // mm, identicon, 404, monsterid, wavatar, blank,
    $defaultImage = (isset($params['default_image']))
        ? urlencode($params['default_image']) : 'mm';
    $size         = (isset($params['size'])) ? $params['size'] : 16;
    $email        = md5(strtolower(trim($params['email'])));
    $htmlAttrs    = [
        'width' => $size,
        'height' => $size,
    ];

    if (array_key_exists('class', $params)) {
        $htmlAttrs['class'] = $params['class'];
    }

    $gravatarUrlParams = [
        's' => $size,
        'd' => $defaultImage,
        'r' => 'g',
    ];

    $gravatarUrlParamsFinal = [];
    foreach ($gravatarUrlParams as $key => $val) {
        $gravatarUrlParamsFinal[] = sprintf('%s=%s', $key, $val);
    }

    $url = sprintf(
        '//www.gravatar.com/avatar/%s?%s',
        $email,
        implode('&', $gravatarUrlParamsFinal)
    );


    $htmlAttrsFinal = [];
    foreach ($htmlAttrs as $key => $val) {
        $htmlAttrsFinal[] = sprintf('%s="%s"', $key, $val);
    }

    return sprintf('<img src="%s" %s />', $url, implode(' ', $htmlAttrsFinal));
}
