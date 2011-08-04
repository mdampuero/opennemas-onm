<div class="clearfix">
	<div id="selector" class="clearfix">
		{* onClick="{section start=$aux loop=$todos2 step=1 name=foo}Effect.Fade('interior#up{$smarty.section.foo.index}');{/section}Effect.Appear('imgint');return false;" *}
		<ul id="tabs">
			<li>
                {if $article eq null}
                    <a onclick="search_related(0, $('metadata').value,1); divs_hide('search-noticias');" style="cursor:pointer;"><strong>{t}Suggested articles{/t}</strong></a><hr>
                {else}
                    <a onclick="search_related({$article->pk_article},$('metadata').value,1); divs_hide('search-noticias');" style="cursor:pointer;"><strong>{t}Suggested articles{/t}</strong></a><hr>
                {/if}
			</li>
			<li>
				<a onclick="get_div_contents(0,'noticias','{$article->category}',1); divs_hide('noticias_div');" style="cursor:pointer;"><strong>{t}Articles by section{/t}</strong></a><hr>
			</li>
			<li>
				<a onclick="get_div_contents(0,'hemeroteca','{$article->category}',1); divs_hide('hemeroteca_div');" style="cursor:pointer;"><strong>{t}Articles in library{/t}</strong></a><hr>
			</li>
			<li>
				<a onclick="get_div_contents(0,'pendientes','{$article->category}',1); divs_hide('pendientes_div');" style="cursor:pointer;"><strong>{t}Articles for review{/t}</strong></a><hr>
			</li>
			<li>
				<a onclick="get_div_contents(0,'opinions',0,1);  divs_hide('opinions_div');" style="cursor:pointer;"><strong>{t}Opinions{/t}</strong></a><hr>
			</li>
<!--			<li>
				<a onclick="get_div_contents(0,'albums',3,1); divs_hide('albums_div');" style="cursor:pointer;"><strong>{t}Albums{/t}</strong></a><hr>
			</li>
			<li>
				<a onclick="get_div_contents(0,'videos',0,1);  divs_hide('videos_div');"  style="cursor:pointer;"><strong>{t}Videos{/t}</strong></a><hr>
			</li>-->
			<li>
				<a onclick="get_div_contents(0,'adjuntos', '{$article->category}',1); divs_hide('adjuntos_div'); " style="cursor:pointer;"><strong>{t}Files{/t}</strong></a><hr>
			</li>
                        <li>
				<a onclick="divs_hide('search-div'); " style="cursor:pointer;"><strong>{t}Search{/t}</strong></a><hr>
			</li>
		</ul>
	</div>
	<div id="available-suggested-contents" class="clearfix" >

		<div id='search-noticias' class='div_lists' style="display:none">
			<h2>{t}Suggested articles{/t}</h2>
		</div>

		<div id="noticias_div" class='div_lists' style="display:none"><br />
			{include file="menu_categorys.tpl" home=""}
			<h2>{t}Articles by section{/t}</h2>

		</div>
		<div id="hemeroteca_div" class='div_lists' style="display:none"><br />
			{include file="menu_categorys.tpl" home=""}
			<h2>{t}Articles in library{/t}</h2>

		</div>
                <div id="pendientes_div" class='div_lists' style="display:none"><br />
                        {include file="menu_categorys.tpl" home=""}
			<h2>{t}Pending articles{/t}</h2>

		</div>

		<div id="opinions_div" class='div_lists' style="display:none">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
				<tbody><tr>
				<td colspan="2">
						<h2>{t}Opinions{/t}</h2>
				</td>
				</tr>
				</tbody>
			</table>
		</div>


		<div  id="albums_div"  class='div_lists' style="display:none">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
				<tbody><tr>
				<td colspan="2">
					{include file="menu_categorys.tpl" home=""}
						 <h2>{t}Albums{/t}</h2>
				</td>
				</tr>
				</tbody>
			</table>
		</div>

		<div id='videos_div'  class='div_lists' style="display:none"><br/>
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
				<tbody><tr>
				<td colspan="2">
						{include file="menu_categorys.tpl" home=""}
						<h2>{t}Videos{/t}</h2>
				</td>
				</tr>
				</tbody>
			</table>
		</div>


		<div id="adjuntos_div"  class='div_lists' style="display:none">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
			<tbody>
			<tr>
				<td>
					{include file="menu_categorys.tpl" home=""}
					<h2>{t}Files{/t}</h2>
				 </td>
			</tr>
			</tbody>
			</table>
		</div>
		<div id="search-div"  class='div_lists' style="display:none">
			<div style="width:65%; margin:0 auto;">
				<h2 >{t}Search in the information catalog:{/t}</h2>
				<div>
					<input 	type="text" id="stringSearch" name="stringSearch" title="stringSearch"
							value="{$smarty.request.stringSearch|escape:"html"|clearslash}"
							size="80" onkeypress="onSearchAdvKeyEnter(event,{$article->pk_article|default:0});"/>
					<a href="#" class="onm-button blue"
					   onclick="search_adv({$article->pk_article|default:0}, $('stringSearch').value,1); Effect.Appear('search-div2');"
					   onmouseover="return escape('<u>S</u>earch');" accesskey="N" tabindex="1" title="Search">
					   {t}Search{/t}
					</a>
				</div>
			</div>
		</div>
                <div id="search-div2"  class='div_lists' style="display:none">
                    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
                        <tbody>
                            <tr>
                                <td>
                                    <div style="margin:0 auto; width:50%">
                                        <h3>{t}We cant find any content with your search criteria.{/t}</h3>
                                        <p>{t escape="no" 1=$smarty.request.stringSearch|clearslash}Your search "<b>%1</b>" didn't return any element.{/t}</p>
                                        <p style="margin-top: 1em;">{t}Suggestions:{/t}</p>
                                        <ul>
                                            <li>{t}Check if all the words are written correctly.{/t}</li>
                                            <li>{t}Use other words.{/t}</li>
                                            <li>{t}Use more general search criteria.{/t}</li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
</div>
