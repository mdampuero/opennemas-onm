{extends file="base/admin.tpl"}

{block name="header-css" append}
	<style type="text/css">
	td {
		background:none !important;
	}
	</style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Cache Manager{/t} :: {t}Configuration{/t}</h2></div>
		<ul class="old-button">
			<li>
				<a href="{$smarty.server.SCRIPT_NAME}" title="{t}Cancel{/t}">
					<img src="{$params.IMAGE_DIR}newsletter/previous.png" border="0" /><br />
					{t}Go back{/t}
				</a>
			</li>

			<li>

			</li>
		</ul>
	</div>

</div>
<div class="wrapper-content">

	<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">


		<div style="width:100%">
			<table id="tabla" name="tabla" class="adminlist" >
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
						<tr style="background:{cycle values="#f1f1f1,#fff"} !important">
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
				<tbody>
					<tr>
						<td>
							<h1>{t}There is no cache configuration available{/t}</h1>
						</td>
					</tr>
				</tbody>
			{/if}
			</table>
		</div>
		<div class="action-bar clearfix">
            <div class="right">
                <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button red">
            </div>
        </div>

		<input type="hidden" id="action" name="action" value="config" />
	</form>
</div>
{/block}
