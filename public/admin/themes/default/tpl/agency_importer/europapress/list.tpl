{extends file="base/admin.tpl"}

{block name="header-js"}
{$smarty.block.parent}
<script type='text/javascript' src='{$params.JS_DIR}prototip.js'></script>
{/block}

{block name="content"}
<div class="wrapper-content">
   <form action="{$smarty.server.PHP_SELF}" method="get" name="formulario" id="formulario" {$formAttrs}>
   {include file="agency_importer/europapress/menu.tpl"}
	
   {if ($message || ($minutes > 10))}
   <div class="notice">
		<ul>
			{if $minutes > 10}
			<p>
				{if $minutes > 100}
				<span class="red">{t}A long time ago from synchronization.{/t}</span>
				{else}
				<span class="red">{t 1=$minutes}Last sync was %1 minutes ago.{/t}</span>
				{/if}
				{t}Try syncing the news list from server by clicking in "Sync with server" button above{/t}
			</p>
			{/if}
			{if $message}<p>{$message}</p>{/if}
		</ul>
   </div>
   {/if}
   
   {if ($error)}
   <div class="error">
		{$error}
   </div>
   {/if}

   <div id="{$category}">

        <table class="adminheading">
			<tr>
                <th align="left">Total: {$elements|count} articles.</th>
				<th nowrap="nowrap" align="right">

					<label for="username">{t}Filter by title{/t}</label>
					<input id="username" name="filter[name]" onchange="$('action').value='list';this.form.submit();" value="{$smarty.request.filter.name}" />

					<label for="usergroup">{t}and category:{/t}</label>
					<select id="usergroup" name="filter[category]" onchange="$('action').value='list';this.form.submit();">
						{html_options options=$categories selected=$smarty.request.filter.group}
					</select>

					<input type="hidden" name="page" value="{$smarty.request.page}" />
					<input type="submit" value="{t}Search{/t}">
				</th>
			</tr>
		</table>

		<table class="adminlist" border=0>
            {if count($elements) >0}
			<thead>
				<tr>
				    <th  style='width:1%;' align="center">{t}Priority{/t}</th>
					<th>{t}Title{/t}</th>
					<th align="center" style="width:5%;">{t}Date{/t}</th>
					<th  style='width:6%;' align="center">{t}Section{/t}</th>
					<th  style='width:6%;' align="center">{t}Actions{/t}</th>
				</tr>
			</thead>
            {/if}
			

            {section name=c loop=$elements}
            <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
			   
		 		<td align="center">{$elements[c]->priority}</td>
				<td style="font-size: 12px;" onmouseout="UnTip()" onmouseover="Tip('{$elements[c]->body|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, false, ABOVE, false, WIDTH, 800)">
					<a href="{$smarty.server.PHP_SELF}?action=show&id={$elements[c]->id}" title="Importar">
						{$elements[c]->title}
					</a>
				</td>
				<td align="center">
						{$elements[c]->created_time->getTimestamp()|relative_date}
				</td>
				
				<td align="center">
					{$elements[c]->category}
				</td>
				
				<td style="font-size: 11px;width:100px;" align="center">
                     <a class="publishing" href="{$smarty.server.PHP_SELF}?action=import&id={$elements[c]->id}" title="Importar">
						<img border="0" alt="Publicar" src="{$params.IMAGE_DIR}archive_no2.png">
					 </a>
                </td> 
				
           </tr>
    
            {sectionelse}
            <tr>
                <td align="center" colspan=10>
                    <br><br>
                    <p>
                        <h2>
                            <b>{t}There is no elements to import{/t}</b>
                        </h2>
                        <p>{t}Try syncing from server by click over the "Sync with server" button above.{/t}</p>
                    </p>
                    <br><br>
                </td>
            </tr>
            {/section}
            <tfoot>
                 <tr class="pagination" >
                     <td colspan="13" align="center">{$paginacion->links}</td>
                 </tr>
            </tfoot>

	   </table>
   </div>

   <input type="hidden" id="action" name="action" value="list" />
   </form>
</div>
{/block}
