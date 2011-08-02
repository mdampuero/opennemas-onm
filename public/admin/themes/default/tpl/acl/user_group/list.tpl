{extends file="base/admin.tpl"}

{block name="action_buttons"}
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}User group management{/t}</h2></div>
			<ul class="old-button">
				<li>
                    <a href="{$smarty.server.PHP_SELF}?action=new">
                        <img border="0" src="{$params.IMAGE_DIR}usergroup_add.png" title="{t}New Privilege{/t}" alt="{t}New User Group{/t}"><br />{t}New User group{/t}
                    </a>
                </li>
			</ul>
		</div>
	</div>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
    {block name="action_buttons"}{/block}
    <div class="wrapper-content">

        <table class="adminheading">
            <tr>
                <th nowrap></th>
            </tr>
        </table>
        <table class="adminlist">
            <thead>
                <tr>
                <th class="title" style="text-align:left; padding-left:10px">{t}Group name{/t}</th>
                <th class="title">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                {section name=c loop=$user_groups}
                <tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
                    <td style="padding:10px;">
                        {$user_groups[c]->name}
                    </td>
                    <td style="padding:10px;width:75px; text-align:center">
						<ul class="action-buttons">
							<li>
								<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$user_groups[c]->id});" title="{t}Edit group{/t}">
									<img src="{$params.IMAGE_DIR}edit.png" alt="{t}Edit group{/t}" border="0" />
								</a>
							</li>
							<li>
								<a href="#" onClick="javascript:confirmar(this, {$user_groups[c]->id});" title="{t}Delete group{/t}">
									<img src="{$params.IMAGE_DIR}trash.png" alt="{t}Delete group{/t}" border="0" />
								</a>
							</li>
						</ul>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td align="center"><b>{t}There is no groups created yet.{/t}</b></td>
                </tr>
                {/section}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="center">{$paginacion->links|default:""}</td>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{/block}
