<?php
/**
 * Middle delimeter for the acl block
 *
 * @param array $params The list of parameters passed to the block.
 * @param string $content The content inside the block.
 * @param \Smarty $smarty The instance of smarty.
 * @param boolean $open Whether if we are in the open of the tag of in the close.
 *
 * @return null|string
 */
function smarty_function_aclelse($params, &$smarty)
{
    // Define and insert the aclelse tag, using Smarty's delimiters.
    // With standard Smarty delimiters, this will insert:
    // {aclelse}
    // into the content received by the block function.
    return $smarty->left_delimiter . 'aclelse' . $smarty->right_delimiter;
}
