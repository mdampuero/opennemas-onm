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
					<img src="{$params.IMAGE_DIR}previous.png" border="0" /><br />
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

		<div style="width:700px; margin:0 auto;">
            <table class="listing-table">
                <thead>
                    <tr>
						<th class="center" style="width:10px">{t}Activate{/t}</th>
						<th >{t}Cache group{/t}</th>
						<th class="right">{t}Expire time{/t}</th>
					</tr>
                </thead>
                <tbody>
					{foreach from=$config key="k" item="v"}
                    <tr>
                        <td class="center">
                            <input type="checkbox" name="caching[{$k|default:""}]" value="1" {if $v.caching}checked="checked"{/if}/>
                        </td>
                        <td>
                            <img src="{$params.IMAGE_DIR}template_manager/{$groupIcon.$k|default:""}" border="0" title="Caché de opinión interior" />
                            {$groupName.$k|default:$k}
                            <input type="hidden" name="group[]" value="{$k|default:""}" />
                        </td>

                        <td class="right">
                            <input type="text" size="7" name="cache_lifetime[{$k}]" value="{$v.cache_lifetime|default:300}" style="text-align: right;" /> {t}seconds{/t}
                        </td>
                    </tr>
                    {foreachelse}
                    <tr>
						<td class="empty" colspan=3>
							{t}There is no cache configuration available{/t}
						</td>
					</tr>
					{/foreach}
				</tbody>
            </table>
			<table id="tabla" name="tabla" class="adminlist" >
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
