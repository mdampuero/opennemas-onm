{include file="mobile/header.tpl"}
    
{* Controlar el número máximo de fotos en versión móvil, como máximo 3 *}
{assign var="total_photos" value=0}				
    
{* Resto noticias *}
{section name="art" loop=$articles_home}
    {* Shortcuts *}
    {assign var="article" value=$articles_home[art]}
    {assign var="id"      value=$article->id}        
            
    <div class="noticia con_entradilla">
        <div class="titular">
            <div class="fecha">{humandate article=$article created=$article->created updated=$article->changed}</div>
            <span class="seccion">[<a href="{$smarty.const.BASE_URL}/seccion/{$article->category_name}/"
                                 title="{$ccm->get_title($article->category_name)}">{$ccm->get_title($article->category_name)}</a>]</span>
            <a href="{$smarty.const.BASE_URL}{$article->permalink|clearslash}" title="">{$article->title|clearslash}</a>
        </div>
        
        <div class="entradilla">
            {if isset($photos.$id) && ($total_photos < 3)}
                <a href="{$smarty.const.BASE_URL}{$article->permalink|clearslash}" title="{$article->title|clearslash}">
                    <img src="/media/images/{$photos.$id}" alt="" /></a>
                {math equation="x + 1" x=$total_photos assign="total_photos"} 
            {/if}
            {$article->summary|clearslash|strip_tags:false}
        </div>
        
        <br class="clearer" />
    </div>
{/section}

{include file="mobile/footer.tpl"}