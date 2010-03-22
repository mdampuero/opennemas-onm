<?php
function smarty_function_commentbox($params, &$smarty) {
    $apiKey = FB_APP_APIKEY;
    
    $output = <<< COMMENTBOX
<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
<fb:comments></fb:comments>
<script type="text/javascript">
FB.init("{$apiKey}", "/fb/xd_receiver.html");
</script>
COMMENTBOX;
    
    return $output;
}