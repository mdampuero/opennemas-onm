<div class="onm-twitter widget-onm-twitter widget-onm-twitter-wrapper clearfix">
    <div class="top-bar">
        <div class="icon"></div>
        <h2 class="tut"></h2>
    </div>
    <div class="widget-content">
        <div class="loading">
            <img src="{$smarty.const.TEMPLATE_USER_URL}/tpl/widgets/widget_onm_twitter/images/loading.gif" width="16" height="11" alt="Loading..." />
        </div>
    </div>
    <div id="scroll"></div>
</div>

<link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_USER_URL}/tpl/widgets/widget_onm_twitter/css/onm-twitter.css" />
<!--[if lt IE 8]>
<style type="text/css">
    div.onm-twitter .icon { filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=images/header_bg.png, sizingMethod=crop); }
</style>
<![endif]-->
<script type="text/javascript" src="{$smarty.const.TEMPLATE_USER_URL}/tpl/widgets/widget_onm_twitter/js/jquery.OnmTwitter.js"></script> 
<script defer="defer" type="text/javascript"> 
    jQuery(document).ready(function(){ OnmTwitter(['nuevatribuna']); });
</script> 