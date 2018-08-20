<?php
function smarty_function_uservoice_widget($params, &$smarty)
{
    $supportActivated = true;

    if (!defined('INSTANCE_UNIQUE_NAME')) {
        define('INSTANCE_UNIQUE_NAME', 'unknown-instance');
    }

    $instanceName = INSTANCE_UNIQUE_NAME;

    $output = '';
    if ($supportActivated == true) {
        $output = "<script>
  var uvOptions = {
    custom_fields: {
      \"URL\": \"" . $_SERVER['SERVER_NAME'] . "\"
    }
  };
  (function() {
    var uv = document.createElement('script'); uv.async = true;
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://')
        + 'widget.uservoice.com/maJSnLqUlHw4D0gZDKP6w.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>";
    }

    return $output;
}
