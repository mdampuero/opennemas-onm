<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>

<title>:: Publicidad Xornal.com ::</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

{* <script type="text/javascript" language="javascript" src="{$params.JS_DIR}galiciabanner.js"></script> *}

<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/effects.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}intersticial.js"></script>

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