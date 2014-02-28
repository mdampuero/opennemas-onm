{extends file="base/admin.tpl"}

{block name="content"}
<form id="formulario" name="formulario" action="{url name=admin_keywords}" method="GET">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Keywords{/t}</h2></div>
			<ul class="old-button">
				{acl isAllowed="PCLAVE_CREATE"}
					<li>
						<a href="{url name=admin_keyword_create}" class="admin_add" title="Nueva palabra clave">
							<img border="0" src="{$params.IMAGE_DIR}list-add.png" title="Nueva palabra clave" alt="" ><br />{t}New{/t}
						</a>
					</li>
				{/acl}
			</ul>
		</div>
	</div>
	<div class="wrapper-content">

		{render_messages}

		<div class="table-info clearfix">
            <div>
                <div class="right form-inline">
                    {if isset($smarty.request.filter)
						&& isset({$smarty.request.filter.pclave})}
						{assign var=filterPClave value=$smarty.request.filter.pclave}
					{/if}
					<div class="input-append">
						<label for="filter">
							{t}Search keyworks containing {/t}
							<input type="search" name="name" value="{$name|default:""}" class="input-medium" />
						</label>
						<button type="submit" class="btn"><i class="icon-search"></i></button>
					</div>
                </div>
            </div>
        </div>
		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th style="width:20px;" class="nowrap">{t}Type{/t}</th>
					<th scope=col>{t}Keyword{/t}</th>
					<th scope=col>{t}Replacement value{/t}</th>
					<th scope=col style="width:100px;" class="right nowrap">{t}Actions{/t}</th>
				</tr>
			</thead>

			<tbody>
				{foreach name=k from=$keywords|default:array() item=keyword}
				<tr>
					<td class="center nowrap">
						{$types[$keyword->tipo]}
					</td>
					<td>
						{$keyword->pclave}
					</td>
					<td>
						{$keyword->value|default:"-"}
					</td>

					<td class="right">
						<div class="btn-group">
							<a class="btn" href="{url name=admin_keyword_show id=$keyword->id}" title="{t}Edit this content{/t}">
	                            <i class="icon-pencil"></i>{t}Edit{/t}
	                        </a>
	                        <a href="{url name=admin_keyword_delete id=$keyword->id}" class="btn btn-danger" title="{t}Delete{/t}">
	                            <i class="icon-trash icon-white"></i>
	                        </a>
						</div>

					</td>
				</tr>
				{foreachelse}
				<tr>
					<td class="empty" colspan=4>
						{t}No available keywords yet.{/t}
					</td>
				</tr>
				{/foreach}
			</tbody>

			<tfoot>
				<tr>
					<td colspan="5" class="center">
						<div class="pagination">
							{$pagination->links}
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</form>

{/block}
