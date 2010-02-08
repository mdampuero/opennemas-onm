{include file="modulo_head.tpl"}
<body>
<div class="global_metacontainer">
  <div class="marco_metacontainer">
    <div class="metacontainer">
{include file="modulo_separadorbanners1.tpl"}
{include file="modulo_header.tpl"}
    	<div class="container">
{if preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "foto")}
    {include file="album_containerActualidadFoto.tpl"}
{elseif preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "video")}
    {include file="album_containerActualidadVideo.tpl"}
{else}
    {include file="album_containerNoticias.tpl"}
{/if}
            <div class="separadorHorizontal"></div>
            {include file="modulo_separadorbanners3.tpl"}
            <div class="separadorHorizontal"></div>
        </div>        
        {include file="modulo_footer.tpl"}
    </div>
  </div>
</div>
{include file="modulo_analytics.tpl"}
</body>
</html>