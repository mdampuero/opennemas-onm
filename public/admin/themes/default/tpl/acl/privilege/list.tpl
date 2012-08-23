{extends file="base/admin.tpl"}
{block name="content"}


<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Privileges manager{/t}</h2></div>
            <ul class="old-button">
				<li>
					<a href="{url name="admin_acl_privileges_create"}" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}user_add.png" title="Nuevo" alt="Nuevo"><br />{t}New Privilege{/t}
					</a>
				</li>
			</ul>
		</div>
	</div>

    <div class="wrapper-content">
        {render_messages}
        <div class="table-info clearfix">
            <div>
                <div class="right form-inline">
                    <label>{t}Filter by module:{/t}
                        <select name="module" onchange="$('action').value='list';$('formulario').submit();">
                            <option value="">{t}-- All --{/t}</option>
                            {section name="mods" loop=$modules}
                            <option value="{$modules[mods]}"{if isset($smarty.request.module) && $modules[mods] eq $smarty.request.module} selected="selected"{/if}>{$modules[mods]}</option>
                            {/section}
                        </select>
                    </label>
                </div>
            </div>
        </div>

        <table class="listing-table table-striped">
            <thead>
                <tr>
                    <th>{t}Privilege name{/t}</th>
                    <th>{t}Description{/t}</th>
                    <th>{t}Module{/t}</th>
                    <th class="center" style="width:110px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                {section name=c loop=$privileges}
                <tr>
                    <td>
                        <a href="{url name="admin_acl_privileges_show" id=$privileges[c]->id}" title="{t}Edit privilege{/t}">
                            {$privileges[c]->name}
                        </a>
                    </td>

                    <td>
                        {$privileges[c]->description}
                    </td>

                    <td>
                        {$privileges[c]->module}
                    </td>

                    <td class="right">
                        <div class="btn-group">
                            <a class="btn" href="{url name="admin_acl_privileges_show" id=$privileges[c]->id}" title="{t}Edit privilege{/t}">
                                <i class="icon-pencil"></i> {t}Edit{/t}
                            </a>
                            <a class="btn btn-danger" href="{url name="admin_acl_privileges_delete" id=$privileges[c]->id}" title="{t}Delete privilege{/t}">
                                <i class="icon-trash icon-white"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td colspan="5" align="center"><b>{t}No available privileges to list here.{/t}</b></td>
                </tr>
                {/section}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" align="center">
                        {$paginacion->links|default:"&nbsp;"}
                    </td>
                </tr>
            </tfoot>
        </table> <!--.listing-table-->

    	<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{/block}
