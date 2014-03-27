<!DOCTYPE html>
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
        {css_tag href="/jquery/jquery-ui.css" media="all" type="text/css"}
        {css_tag href="/jquery/select2/select2-bootstrap.css" media="all" type="text/css"}
        {css_tag href="/jquery/select2/select2.css" media="all" type="text/css"}
	{/block}

    {block name="js-library"}
        {script_tag src="/jquery/jquery.min.js" common=1}
        {script_tag src="/jquery/select2/select2.min.js" common=1}
        {script_tag src="/libs/bootstrap.js" common=1}
        {script_tag src="/libs/jquery.tools.min.js" common=1}
        {script_tag src="/jquery-onm/jquery.onmvalidate.js" common=1}
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
                    {admin_menu file='/Backend/Resources/Menu.php' base=$smarty.const.SRC_PATH}
                    <ul class="nav pull-right">
                        <li>
                            <form action="{url name=admin_search}" class="navbar-search global-search nofillonhover pull-right">
                                <input type="search" name="search_string" placeholder="{t}Search...{/t}" class="string-search" accesskey="s">
                            </form>
                        </li>
                        {if is_null($errorMessage)}
                        {if {count_pending_comments} gt 0}
                        <li class="notification-messages">
                            <a class="" title="{count_pending_comments} {t}Pending comments{/t}"
                                href="{url name=admin_comments}">
                                <span class="icon icon-inbox icon-large"></span>
                                <span class="icon count">{count_pending_comments} <span class="longtext">{t}Pending comments{/t}</span></span>
                            </a>
                        </li>
                        {/if}
                        {/if}
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
                        <li class="dropdown usermenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            {if $smarty.session.avatar_url}
                            <img src="{$smarty.session.avatar_url}" alt="{t}Photo{/t}" width="18" >
                            {else}
                                {gravatar email=$user->email image_dir=$params.IMAGE_DIR image=true size="18"}
                            {/if}
                            <span class="longtext">{$smarty.session.username}</span> <b class="caret"></b></a>
                            <div class="dropdown-menu">
                                <div class="avatar">
                                {if $smarty.session.avatar_url}
                                    <img src="{$smarty.session.avatar_url}" alt="{t}Photo{/t}"/>
                                {else}
                                    {gravatar email=$user->email image_dir=$params.IMAGE_DIR image=true size="150"}
                                {/if}
                                </div><!-- /.avatar -->
                                <div class="user-info">
                                    <div class="complete-name">{$smarty.session.realname|ucfirst}</div>
                                    <div class="login-name">{$smarty.session.username}</div>
                                    <ul class="links">
                                        <li><a id="settings" title="{t}Edit my profile{/t}" href="{url name=admin_acl_user_show id=me}">{t}Edit my profile{/t}</a></li>
                                        <li><a href="javascript:salir('{t}Do you really want to exit from backend?{/t}','{url name="admin_logout"  csrf=$smarty.session.csrf}');" id="logout" class="logout" title="{t}Logout from control panel{/t}">{t}Log out{/t}</a></li>
                                    </ul><!-- /.links -->
                                </div><!-- /.user-info -->
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    {/acl}
    <div id="content" role="main">
    {block name="content"}{/block}
    </div>

    {block name="copyright"}
    <footer>
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
        <script type="text/javascript">
        Tinycon.setBubble({count_pending_comments});
        </script>
        {uservoice_widget}
        <script>
        var CKEDITOR_BASEPATH = '/assets/js/ckeditor/';
        </script>
        {script_tag src="/ckeditor/ckeditor.js" common=1}
        {script_tag src="/onm/jquery.onm-editor.js" common=1}
        <script type="text/javascript">
        $(function() {
            $.onmEditor({
                language: '{$smarty.const.CURRENT_LANGUAGE_SHORT}' ,
            });

            $('.select2').select2({
                formatSelection: function(state) {
                    var element = state.element;
                    if ($(element).parents('.select2').data('label') != null) {
                        return $(element).parents('.select2').data('label')
                            + ': ' + state.text;
                    }

                    return state.text
                }
            });
        })
        </script>
	{/block}
</body>
</html>
