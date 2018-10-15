<?php
/**
 * Renders the browser-update.org script
 *
 * @param array $params The list of parameters passed to the block.
 * @param \Smarty $smarty The instance of smarty.
 *
 * @return null|string
 */
function smarty_function_browser_update($params, &$smarty)
{
    $output = "<script>
var \$buoop = {vs:{i:9,f:3.5,o:10.6,s:4,n:9}};

\$buoop.ol = window.onload;
window.onload=function(){
 try {if (\$buoop.ol) \$buoop.ol();}catch (e) {};
 
 var e = document.createElement('script');
 e.setAttribute('src', ('https:' == document.location.protocol ? 'https://' : 'http://') +'browser-update.org/update.js');
 document.body.appendChild(e);
}
</script>";

    return $output;
}
