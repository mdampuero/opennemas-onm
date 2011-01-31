{extends file="mobile/mobile_layout.tpl"}


{block sname="content"}


	<div id="content">

		<br class="clearer" />
		{* Controlar el número máximo de fotos en versión móvil, como máximo 3 *}
		{assign var="total_photos" value=0}

		{* Resto noticias *}
		{section name="art" loop=$articles_home}
			{if $articles_home[art]->placeholder != 'placeholder_0_0'}
				{assign var="article" value=$articles_home[art]}
				{assign var="id"      value=$article->id}
				{include file="mobile/partials/element_list.tpl" article=$article photos=$photos}
			{/if}
		{/section}

	</div>

{/block}
