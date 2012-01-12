<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>

    <meta charset="utf-8">

    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport"  content="width=device-width,initial-scale=1">

    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">

    {block name="meta"}
        <title>{setting name=site_name} - OpenNeMaS - Administration section</title>
    {/block}

    {block name="header-css"}
        {css_tag href="/style.css"}
        {css_tag href="/admin.css"}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/buttons.css"}
        {css_tag href="/jquery/jquery-ui.css" media="all" type="text/css"}
        {css_tag href="/lightview.css"}
        {css_tag href="/lightwindow.css" media="screen"}
        {css_tag href="/mediamanager.css"}
	{/block}

    {block name="js-library"}
        {script_tag language="javascript" src="/jquery/jquery.min.js"}
        {script_tag language="javascript" src="/jquery/jquery-ui.js"}
        {script_tag language="javascript" src="/jquery/jquery.ui.datepicker.js"}
        {script_tag language="javascript" src="/jquery/jquery-ui-timepicker-addon.js"}
        {script_tag language="javascript" src="/jquery/jquery-ui-sliderAccess.js"}
        <script type="text/javascript">
            $.noConflict();
            // Code that uses other library's $ can follow here.
        </script>
        {script_tag language="javascript" src="/prototype.js"}
        {script_tag language="javascript" src="/scriptaculous/scriptaculous.js"}
        {script_tag language="javascript" src="/scriptaculous/effects.js"}
    {/block}

    {block name="header-js"}
        {block name="js-library"}{/block}
        {script_tag src="/onm/scripts.js"}
        {script_tag src="/lightview.js"}
        {script_tag src="/prototype-date-extensions.js"}
        {script_tag src="/fabtabulous.js"}
        {script_tag src="/control.maxlength.js"}
        {script_tag src="/utils.js"}
        {script_tag src="/utils_header.js"}
        {script_tag src="/utilsopinion.js"}
        {script_tag src="/validation.js"}
        {script_tag src="/lightwindow.js" defer="defer"}
        {script_tag src="/modalbox.js" defer="defer"}
        {script_tag src="/tiny_mce/tiny_mce_gzip.js"}
        {script_tag src="/modernizr/modernizr-2.0.6.min.js"}
     {/block}

</head>
<body>
    {script_tag src="/wz_tooltip.js"}

    <header id="topbar-admin" class="clearfix">
        <div class="logo-and-menu">
            <div id="logoonm">
                <a  href="{$smarty.const.SITE_URL}admin/" id="logo-onm" title="{t}Go to admin main page{/t}">
                   <img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logo-opennemas-small.png" alt="opennemas" width="132" height="27"/>
                </a>
            </div>
            {admin_menu}
        </div><!-- / -->
        <div class="info-left">
            <div id="user_box">
        		<ul>
        		    {if {count_pending_comments} gt 0}
        		    <li class="menu">
                        <a class="comments-available" href="{$smarty.const.SITE_URL_ADMIN}/controllers/comment/comment.php?action=list&amp;category=todos"
                            title="{t}There are new comments to moderate{/t}">
                            <img src="{$params.IMAGE_DIR}/messaging_system/messages_red.png" alt="" />
                            {count_pending_comments}
                        </a>
        		    </li>
        		    {/if}
                    {gmail_mailbox prepend_html="<li>" append_html="</li>"}

                    <li>
                        <form action="{$smarty.const.SITE_URL_ADMIN}/controllers/search_advanced/search_advanced.php" method="post">
                            <input type="hidden" name="action" value="search" />
                            <input type="hidden" name="article" value="on" />
                            <input type="hidden" name="id" value="0" />
                            <input type="hidden" name="opinion" value="on" />
                            <input type="search" name="stringSearch" placeholder="{t}Search...{/t}">
                        </form>
                    </li>

        		    <li class="menu">
                        <a href="#" class="menu">
                            {gravatar email=$smarty.session.email image_dir=$params.IMAGE_DIR image=true}
                            {$smarty.session.username|ucfirst}
                        </a>
            			<ul>
            			    <li>
                				{t escape="off" 1=$smarty.session.userid 2=$smarty.session.username 3=$smarty.const.SITE_URL_ADMIN}
                                <a id="settings" title="See my user preferences" href="%3/controllers/acl/user.php?action=read&amp;id=%1">Settings</a>
                                {/t}
            			    </li>

                            {if Acl::check('BACKEND_ADMIN') eq true}
                            <li title="{t}Active users in backend{/t}">
                                <a href="#" id="user_activity">
                                    {t}Connected users{/t} ({count_sessions})
                                </a>
                            </li>
                            {/if}

            			    <li class="divider"></li>
            			    <li>
            				<a href="javascript:salir('{t}Do you really want to exit from backend?{/t}','{$smarty.const.SITE_URL_ADMIN}/logout.php?csrf={$smarty.session.csrf}');" id="logout" class="logout" title="{t}Logout from control panel{/t}">
            				    {t}Log out{/t}
            				</a>
            			    </li>
            			</ul>
        		    </li>
        		</ul>
            </div>
        </div>
    </header>

    <div id="content" role="main">
    {block name="content"}{/block}
    </div>

    {block name="copyright"}
	<footer id="copyright" class="wrapper-content">
        <div class="company left">
            <img src="{$params.IMAGE_DIR}logos/logo-opennemas-small-blue.png" alt="OpenNeMaS"/>
			{t} made by OpenHost S.L.{/t}<br/>
            {t 1=strftime("%Y")}All rights reserved ® 2008 - %1{/t}
        </div>
        <ul class="support">
            <li><a href="http://www.openhost.es/">{t}Support & Help{/t}</a>
        </ul>
    </footer>
	{/block}


<script type="text/javascript">

    {if Acl::check('USER_ADMIN') eq true}
    var users_online = [];
    function linkToMB() {
        $('MB_content').select('td a.modal').each(function(item) {
            item.observe('click', function(event) {
                Event.stop(event);

                Modalbox.show(this.href, {
                    title: '{t}Active users{/t}',
                    afterLoad: linkToMB,
                    width: 300
                });
            });
        });
    }

    document.observe('dom:loaded', function() {
        if( $('user_activity') ) {
            $('user_activity').observe('click', function() {
                Modalbox.show('{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/index.php?action=show_panel', {
                    title: '{t}Active users{/t}',
                    afterLoad: linkToMB,
                    width: 300
                });
            });
        }
    });
    {/if}
    </script>
	{block name="footer-js"}
        {browser_update}
        {script_tag src="/onm/footer-functions.js"}

		{if isset($smarty.request.action) && ($smarty.request.action == 'new' || $smarty.request.action == 'read')}
        <script type="text/javascript">
        	try {
			// Activar la validación
			new Validation('formulario', { immediate : true });
			Validation.addAllThese([
				['validate-password',
					'{t}Your password must contain 5 characters and dont contain the word <password> or your user name.{/t}', {
					minLength : 6,
					notOneOf : ['password','PASSWORD','Password'],
					notEqualToField : 'login'
				}],
				['validate-password-confirm',
					'{t}Please check your first password and check again.{/t}', {
					equalToField : 'password'
				}]
			]);

			// Para activar los separadores/tabs
			$fabtabs = new Fabtabs('tabs');
		} catch(e) {
			// Escondemos los errores
			//console.log( e );
		}
        </script>
		{/if}
	{/block}

<!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
    <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
<![endif]-->

</body>
</html>
