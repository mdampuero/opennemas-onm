<?php
function smarty_function_gravatar($params, &$smarty) {

    $url = '';
    if (array_key_exists('email', $params)) {

        $size = '16';
        $d = 'mm'; // mm, identicon, 404, monsterid, wavatar
        $r = 'g';
        $img = false;
        $atts = array();
        $default_icon = "&d=".urlencode($params["image_dir"]."favicon.png");
        if (array_key_exists('size',$params))  $size = $params['size'];
        if (array_key_exists('image',$params))  $img = $params['image'];

        $url = 'http://www.gravatar.com/avatar/';

        $url .= md5( strtolower( trim( $params['email'] ) ) );
        $url .= "?s=$size&amp;d=$d&amp;r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
    }


    return $url;
}
?>
