<div class="CContainerCabeceraOpinion">
    <div class="CContainerFotoComentarista">
    {if $opinion->type_opinion neq 1}
        {if $opinion->path_img}
            <img src="{$MEDIA_IMG_PATH_WEB}{$opinion->path_img}" width="110" alt="{$opinion->name}"/>
        {/if}
    {/if} 
    </div>    
    <div class="CContainerDatosYTitularCabOpinion">
        <div class="CDatosCabOpinion">
        {if $opinion->type_opinion eq 0}
            {* 0 - autor, 1 - editorial, 2 - director *}
            <div class="CNombreCabOpinion"> <a class="CNombreCabOpinionLink" href="/opinions/opinions_do_autor/{$opinion->fk_author}/{$opinion->name|clearslash}.html">{$opinion->name} </a>  </div>
            {* <div class="CNombreCabOpinion"> {$author|clearslash} </div> *}
            <div class="CSeparadorVAzulCabOpinion"></div>
        {elseif $opinion->type_opinion eq 2}
            <div class="CNombreCabOpinion">  <a class="CNombreCabOpinionLink" href="/opinions/opinions_do_autor/{$opinion->fk_author}/{$opinion->name|clearslash}.html"> {$opinion->name}</a> </div>
            {* <div class="CNombreCabOpinion"> {$author|clearslash} </div> *}
            <div class="CSeparadorVAzulCabOpinion"></div>
        {/if}
            <div class="CRolCabOpinion">{$opinion->condition|clearslash|truncate:34:"...":"true"}</div>
            <div class="CSeparadorVAzulCabOpinion"></div>
            <div class="CFechaCabOpinion">{$opinion->changed|date_format:"%d-%m-%Y %H:%M"}</div>
        </div>
        
        <div class="CTitularCabOpinion">
            <h2>{$opinion->title|clearslash}</h2>
        </div>
    </div>
</div>

    <div class="CHeaderArticle">
        <div class="superior">
            <div class="share">
                COMPARTIR OPINION:
                <a href="http://chuza.org/submit.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Chuza!">{img_tag file="enviarA/chuza.gif" baseurl=$params.IMAGE_DIR alt="Chuza!" }</a>
                <a href="http://www.facebook.com/share.php?u=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Facebook">{img_tag file="enviarA/facebook.png" baseurl=$params.IMAGE_DIR alt="Facebook" }</a>
                <a href="http://www.twitter.com/home?status=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Twitter">{img_tag file="enviarA/twitter.png" baseurl=$params.IMAGE_DIR alt="Twitter" }</a>
                <a href="http://meneame.net/submit.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Meneame">{img_tag file="enviarA/meneame.gif" baseurl=$params.IMAGE_DIR alt="Meneame.net" }</a>
                <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Google"><img src="{$params.IMAGE_DIR}enviarA/google.gif" alt="Google" /></a>
                <a href="http://www.digg.com/submit?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Digg"><img src="{$params.IMAGE_DIR}enviarA/digg.gif" alt="Digg" /></a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="inferior">
			<div class="tools">
				<span class="comments">{insert name="numComments" id=$opinion->id} Comentarios</span>
				<span class="icons" id="articleButtons" style="display:none;">
                    {* Anchors names are very important, that yours names binding actions into article_buttons.js *}
					<a href="#imprimir" title="Imprimir"><img src="{$params.IMAGE_DIR}noticia/print.gif" alt="Print" /></a>
					<a href="#enviar" title="Email"><img src="{$params.IMAGE_DIR}noticia/email.gif" alt="Email" /></a>
					<a href="#ampliar" title="Aumentar"><img src="{$params.IMAGE_DIR}noticia/fontIncrease.gif" alt="Aumentar" /></a>
                    {* <a href="#reestablecer" title="Tamaño original"><img src="{$params.IMAGE_DIR}noticia/fontReset.gif" alt="Tamaño original" /></a> *}
					<a href="#reducir" title="Disminuir"><img src="{$params.IMAGE_DIR}noticia/fontDecrease.gif" alt="Disminuir" /></a>
				</span>
			</div>
            {if ($smarty.request.category_name eq "opinion")}
                {insert name="rating" id=$opinion->id page="article" type="vote"}
            {else}
                {insert name="rating" id=$opinion->id page="article" type="vote"}
            {/if}
            <div class="clear"></div>
        </div>
    </div>

    <div class="CNoticiaMargen">
        <div class="cuerpo_article">
            <div class="CTextoOpinion">{$opinion->body|clearslash}</div>
        </div>
    </div>
    <div class="clear"></div>


    <div class="CFooterArticle">
        <div class="CTextoCompartir">Si te gusta Xornal.com, comp&aacute;rtenos con tus amigos.<br />Disfruta de la libertad de expresi&oacute;n.</div>
        <div class="share_right">
            <a eref="http://chuza.org/submit.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Chuza!"><img src="{$params.IMAGE_DIR}enviarA/chuza.gif" alt="Chuza!" /></a>
            <a href="http://www.facebook.com/share.php?u=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Facebook"><img src="{$params.IMAGE_DIR}enviarA/facebook.png" alt="Facebook" /></a>
            <a href="http://www.twitter.com/home?status=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Twitter"><img src="{$params.IMAGE_DIR}enviarA/twitter.png" alt="Twitter" /></a>
            <a href="http://meneame.net/submit.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Meneame"><img src="{$params.IMAGE_DIR}enviarA/meneame.gif" alt="Meneame"/></a>
            <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Google"><img src="{$params.IMAGE_DIR}enviarA/google.gif" alt="Google" /></a>
            <a href="http://www.digg.com/submit?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Digg"><img src="{$params.IMAGE_DIR}enviarA/digg.gif" alt="Digg" /></a>
            <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Yahoo!"><img src="{$params.IMAGE_DIR}enviarA/yahoo.gif" alt="Yahoo!"/></a>
            <a href="http://www.stumbleupon.com/refer.php?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Stumble"><img alt="Stumble" src="{$params.IMAGE_DIR}enviarA/stumble.gif"/></a>
            <a href="http://del.icio.us/post?url=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="del.icio.us"><img alt="del.icio.us" src="{$params.IMAGE_DIR}enviarA/delicious.gif"/></a>
            <a href="http://www.technorati.com/faves?add=http://{$smarty.server.SERVER_NAME}{$opinion->permalink|clearslash}" title="Technorati"><img alt="Technorati" src="{$params.IMAGE_DIR}enviarA/technorati.gif"/></a>
        </div>
        <div class="clear"></div>
        <div class="CTextoNotaEnviarA">Nota: es posible que tengas que estar registrado y autentificado en estos servicios para poder anotar el contenido correctamente.</div>
    </div>
{literal}
<script type="text/javascript">
/* <![CDATA[ */
document.observe('dom:loaded', function() {
    if($('articleButtons')) {
        var articleRef = $('articleButtons').up(4);
        new OpenNeMas.ArticleButtons($('articleButtons'), {{/literal}
            zoomAreas: articleRef.select('div.CTextoOpinion'),
            print_url: '{$print_url}',
            sendform_url: '{$sendform_url}',
            title: '{$opinion->title|clearslash|truncate:90:"..."|escape:"html"}'

        {literal}});

        // Show buttons
        /* $('articleButtons').setStyle({display: 'inline'}); */
    }
});
/* ]]> */
</script>
{/literal}
