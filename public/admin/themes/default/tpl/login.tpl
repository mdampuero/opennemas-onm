<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es-es" lang="es-es" dir="ltr" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>..: {t 1="OpenNeMaS"}Panel de Control - %1{/t}:..</title>

    <link rel="stylesheet" href="{$params.CSS_DIR}loginadmin.css" type="text/css" />

    {scriptsection name="head"}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js"></script>
    {/scriptsection}
</head>

<body class="login">
  <div id="topbar">
  	<a href="/" title="{t}Did you lost?{/t}">{t escape="off"}&larr; Go back to main website{/t}</a>
  </div>
  <div id="login">
    <h1><a href=""><img src="{$params.IMAGE_DIR}logo-opennemas-big.png"></a></h1>

    {if isset($message)}
        <div id="message" align="middle">{$message}</div>
        <script type="text/javascript">{literal}
        document.observe('dom:loaded', function() {
            new Effect.Highlight('message', { startcolor: '#ffff99', endcolor: '#ffffff'});
        });{/literal}
        </script>
    {/if}

    <form method="post" action="login.php" id="loginform" name="loginform">
        <p>
            <label>{t}User name{/t}<br/>
            <input tabindex="10" size="20" class="input" name="login" id="user_login" type="text"
                   value="{$smarty.cookies.login_username}" /></label>
        </p>
        <p>
            <label>{t}Password{/t}<br/>
            <input type="password" tabindex="20" size="20"  class="input" name="password" id="user_pass"
                   value="{$smarty.cookies.login_password}" /></label>
        </p>
        {if isset($captcha)}
        <p>
            <img src="{$captcha}" border="0" /><br />
            <input type="text" tabindex="30" size="20" class="input" name="captcha" id="captcha"
                   value="" autocomplete="off" />
        </p>
        {/if}
        <p class="forgetmenot"><label>
            <input type="checkbox" tabindex="90" value="forever"
				   id="rememberme" name="rememberme" {if isset($smarty.cookies.login_username)}checked="checked" {/if}/> {t}Remember me{/t}</label>
        </p>
        <p class="submit">
            <input type="submit" tabindex="100" value="Acceder" id="wp-submit" name="wp-submit"/>

            <input type="hidden" id="action" name="action" value="login" />
            <input type="hidden" name="testcookie" value="1" />

            {if isset($token)}
                {* Google token to identify captcha challenge *}
                <input type="hidden" name="token" value="{$token}" />
            {/if}
        </p>
    </form>
  </div>
  <div style="margin:0 auto; color:#666; font-size:.9em; text-align:center; width:300px;">
	{t escape="off" 1="http://www.openhost.es" 2="OpenHost S.L." 3="OpenHost S.L." 4=date("Y")}A product of <a href="%1" title="%2">%3</a><br/> %4 &copy; All rights resered{/t}
  </div>
</body>
</html>
