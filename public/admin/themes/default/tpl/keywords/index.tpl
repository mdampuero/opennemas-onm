{extends file="base/admin.tpl"}

{block name="content"}
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Keyword Manager :: Listing keywords{/t}</h2></div>
			<ul class="old-button">
				<li>
					<a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add" title="Nueva palabra clave">
						<img border="0" src="{$params.IMAGE_DIR}list-add.png" title="Nueva palabra clave" alt="" ><br />{t}New{/t}
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="wrapper-content">

			<table class="adminheading">
				<tr>
					<td align="right">
						{t}Search keyworks containing {/t}
						{if isset($smarty.request.filter)
							&& isset({$smarty.request.filter.pclave})}
							{assign var=filterPClave value=$smarty.request.filter.pclave}
						{/if}
						<input type="text" name="filter[pclave]" style="margin-top:-2px;" value="{$filterPClave|default:""}" />
						<button type="submit" onclick="javascript:$('action').value='list';">Filtrer</button>
					</td>
				</tr>
			</table>
			<table border="0" cellpadding="4" cellspacing="0" class="adminlist">
				<thead>
					<tr>
						<th class="title">{t}Type{/t}</th>
						<th class="title">{t}Keyword{/t}</th>
						<th class="title">{t}Replacement value{/t}</th>
						<th>{t}Actions{/t}</th>
					</tr>
				</thead>

				<tbody>
					{section name=k loop=$pclaves|default:array()}
					<tr style="background:{cycle values="#eeeeee,#ffffff"} !important">

						<td align="center" weight="15">
							<img src="{$params.IMAGE_DIR}iconos/{$pclaves[k]->tipo}.gif" border="0" alt="{$pclaves[k]->tipo}" />
						</td>
						<td>
							{$pclaves[k]->pclave}
						</td>
						<td>
							{$pclaves[k]->value|default:"-"}
						</td>

						<td width="44">
							<a href="{$smarty.server.PHP_SELF}?action=read&id={$pclaves[k]->id}" title="{t}Modify{/t}">
								<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
							&nbsp;
							<a href="#" onClick="javascript:confirmar(this, {$pclaves[k]->id});" title="{t}Delete{/t}">
								<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
						</td>
					</tr>
					{sectionelse}
					<tr>
						<td align="center"><b>Ninguna palabra guardada.</b></td>
					</tr>
					{/section}
				</tbody>

				<tfoot class="pagination">
					<tr>
						<td colspan="5" align="center">
							{$pager->links}
						</td>
					</tr>
				</tfoot>
			</table>

			<input type="hidden" id="action" name="action" value="" />
			<input type="hidden" name="id" id="id" value="{$id|default:""}" />

	</div>
</form>

{/block}
