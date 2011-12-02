{extends file="base/admin.tpl"}

{block name="header-css" append}
<style>
	table.adminlist td,
	table.adminlist th {
		padding: 8px;
	}
</style>
{/block}

{block name="content"}


<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Privileges manager{/t}</h2></div>
            <ul class="old-button">				 
				<li>
					<a href="{$smarty.server.PHP_SELF}?action=new&id=0" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}user_add.png" title="Nuevo" alt="Nuevo"><br />{t}New Privilege{/t}
					</a>
				</li>
			</ul>
		</div>
	</div>
    
    {render_messages}
    
    <div class="wrapper-content">
        <table class="adminheading">
            <tr>
                <th nowrap align="right">
                    <label>{t}Filter by module:{/t}
                        <select name="module" onchange="$('action').value='list';$('formulario').submit();">
                            <option value="">{t}-- All --{/t}</option>
                            {section name="mods" loop=$modules}
							<option value="{$modules[mods]}"{if isset($smarty.request.module) && $modules[mods] eq $smarty.request.module} selected="selected"{/if}>{$modules[mods]}</option>
                            {/section}
                        </select>
                    </label>
                </th>
            </tr>
        </table><!--.adminheading-->

        <table class="listing-table">
            <thead>
                <tr>
                    <th>{t}Privilege name{/t}</th>
                    <th>{t}Description{/t}</th>
                    <th>{t}Módule{/t}</th>
                    <th class="center" style="width:30px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                {section name=c loop=$privileges}
                <tr>
                    <td>
                        <a href="{$smarty.server.PHP_SELF}?action=read&id={$privileges[c]->id}" title="{t}Edit privilege{/t}">
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
                        <ul class="action-buttons">
                            <li>
                                <a href="{$smarty.server.PHP_SELF}?action=read&id={$privileges[c]->id}" title="{t}Edit privilege{/t}">
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                </a>
                            </li>
                            <li>
                                <a href="#" onClick="javascript:confirmar(this, '{$privileges[c]->id}');" title="{t}Delete privilege{/t}">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                </a>
                            </li>
                        </ul>
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
                        {$paginacion->links|default:""}&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table> <!--.listing-table-->

    	<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{/block}
