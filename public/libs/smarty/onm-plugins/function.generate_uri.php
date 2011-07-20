<?php
/*
 * -------------------------------------------------------------
 * File:     	function.typecontent.php
 * Comprueba el tipo y escribe el nombre o la imag
 */
function smarty_function_generate_uri($params, &$smarty) {

    if (isset($params['slug'])) {
        $slug = $params['slug'];
    } elseif ( isset($params['title']) ) {
        $slug = String_Utils::get_title($params['title']);
    }

    $output = Uri::generate( $params['content_type'],
                            array(
                                'id' => $params['id'],
                                'date' => date('Y-m-d', strtotime($params['date'])),
                                'category' => $params['category_name'],
                                'slug' => $slug,
                            )
                        );

	return $output;
}


