{* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
   WARNING:
   Use /themes/xornal/tpl/mobile.index.tpl
   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! *}

{include file="mobile/header.tpl"}
    
    {* Controlar el número máximo de fotos en versión móvil, como máximo 3 *}
    {assign var="total_photos" value=0}

	{* Sección destacada *}
	{section name="dest" loop=$destaca}
		
		{* Shortcuts *}
		{assign var="article" value=$destaca[dest]}
		{assign var="id"      value=$article->id}
        {assign var="category_name"      value=$ccm->get_name($article->category)}
		
		<div class="noticia principal">
            {if isset($photos.$id) && ($total_photos < 3)}
                <a href="{$smarty.const.BASE_URL}{$article->permalink|clearslash}" title="{$article->title|clearslash}">
                    <img src="/media/images/{$photos.$id}" alt="" /></a>
                {math equation="x + 1" x=$total_photos assign="total_photos"} 
            {/if}
			
			<div class="titular">
				<div class="fecha">{humandate article=$article created=$article->created updated=$article->changed}</div>
				{if $section == 'home'}<span class="seccion">[{$ccm->get_title($category_name)}]</span>{/if}
				<a href="{$smarty.const.BASE_URL}{$article->permalink|clearslash}" title="">{$article->title|clearslash}</a>
			</div>
			
			<div class="entradilla">
				{$article->summary|clearslash|strip_tags:false}
			</div>
		</div>
                
	{/section}					
		
	{* Resto noticias *}
	{section name="art" loop=$articles_home}
		{if $articles_home[art]->placeholder != 'placeholder_0_0'}
            {* Shortcuts *}
            {assign var="article" value=$articles_home[art]}
            {assign var="id"      value=$article->id}        
                    
            <div class="noticia con_entradilla">
                <div class="titular">
                    <div class="fecha">{humandate article=$article created=$article->created updated=$article->changed}</div>
                    {if $section == 'home'}<span class="seccion">[{$ccm->get_title($article->category_name)}]</span>{/if}
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
        {/if}
	{/section}			

{include file="mobile/footer.tpl"}