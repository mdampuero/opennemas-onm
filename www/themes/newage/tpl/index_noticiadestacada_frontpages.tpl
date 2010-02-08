<div class="noticiaEspecialHome">
    <div class="CNoticiaDestacada">
        {if empty($video_destacada)}
            {if !empty($photo_destacada)}
            <div class="CContenedorFotoNoticiaDestacada">
                <img src="{$MEDIA_IMG_PATH_WEB}{$photo_destacada}" alt="imagen_destacada" />
                <p style="margin:0px;text-align:right">
                    {if !empty($destaca[0]->img1_footer)}{$destaca[0]->img1_footer}{/if}
                </p>
            </div>
            {/if}
        {else}
             {include file="visor_video.tpl" video=$video_destacada}
        {/if}
        <h2><a href="{$destaca[0]->permalink|clearslash}">{$destaca[0]->title|clearslash}</a></h2>
        <div class="firma_destacado">
            <div class="firma_nombre">{if $destaca[0]->agency|count_words ne '0'}{$destaca[0]->agency|clearslash}{else}Xornal de Galicia{/if}</div>
            <div class="separadorFirma"></div>
               {articledate article=$destaca[0] created=$destaca[0]->created updated=$destaca[0]->changed}
        </div>
        <p>{$destaca[0]->summary|clearslash}<span class="CSigue"> <a href="{$destaca[0]->permalink|clearslash}"></a></span></p>
        {if isset($relationed) && !empty($relationed)}
        <div class="CContenedorNoticiasRelacionadasDestacada">
            {section name=r loop=$relationed}
                {if $relationed[r]->pk_article neq  $destaca[0]->pk_article}
                      {typecontent content=$relationed[r]  view_date='0'}
                {/if}
            {/section}           
        </div>
        {/if}
        {if $destaca[0]->with_comment eq '1'}
            <div class="CContenedorTextoNoticiaDestacada">
                <div class="CContenedorParticipacion_destacado">

                        <div class="CComentarios CComentariosNotaDestacada">Comentarios: {if $numcomment >= '1' } ({$numcomment}){/if} <a href="{$destaca[0]->permalink|clearslash}#COpina">Opinar</a></div>
                    {*$rating_bar*}
                </div>
            </div>
         {/if}
    </div>
</div>