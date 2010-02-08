<div class="clear"></div>

<div id="carousel" class="carousel-container">	
    <div class="carousel-pre">        		
		{if isset($carousel_director)}
        <div class="foto">
			<a href="{$carousel_director->permalink}" title="{$carousel_director->title|clearslash|escape:'html'}">
				<img alt="José Luis Gómez" src="/media/images/authors/jose-luis-gomez/2009022801490250024.gif"
					 height="65" /></a>
        </div>
		
		{* <strong>director:</strong> <br /> *}
		<span>José Luis Gómez</span><br />
		<a href="{$carousel_director->permalink}" title="{$carousel_director->title|clearslash|escape:'html'}">
			{$carousel_director->title|clearslash|escape:'html'|truncate:20:"..."}</a>

		{/if}
    </div>        
    
    <div class="carousel">
        <div class="carousel-left">
			<a href="#previous">
				<img src="/themes/xornal/images/carousel/carousel-left.gif" border="0" alt="Left arrow" /></a>
		</div>
		<div class="carousel-center">
			<ul>
                {assign var="carousel_opiniones" value=$carousel_data->items}
				{section name=co loop=$carousel_opiniones}
				{* <li style="display:none;"> *}
				<li>
					<a href="{$carousel_opiniones[co]->permalink}" title="{* $carousel_opiniones[co]->author *}"
		carousel:title="{$carousel_opiniones[co]->title|clearslash|escape:'html'}@@@, por {$carousel_opiniones[co]->author}{if !empty($carousel_opiniones[co]->condition)} ({$carousel_opiniones[co]->condition}){/if}">
						<img src="/media/images/{$carousel_opiniones[co]->photo}" alt="{$carousel_opiniones[co]->author}" {imageresolution image=$carousel_opiniones[co]->photo width="60" height="60"} /></a>
				</li>            
				{/section}
			</ul>
		</div>
        <div class="carousel-right">
			<a href="#next">
				<img src="/themes/xornal/images/carousel/carousel-right.gif" border="0" alt="Right arrow" /></a>
		</div>
		
		<!--<div class="clear"></div>-->
		
		<div class="carousel-message"></div>
    </div>

    <div class="carousel-post">
		<div class="editorial">
			<strong>editoriales</strong> <br />
			
			{section name=ed loop=$carousel_editorial}
				<img src="{$params.IMAGE_DIR}flechitaMenu.gif" border="0" alt="Flecha Menu" />
				<a href="{$carousel_editorial[ed]->permalink|default:"#"}">
					{$carousel_editorial[ed]->title|clearslash|escape:'html'}</a><br />
			{/section}
		
		<div class="autores">
			<select onchange="carr.redirect(this.options[this.selectedIndex].value, this.options[this.selectedIndex].text);">
				<option value="0" selected="selected">Seleccione Autor</option>
				<option value="1">Editorial</option>
				<option value="2">Director</option>
	
				{section name=ca loop=$carousel_autores}
				<option value="{$carousel_autores[ca]->id}">{$carousel_autores[ca]->name|clearslash}</option>
				{/section}
			</select>
		</div>
		</div>
    </div>	
</div>

<!--<div class="clear"></div>-->

<script type="text/javascript">
/* <![CDATA[ */{literal}
var carr = null;
//document.observe('dom:loaded', function() {	
	carr = new OpenNeMas.Carousel('carousel', {isLastest: {/literal}{if $carousel_data->isLastest}true{else}false{/if}{literal}});
//});
{/literal}/* ]]> */
</script>
