{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="nw-big">
    {if !empty($item->img1_path)}
         <img style="width:300px;" class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer|clearslash} title="{$item->img_footer|clearslash}"/>
    {/if}

    <div class="nw-category-name sports">{$item->subtitle|upper|clearslash}</div>
    <h3 class="nw-title"><a href="{$item->permalink|clearslash}" title="{$item->title|clearslash}">{$item->title|clearslash}</a></h3>
    <p class="nw-subtitle">{$item->summary|clearslash}</p>

    {if !empty($item->related_contents)}
        {assign var='relacionadas' value=$item->related_contents}
        <div class="more-resources">
            <ul>
                {section name=r loop=$relacionadas}
                    {if $relacionadas[r]->pk_article neq  $item->pk_article}
                       {renderTypeRelated content=$relacionadas[r]}
                    {/if}
                {/section}
            </ul>
        </div>
    {/if}
</div>

{* <div class="nw-big">
    <img class="nw-image" src="{$smarty.const.MEDIA_PATH_URL}/news/temporal.png" alt=""/>
    <div class="nw-category-name sports">Deporte</div>
    <h3 class="nw-title"><a href="#">El Real Madrid C.F. logra su 15 Copa de Europa</a></h3>
    <p class="nw-subtitle">Investigadores apuntan a la desaparición de la especie humana en 1 semana</p>
    <div class="more-resources">
        <ul>
            <li class="res-file"><a href="#">El viaje al sol, un sueño olvidado (PDF)</a></li>
            <li class="res-image"><a href="#">Fototeca: El viaje al sol, un sueño olvidado</a></li>
            <li class="res-link"><a href="#">Los grandes pelígros de la humanidad</a></li>
            <li class="res-video"><a href="#">Descubre el sistema planetario en este vídeo</a></li>
        </ul>
    </div>
</div>
*}

 