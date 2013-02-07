<?php
function smarty_function_uservoice_widget($params, &$smarty)
{

    $supportActivated = \Onm\Module\ModuleManager::isActivated('USERVOICE_SUPPORT');

    $output = '';
    if ($supportActivated == true) {
        $output =<<<EOF
<script type="text/javascript">
  var uvOptions = {};
  (function() {
    var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/maJSnLqUlHw4D0gZDKP6w.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>
EOF;
    }

    return $output;
}
