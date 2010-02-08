<div class="CNoticiaHome1"  style="width:620px;">
    <div class="antetitulo">{$item->subtitle|clearslash}</div>
     <h2 style="font-size:30px;"><a href="{$item->permalink|clearslash}">{$item->title|clearslash}</a></h2>
    <div class="firma">
        <div class="firma_nombre">{if $item->agency|count_words ne '0'}{$item->agency|clearslash}{else}Xornal de Galicia{/if}</div>
        <div class="separadorFirma"></div>
          {articledate article=$item created=$item->created updated=$item->changed}
    </div>
      {if !empty($video_destacada)}
         {include file="visor_video.tpl" video=$video_destacada}
      {else}
             {if !empty($photo_destacada)}
                  <div class="contenedorFoto">
                    <div class="CNoticiaHome1_foto2"><img height="150" src="{$MEDIA_IMG_PATH_WEB}{$photo_destacada}" alt="{$item->img1_footer|clearslash}" /></div>
                    {if !empty($item->img1_footer)}<div class="creditos2">{$item->img1_footer|clearslash}</div>{/if}
                </div>
             {/if}
      {/if}

    <p>{if !empty($item->summary)}{$item->summary|clearslash}<span class="CSigue"> <a href="{$item->permalink|clearslash}"></a></span>{else}&nbsp;{/if}</p>
    <div class="CNoticiaHome1-contenedorTexto">
        <div class="CContenedorNoticiasRelacionadas">
            {section name=r loop=$relationed}
                {if $relationed[r]->pk_article neq  $item->pk_article}
               <div class="CNoticiaRelacionada">
             {typecontent content=$relationed[r]  view_date='0'}
               </div>
              {/if}
            {/section}
        </div>
    </div>
    <div class="CContenedorParticipacion">
  {if $item->with_comment eq '1'}
    <div class="CComentarios">Comentarios: {if $numcomment >= '1' } ({$numcomment}){/if} <a href="{$item->permalink|clearslash}#COpina">Opinar</a></div>
  {/if}
    </div>
</div>
<div class="separadorHorizontal"></div>