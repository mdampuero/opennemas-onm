<div class="clearfix" style="width:100%">
	<div id="selector" class="clearfix" style="width:100%">
		{* onClick="{section start=$aux loop=$todos2 step=1 name=foo}Effect.Fade('interior#up{$smarty.section.foo.index}');{/section}Effect.Appear('imgint');return false;" *}
		<ul id="tabs">
			<li>
				<a onclick="search_related({$article->pk_article},$('metadata').value,1); divs_hide('search-noticias');" style="cursor:pointer;"><strong>{t}Suggested articles{/t}</strong></a><hr>
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
			<li>
				<a onclick="get_div_contents(0,'albums',3,1); divs_hide('albums_div');" style="cursor:pointer;"><strong>{t}Albums{/t}</strong></a><hr>
			</li>
			<li>
				<a onclick="get_div_contents(0,'videos',0,1);  divs_hide('videos_div');"  style="cursor:pointer;"><strong>{t}Videos{/t}</strong></a><hr>
			</li>
			<li>
				<a onclick="get_div_contents(0,'adjuntos', '{$article->category}',1); divs_hide('adjuntos_div'); " style="cursor:pointer;"><strong>{t}Files{/t}</strong></a><hr>
			</li>
		</ul>
	</div>
	<div id="available-suggested-contents" class="clearfix" >

		<div id='search-noticias' class='div_lists' style="border:1px solid #ccc; padding:10px; display:none">
			<h2>{t}Suggested articles{/t}</h2>
		</div>

		<div id="noticias_div" class='div_lists' style="border:1px solid #ccc; padding:10px; display:none"><br />
			{include file="menu_categorys.tpl" home=""}
			<h2>{t}Articles by section{/t}</h2>

		</div>
		<div id="hemeroteca_div" class='div_lists' style="border:1px solid #ccc; padding:10px; display:none"><br />
			{include file="menu_categorys.tpl" home=""}
			<h2>{t}Articles in library{/t}</h2>

		</div>
			<div id="pendientes_div" class='div_lists' style="border:1px solid #ccc; padding:10px; display:none"><br />
				{include file="menu_categorys.tpl" home=""}
			<h2>{t}Pending articles{/t}</h2>

		</div>

		<div id="opinions_div" class='div_lists' style="border:1px solid #ccc; padding:10px; display:none">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
				<tbody><tr>
				<td colspan="2">
						<h2>{t}Opinions{/t}</h2>
				</td>
				</tr>
				</tbody>
			</table>
		</div>


		<div  id="albums_div"  class='div_lists' style="border:1px solid #ccc; padding:10px; display:none">
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

		<div id='videos_div'  class='div_lists' style="border:1px solid #ccc; padding:10px; display:none"><br/>
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


		<div id="adjuntos_div"  class='div_lists' style="border:1px solid #ccc; padding:10px; display:none">
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


		<div id="search_div"  class='div_lists' style="border:1px solid #ccc; padding:10px; display:none">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
			<tbody>
				<tr>
					<td>
						 Buscar: <input type="text" id="stringSearch" name="stringSearch" title="stringSearch"
										class="required" size="80" onkeypress=" "/>
					 </td>
				</tr>
			</tbody>
			</table>
		</div>

	</div>

</div>
