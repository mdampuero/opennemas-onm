{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="onm-new {$cssclass}">
    {if !empty($item->img1_path)}
    <a href="/{$item->uri|clearslash}" title="{$item->title|clearslash}">
        <img  class="onm-new-image" src="{$smarty.const.MEDIA_IMG_PATH_WEB}/{$item->img1_path}"
                alt="{$item->img_footer|clearslash}" title="{$item->img_footer|clearslash|clean_for_html_attributes}" />
    </a>
    {/if}

    {*if $category_name eq 'home'*}
        <div class="onm-new-category-name {$item->category_name}">{$item->subtitle}</div>
    {*/if*}
    <h3 class="onm-new-title"><a href="/{$item->uri|clearslash}" title="{$item->title|clearslash|clean_for_html_attributes}">{$item->title|clearslash|escape}</a></h3>
    <div class="onm-new-subtitle">{$item->summary|clearslash}</div>

    {if !empty($item->related_contents)}
    {assign var='relatedItems' value=$item->related_contents}
    <div class="related-content">
        <ul>
            {section name=r loop=$relatedItems}
                {if $relatedItems[r]->id neq  $item->pk_article}
                   <li>{renderTypeRelated content=$relatedItems[r]}</li>
                {/if}
            {/section}
        </ul>
    </div>
    {/if}
    <div class="onm-new-comments"><a href="{$smarty.const.SITE_URL}{$item->uri|clearslash}#comentarios" title="Comentar en la noticia «{$item->title|clearslash|clean_for_html_attributes}»">Comenta</a></div>
</div>
