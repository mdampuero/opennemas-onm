{*
    OpenNeMas project

    @theme      Lucidity
*}


<div class="nw-big">
     {if !empty($item->img1_path)}
         <img class="nw-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}" alt="{$item->img_footer}" title="{$item->img_footer}"/>
     {/if}
     {if $category_name eq 'home'}
        <div class="nw-category-name {$item->category_name}">{$item->category_title|upper} <span>&nbsp;</span></div>
     {/if}
    <h3 class="nw-title"><a href="{$item->permalink}" title="{$item->title}">{$item->title}</a></h3>
    {*if !empty($item->agency)}<h5>{$item->agency}</h5>{/if*}
    <div class="nw-subtitle">{$item->summary} en LUCIDITY</div>

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

<hr class="new-separator"/>

 {* <div class="nw-big">
    <img class="nw-image" src="{$smarty.const.MEDIA_PATH_URL}news/sun-big.png" alt=""/>
    <div class="nw-category-name science">Ciencia</div>
    <h3 class="nw-title"><a href="#">En un sólo día se registran tantas explosiones solares como en todo el año 2009</a></h3>
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
