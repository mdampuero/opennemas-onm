{extends file="base/admin.tpl"}

{block name="content"}
<div class="wrapper-content">

	<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">

		<div id="menu-acciones-admin">
			<div style="float:left; margin:8px;"><h2>{t}Cache Manager :: Cache groups{/t}</h2></div>
			<ul>
				<li>
					<a href="{$smarty.server.SCRIPT_NAME}" title="{t}Cancel{/t}">
						<img src="{$params.IMAGE_DIR}cancel.png" border="0" /><br />
						{t}Cancel{/t}
					</a>
				</li>

				<li>
					<a href="#config" onclick="$('formulario').submit();return false;" title="">
						<img src="{$params.IMAGE_DIR}save.gif" border="0" /><br />
						{t}Save{/t}
					</a>
				</li>
			</ul>
		</div>

		<div>

			<table class="adminheading" style="width:50%; margin:0 auto; margin-top:10px;">
				<tr>
					<th>&nbsp;</th>
				</tr>
			</table>

			<table id="tabla" name="tabla" class="adminlist" style="width:50%; margin:0 auto;">
			{if count($config)>0}

				<thead>
					<tr>
						<th>{t}Cache group{/t}</th>
						<th >{t}Activate{/t}</th>
						<th>{t}Expire time{/t}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$config key="k" item="v"}
						<tr bgcolor="{cycle values="#ccc,#eee"}">
							<td style="padding-left:30px;">
								<img src="{$params.IMAGE_DIR}template_manager/{$groupIcon.$k|default:""}" border="0" title="Caché de opinión interior" />
								{$groupName.$k|default:$k} &nbsp;
								<input type="hidden" name="group[]" value="{$k|default:""}" />
							</td>

							<td align="center">
								<input type="checkbox" name="caching[{$k|default:""}]" value="1" {if $v.caching}checked="checked"{/if}/>
							</td>

							<td align="center">
								<input type="text" size="12" name="cache_lifetime[{$k}]" value="{$v.cache_lifetime|default:300}" style="text-align: right;" /> <sub>segundos</sub>
							</td>
						</tr>
					{/foreach}
				</tbody>
			{else}
				<h1>{t}There is no cache configuration available{/t}</h1>
			{/if}
			<tfoot>
				<tr>
					<td colspan=3></td>
				</tr>
			</tfoot>
			</table>


		</div>

		<input type="hidden" id="action" name="action" value="config" />
	</form>
</div>
{/block}
