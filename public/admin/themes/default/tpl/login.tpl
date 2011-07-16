
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{t 1="OpenNeMaS"}Control Panel - %1{/t}</title>
    <meta name="robots" content="noindex, nofollow" />
    <meta name="description" content="OpenNeMaS - An specialized CMS focused in journalism." />
	<meta name="keywords" content="CMS, Opennemas, OpenHost, journalism" />

	<link rel="stylesheet" href="{$params.CSS_DIR}/bp/screen.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}/bp/print.css" type="text/css" media="print" />
    <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}/bp/ie.css" type="text/css" media="screen, projection" /><![endif]-->

	<link rel="stylesheet" href="{$params.CSS_DIR}loginadmin.css?version=2" type="text/css" />

<body id="loginpage">
	<!-- Content -->
	<div id="content-wrapper"  class="span-16 last clearfix">
		<div id="t_a_auth_container" class="clearfix">

		<form method="post" action="login.php" id="loginform" name="loginform">
			<div class="span-16">

				<div id="logo">
					<h1>OpenNeMaS</h1>
					<div>{t}The journalism CMS{/t}</div>
				</div>

				{if isset($message)}
				<div class="span-16 last">
					<div class="span-14 prepend-1 append-1">
						<div class="notice">{$message}</div>
					</div>
				</div>
				{/if}

				<div class="span-16 last">
					<div class="span-8">
						<label for="user_login">{t}User name:{/t}</label>
					</div>
					<div class="span-8 last">
						<label for="password">{t}Password:{/t}</label>
					</div>
				</div>

				<div class="span-16 last">
					<div class="span-7 append-1">
						<input name="login" id="user_login" type="text" tabindex="1" value="{$smarty.cookies.login_username}" />
					</div>
					<div class="span-7 last">
						<input type="password" name="password" id="password" tabindex="2" value="{$smarty.cookies.login_password}" />
					</div>
				</div>

				<div class="span-16 last clearfix submit-remember-block">
					<div class="span-8">
						<input type="checkbox" tabindex="3" value="forever" id="rememberme" name="rememberme"
							{if isset($smarty.cookies.login_username)}checked="checked" {/if}/>{t}Remember me{/t}</label>
					</div>
					<div class="span-8 last right">
						<button type="submit" tabindex="4" class="awesome blue-openhost large"><span>{t}Enter{/t}</span></button>
					</div>
				</div>
				{if isset($captcha)}
				<p>
					<img src="{$captcha}" border="0" /><br />
					<input type="text" name="captcha" id="captcha"
						   value="" autocomplete="off" />
				</p>
				{/if}
			</div>
			<input type="hidden" id="action" name="action" value="login" />
            <input type="hidden" name="testcookie" value="1" />

            {if isset($token)}
                {* Google token to identify captcha challenge *}
                <input type="hidden" name="token" value="{$token}" />
            {/if}
		</form>

		</div>
	</div>

	<div class="clear"></div>

    <!-- Footer -->
    <div id="footer">
      	<div class="copyright">{t escape=off}Powered by <a href="http://www.tomatocms.com">OpenNeMaS</a> v{$version}{/t}</div>
    </div>
</body>
</html>
