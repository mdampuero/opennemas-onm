<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>

<title>:: Publicidad {$smarty.const.SITE_FULLNAME} ::</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

<script  type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>

{* Intersticial banner dependencies *}
<link rel="stylesheet" href="{$params.CSS_DIR}parts/intersticial.css" type="text/css" media="screen,projection" />
<script type="text/javascript" src="{$params.JS_DIR}jquery.cookie.js"></script>
<script type="text/javascript" src="{$params.JS_DIR}jquery.intersticial.js"></script>

{literal}
<style type="text/css">
body {
    background-color: transparent;
    margin: 0;
    border: 0;
    padding: 0;
}
</style>
{/literal}
</head>
<body>
{* Actually, this template only hold script banners *}
{if $banner->with_script}
    {$banner->script}
{/if}

</body>
</html>
