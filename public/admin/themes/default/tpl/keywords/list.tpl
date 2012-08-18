{extends file="base/admin.tpl"}

{block name="content"}
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Keyword Manager{/t} :: {t}Listing keywords{/t}</h2></div>
			<ul class="old-button">
				<li>
					<a href="{url name=admin_keywords_create}" class="admin_add" title="Nueva palabra clave">
						<img border="0" src="{$params.IMAGE_DIR}list-add.png" title="Nueva palabra clave" alt="" ><br />{t}New{/t}
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="wrapper-content">

			<div class="table-info clearfix">
	            <div>
	                <div class="right form-inline">
	                    {if isset($smarty.request.filter)
							&& isset({$smarty.request.filter.pclave})}
							{assign var=filterPClave value=$smarty.request.filter.pclave}
						{/if}
						<label for="filter">
							{t}Search keyworks containing {/t}
							<input type="search" name="filter[pclave]" value="{$filterPClave|default:""}" class="input-medium search-query" />
						</label>
						<button type="submit" class="btn">{t}Buscar{/t}</button>
	                </div>
	            </div>
	        </div>
			<table class="listing-table">
				<thead>
					<tr>
						<th style="width:20px;">{t}Type{/t}</th>
						<th scope=col>{t}Keyword{/t}</th>
						<th scope=col>{t}Replacement value{/t}</th>
						<th scope=col style="width:100px;">{t}Actions{/t}</th>
					</tr>
				</thead>

				<tbody>
					{section name=k loop=$pclaves|default:array()}
					<tr>
						<td class="center">
							<img src="{$params.IMAGE_DIR}iconos/{$pclaves[k]->tipo}.gif" border="0" alt="{$pclaves[k]->tipo}" />
						</td>
						<td>
							{$pclaves[k]->pclave}
						</td>
						<td>
							{$pclaves[k]->value|default:"-"}
						</td>

						<td class="right">
							<a class="btn btn-mini" href="{$smarty.server.PHP_SELF}?action=read&amp;id={$pclaves[k]->id}" title="{t}Edit this content{/t}">
	                            {t}Edit{/t}
	                        </a>
	                        <a class="btn btn-danger btn-mini" onClick="javascript:confirmar(this, {$pclaves[k]->id});" title="{t}Delete{/t}">
	                            {t}Delete{/t}
	                        </a>
						</td>
					</tr>
					{sectionelse}
					<tr>
						<td class="empty" align="center" colspan=4>{t}No available keywords yet.{/t}</td>
					</tr>
					{/section}
				</tbody>

				<tfoot class="pagination">
					<tr>
						<td colspan="5" class="center">
							{$pager->links}&nbsp;
						</td>
					</tr>
				</tfoot>
			</table>

			<input type="hidden" id="action" name="action" value="" />
			<input type="hidden" name="id" id="id" value="{$id|default:""}" />

	</div>
</form>

{/block}
