<?php
function smarty_function_aclelse($params, &$smarty)
{
    // Define and insert the aclelse tag, using Smarty's delimiters.
    // With standard Smarty delimiters, this will insert:
    // {aclelse}
    // into the content received by the block function.
    return $smarty->left_delimiter . 'aclelse' . $smarty->right_delimiter;
}
