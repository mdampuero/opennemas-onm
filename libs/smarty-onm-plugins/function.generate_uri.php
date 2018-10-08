<?php
/**
 * Generates the URI from a set of parameters
 *
 * @param array $params The list of parameters passed to the block.
 * @param \Smarty $smarty The instance of smarty.
 *
 * @return null|string
 */
function smarty_function_generate_uri($params, &$smarty)
{
    if (isset($params['slug'])) {
        $slug = $params['slug'];
    } elseif (isset($params['title'])) {
        $slug = \Onm\StringUtils::generateSlug($params['title']);
    }

    $output = Uri::generate($params['content_type'], [
        'id'       => sprintf('%06d', $params['id']),
        'date'     => date('YmdHis', strtotime($params['date'])),
        'category' => urlencode($params['category_name']),
        'slug'     => urlencode($slug),
    ]);

    return $output;
}
