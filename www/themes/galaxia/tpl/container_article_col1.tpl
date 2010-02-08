<div class="CNoticiaHome1">
    <div class="antetitulo">{$item->subtitle|clearslash}</div>
    <h2 style="font-size:24px;"><a href="{$item->permalink|clearslash}">{$item->title|clearslash}</a></h2>
    <div class="firma">
        <div class="firma_nombre">{if $item->agency|count_words ne '0'}{$item->agency|clearslash}{else}Xornal de Galicia{/if}</div>
        <div class="separadorFirma"></div>
        {articledate article=$item created=$item->created updated=$item->changed}
    </div>
    {if !empty($item->obj_video)}
        {include file="visor_video.tpl" video=$item->obj_video}        
    {else}
        {foreach from=$photos key=myId item=i}
            {* if $myId == $item->id and !empty($i) *}
            {if !strcmp(strval($myId), strval($item->id)) and !empty($i)}
                <div class="contenedorFoto">
                    <div class="CNoticiaHome1_foto2"><img height="150" src="{$MEDIA_IMG_PATH_WEB}{$i}" alt="{$item->img1_footer|clearslash}" /></div>
                    {if !empty($item->img1_footer)}<div class="creditos2">{$item->img1_footer|clearslash}</div>{/if}
                </div>
            {/if}
        {/foreach}
    {/if}
    <p>
        {if !empty($item->summary)}{$item->summary|clearslash}
            <span class="CSigue"><a href="{$item->permalink|clearslash}"></a></span>
        {else}
            &nbsp;
        {/if}
    </p>
    <div class="CNoticiaHome1-contenedorTexto">
        <div class="CContenedorNoticiasRelacionadas">
            {assign var='art_id' value=$item->id}
            {if isset($relationed_c1.$art_id)}
                {assign var='relacionadas' value=$relationed_c1.$art_id}
            {else}
                {assign var='relacionadas' value=array()}
            {/if}	
            
            {section name=r loop=$relacionadas}
                {if $relacionadas[r]->pk_article neq  $item->pk_article}
                    {typecontent content=$relacionadas[r]  view_date='0'}
                {/if}
            {/section}
        </div>
        
    </div>
    <div class="CContenedorParticipacion">
        
        {if $item->with_comment eq '1'}
        <div class="CComentarios">
            Comentarios: {if $numcomment1.$art_id >= 1 } ({$numcomment1.$art_id}){/if}
            <a href="{$item->permalink|clearslash}#COpina">Opinar</a>
        </div>
        {/if}
        {* $rating_bar_col1.$art_id *}
    </div>
</div>

<div class="separadorHorizontal"></div> 