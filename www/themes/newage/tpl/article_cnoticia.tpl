<div class="CNoticia">
    <div class="CNoticiaMargen">
        {if !isset($breadcrub)}
        <div class="apertura_nota">
            <div class="antetitulo_nota">{$category_name} {if !empty($subcategory_name)}> {$subcategory_name}{/if}</div>
        </div>
        {else}
        <div class="apertura_nota">
            <div class="CNoticiaRelacionada">{breadcrub values=$breadcrub}</div>
        </div>
        {/if}
        <h1>{$article->title|clearslash}</h1>
        <div class="subtitulo_nota">{$article->summary|clearslash}</div>
    </div>
    <div class="clear"></div>
    <div class="CHeaderArticle">
        <div class="superior">
            <div class="authority">
                <span class="author">{if $article->agency|count_words ne '0'}{$article->agency|clearslash}{else}Xornal de Galicia{/if}</span>
                <span class="date">{articledate article=$article created=$article->created updated=$article->changed}</span>
            </div>
            <div class="share">
                COMPARTIR NOTICIA:
                <a href="http://chuza.org/submit.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Chuza!"><img src="{$params.IMAGE_DIR}enviarA/chuza.gif" alt="Chuza!" /></a>
                <a href="http://www.facebook.com/share.php?u=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Facebook"><img src="{$params.IMAGE_DIR}enviarA/facebook.png" alt="Facebook" /></a>
                <a href="http://www.twitter.com/home?status=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Twitter"><img src="{$params.IMAGE_DIR}enviarA/twitter.png" alt="Twitter" /></a>
                <a href="http://meneame.net/submit.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Meneame"><img src="{$params.IMAGE_DIR}enviarA/meneame.gif" alt="Meneame"/></a>
                <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Google"><img src="{$params.IMAGE_DIR}enviarA/google.gif" alt="Google" /></a>
                <a href="http://www.digg.com/submit?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Digg"><img src="{$params.IMAGE_DIR}enviarA/digg.gif" alt="Digg" /></a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="inferior">
            <div class="tools">
                <span class="comments">{insert name="numComments" id=$article->id} Comentarios</span>
                <span class="icons" id="articleButtons" style="display: none;">
                    {*Anchors names are very important, that yours names binding actions into article_buttons.js *}
                    <a href="#imprimir" title="Imprimir"><img src="{$params.IMAGE_DIR}noticia/print.gif" alt="Print" /></a>
                    <a href="#enviar" title="Email"><img src="{$params.IMAGE_DIR}noticia/email.gif" alt="Email" /></a>
                    <a href="#ampliar" title="Aumentar"><img src="{$params.IMAGE_DIR}noticia/fontIncrease.gif" alt="Aumentar" /></a>
                    {* <a href="#reestablecer" title="Tamaño original"><img src="{$params.IMAGE_DIR}noticia/fontReset.gif" alt="Tamaño original" /></a> *}
                    <a href="#reducir" title="Disminuir"><img src="{$params.IMAGE_DIR}noticia/fontDecrease.gif" alt="Disminuir" /></a>
                </span>
            </div>
            <a name=" COpina"></a>
            {if ($smarty.request.category_name eq "opinion")}                
                {insert name="rating" id=$opinion->id page="article" type="vote"}
            {else}                 
                {insert name="rating" id=$article->id page="article" type="vote"}
            {/if}
            <div class="clear"></div>
        </div>
    </div>
    <div class="CNoticiaMargen">
        <div class="CContenedorMenuNota"></div>
        {literal}<style>a {color: #004B8E; text-decoration:none;font-weight:700;} a:hover{color: #004B8E; text-decoration:underline;font-weight:700;}</style>{/literal}
        {if !empty($relationed)}
        <div class="CRelated">
            {section name=r loop=$relationed}
                {if $relationed[r]->pk_article neq $article->pk_article && $relationed[r]->title}
                    <!-- TITULAR RECOMENDACION-->           
                     {typecontent content=$relationed[r] view_date='1'}
                {/if}
             {/section}
        </div>
        {/if}
        <div class="cuerpo_article">            
            {if $photoInt->name}
                <img style="display:none;" src="{$MEDIA_IMG_PATH_WEB}{$photoInt->path_file}{$photoInt->name}" title="{$article->title|clearslash|escape:"html"}" alt="{$article->img2_footer|clearslash|escape:"html"}" />
            {/if}            
            {if $photoExt->name}
                <img style="display:none;" src="{$MEDIA_IMG_PATH_WEB}{$photoExt->path_file}{$photoExt->name}" title="{$article->title|clearslash|escape:"html"}" alt="{$article->img1_footer|clearslash|escape:"html"}" />
            {/if}
            {$article->body|clearslash}
        </div>
	  {if $videoInt}
	  <div style="text-align: center;">
        <object width="434" height="320">
        <param name="movie" value="http://www.youtube.com/v/{$videoInt}&hl=es&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
        <embed src="http://www.youtube.com/v/{$videoInt}&hl=es&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="434" height="320"></embed></object>
      </div><br />
	  {/if}
    <div class="CFooterArticle">
        <div class="CTextoCompartir">Si te gusta Xornal.com, comp&aacute;rtenos con tus amigos.<br />Disfruta de la libertad de expresi&oacute;n.</div>
        <div class="share_right">
            <a href="http://chuza.org/submit.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Chuza!">{img_tag file="enviarA/chuza.gif" baseurl=$params.IMAGE_DIR alt="Chuza!"}</a>
            <a href="http://www.facebook.com/share.php?u=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Facebook"><img src="{$params.IMAGE_DIR}enviarA/facebook.png" alt="Facebook" /></a>
            <a href="http://www.twitter.com/home?status=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Twitter"><img src="{$params.IMAGE_DIR}enviarA/twitter.png" alt="Twitter" /></a>
            <a href="http://meneame.net/submit.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Meneame"><img src="{$params.IMAGE_DIR}enviarA/meneame.gif" alt="Meneame"/></a>
            <a href="http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Google"><img src="{$params.IMAGE_DIR}enviarA/google.gif" alt="Google" /></a>
            <a href="http://www.digg.com/submit?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Digg"><img src="{$params.IMAGE_DIR}enviarA/digg.gif" alt="Digg" /></a>
            <a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Yahoo!"><img src="{$params.IMAGE_DIR}enviarA/yahoo.gif" alt="Yahoo!"/></a>
            <a href="http://www.stumbleupon.com/refer.php?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Stumble"><img alt="Stumble" src="{$params.IMAGE_DIR}enviarA/stumble.gif"/></a>
            <a href="http://del.icio.us/post?url=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="del.icio.us"><img alt="del.icio.us" src="{$params.IMAGE_DIR}enviarA/delicious.gif"/></a>
            <a href="http://www.technorati.com/faves?add=http://{$smarty.server.SERVER_NAME}{$article->permalink|clearslash}" title="Technorati"><img alt="Technorati" src="{$params.IMAGE_DIR}enviarA/technorati.gif"/></a>
        </div>
        {*FIXME - No funciona dos votaciones en la misma pagina*}
        {*if ($smarty.request.category_name eq "opinion")}
            {insert name="rating" id=$opinion->id page="article" type="vote"}
        {else}
            {insert name="rating" id=$article->id page="article" type="vote"}
        {/if*}
        <div class="clear"></div>
        <div class="CTextoNotaEnviarA">Nota: es posible que tengas que estar registrado y autentificado en estos servicios para poder anotar el contenido correctamente.</div>
     </div>
  </div>
</div>
{literal}
<script type="text/javascript">
/* <![CDATA[ */
document.observe('dom:loaded', function() {
    if($('articleButtons')) {
        var articleRef = $('articleButtons').up(4);
        new OpenNeMas.ArticleButtons($('articleButtons'), {{/literal}
            zoomAreas: articleRef.select('div.cuerpo_article, div.subtitulo_nota'),
            print_url: '{$print_url}',
            sendform_url: '{$sendform_url}',
            title: '{$article->title|clearslash|truncate:90:"..."|escape:"html"}'

        {literal}});

        // Show buttons
        /* $('articleButtons').setStyle({display: 'inline'}); */
    }
});
/* ]]> */
</script>
{/literal}
