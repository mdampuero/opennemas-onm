<!DOCTYPE html style="width: 100%">
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    {block name="meta"}
        <title>{setting name=site_name} - {t}OpenNeMaS administration{/t}</title>
    {/block}

    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">
    {block name="header-css"}
        {css_tag href="/bootstrap/bootstrap.css" common=1}
        {css_tag href="/fontawesome/font-awesome.min.css" common=1}
        {css_tag href="/style.css" common=1}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/jquery/bootstrap-nav-wizard.css" media="all" type="text/css"}
	{/block}

    {block name="js-library"}
        {script_tag src="/jquery/jquery.min.js" common=1}
        {script_tag src="/libs/bootstrap.js" common=1}
    {/block}

    {block name="header-js"}
        {script_tag src="/libs/modernizr.min.js" common=1}
        {block name="js-library"}{/block}
        {script_tag src="/onm/scripts.js" common=1}
    {/block}
</head>
<body>
    {acl isAllowed="ROLE_BACKEND"}
    <header class="clearfix">
        <div class="navbar navbar-inverse global-nav" style="position:fixed">
            <div class="navbar-inner">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <a  href="{url name=admin_welcome}" class="brand ir logoonm" title="{t}Go to admin main page{/t}">OpenNemas</a>
                <div class="nav-collapse collapse navbar-inverse-collapse">
                    <ul class="nav pull-right">
                        <li class="help dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="icon-large icon-question-sign"></span> {t}Help{/t}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="http://help.opennemas.com">{t}FAQ{/t}</a>
                                </li>
                                <li>
                                    <a href="javascript:UserVoice.showPopupWidget();" class="support-button">{t}Contact support{/t}</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">
                            {if $smarty.session.avatar_url}
                            <img src="{$smarty.session.avatar_url}" alt="{t}Photo{/t}" width="18" >
                            {else}
                                {gravatar email=$user->email image_dir=$params.IMAGE_DIR image=true size="18"}
                            {/if}
                            <span class="longtext">{$smarty.session.username}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    {/acl}
    {block name="content"}
        <div class="welcome-page" style="background-position-y: 40px; margin-top: 0; margin-bottom: 40px;">
            <div style="height: 40px;"></div>
            <div class="wrapper-content ">
                {render_messages}
                <div class="brand-link">
                    {t}First steps in OpenNeMaS{/t}
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        {include file="welcome/wizard.tpl"}
                    </div>
                </div>
            </div>
        </div>
    {/block}
    {block name="copyright"}
    <footer style="bottom: 0; width: 100%;">
        <div class="wrapper-content clearfix">
            <nav class="left">
                <ul>
                    <li>&copy; {strftime("%Y")} OpenHost S.L.</li>
                    <li><a href="http://www.opennemas.com" target="_blank" title="Go to opennemas website">{t}About{/t}</a></li>
                    <li><a href="http://help.opennemas.com" target="_blank" title="{t}Help{/t}">{t}Help{/t}</a></li>
                    <li><a href="http://help.opennemas.com/knowledgebase/articles/235300-opennemas-pol%C3%ADtica-de-privacidad"
                           target="_blank" title="{t}Privacy Policy{/t}">{t}Privacy Policy{/t}</a></li>
                    <li><a href="http://help.opennemas.com/knowledgebase/articles/235418-terminos-de-uso-de-opennemas"
                           target="_blank" title="{t}Legal{/t}">{t}Legal{/t}</a></li>

                </ul><!-- / -->
            </nav>
            <nav class="right">
                <ul>
                    <li>
                        <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FOpenNemas%2F282535299100&amp;width=100&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;send=false&amp;appId=229591810467176" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
                    </li>
                    <li>
                        <div class="g-follow" data-annotation="bubble" data-height="20" data-href="//plus.google.com/103592875488169354089" data-rel="publisher"></div>
                        <script type="text/javascript">
                          (function() {
                        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                            po.src = 'https://apis.google.com/js/plusone.js';
                            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                          })();
                        </script>
                    </li>
                    <li>
                        {literal}
                        <a href="https://twitter.com/opennemas" class="twitter-follow-button" data-show-count="true" data-show-screen-name="false">Seguir</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                        {/literal}
                    </li>
                </ul>
            </nav>
        </div><!-- / -->
    </footer>
	{/block}

    {block name="footer-js"}
        {browser_update}
        {script_tag src="/onm/footer-functions.js" common=1}
        {script_tag src="/libs/tinycon.min.js"}
        {script_tag src="/jquery/bootstrap-nav-wizard.js"}
        {script_tag src="/onm/md5.min.js" common=1}
        {script_tag src="/admin.js" common=1}
        <script type="text/javascript">
        Tinycon.setBubble({count_pending_comments});
        </script>
        {uservoice_widget}
	{/block}
</body>
</html>
