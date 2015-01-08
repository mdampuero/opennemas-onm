{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_tpl_manager_config}" method="POST">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}Cache Manager{/t} :: {t}Settings{/t}
                        </h4>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="top-action-bar clearfix">
    	<div class="wrapper-content">
    		<ul class="old-button">
                <li>
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
    			<li>
    				<a href="{url name=admin_tpl_manager}" title="{t}Cancel{/t}">
    					<img src="{$params.IMAGE_DIR}previous.png" /><br />
    					{t}Go back{/t}
    				</a>
    			</li>
    		</ul>
    	</div>
    </div>
    <div class="wrapper-content">
		<div style="width:700px; margin:0 auto;">
            {render_messages}
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
						<th class="center" style="width:10px">
                            <input type="checkbox" class="toggleallcheckbox" value="" />
                        </th>
						<th >{t}Cache group{/t}</th>
						<th class="right">{t}Expire time{/t}  <small>({t}seconds{/t})</small></th>
					</tr>
                </thead>
                <tbody>
					{foreach from=$config key="k" item="v"}
                    <tr>
                        <td class="center">
                            <input type="checkbox" name="caching[{$k|default:""}]" value="1" {if $v.caching}checked="checked"{/if}/>
                        </td>
                        <td>
                            <img src="{$params.IMAGE_DIR}template_manager/elements/{$groupIcon.$k}" title="Caché de opinión interior" />
                            {$groupName.$k|default:$k}
                            <input type="hidden" name="group[]" value="{$k|default:""}" />
                        </td>

                        <td class="right">
                            <input type="text" size="7" name="cache_lifetime[{$k}]" value="{$v.cache_lifetime|default:300}" style="text-align: right;" />
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
		</div>

		<input type="hidden" id="action" name="action" value="config" />
    </div>
</form>
{/block}
