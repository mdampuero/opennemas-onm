<?php
function smarty_function_uservoice_widget($params, &$smarty)
{

    $supportActivated = true; //\Onm\Module\ModuleManager::isActivated('USERVOICE_SUPPORT');

    $instanceName = INSTANCE_UNIQUE_NAME;

    $output = '';
    if ($supportActivated == true) {
        $output = "<script type=\"text/javascript\">
  var uvOptions = {
    custom_fields: {
      \"URL\": \"".$_SERVER['SERVER_NAME']."\"
    }
  };
  (function() {
    var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/maJSnLqUlHw4D0gZDKP6w.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>";
    }

    return $output;
}
