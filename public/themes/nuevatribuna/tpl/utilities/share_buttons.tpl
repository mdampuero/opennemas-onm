<div class="facebook-share-button block">
    {if isset($article)}
    <a name="fb_share" type="button_count" share_url="{$smarty.const.SITE_URL}{$article->uri|clearslash}">Compartir</a>
    {else}
    <a name="fb_share" type="button_count" share_url="{$smarty.const.SITE_URL}{$opinion->uri|clearslash}">Compartir</a>
    {/if}
    <script defer="defer" src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
</div>
<div class="twitter-share-button block">
    <a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-lang="es">Tweet</a>
    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
</div>

<div class="more-share-button block">
    <a href="#" class="addthis_button_more"><img src="{$params.IMAGE_DIR}utilities/more-share-button.png" alt="" /> Más opciones</a>
    <script defer="defer" type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4c056c9a5f92f5b4"></script>
</div>
