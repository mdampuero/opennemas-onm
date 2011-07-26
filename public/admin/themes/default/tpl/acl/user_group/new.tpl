{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Keyword Manager :: Listing keywords{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$user_group->id}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
                    </a>
                </li>
                <li>
                {if isset($user_group->id)}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', {$user_group->id}, 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', 0,'formulario');">
                {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
                    </a>
                </li>
                <li>
                    <a href="{$smaty.server.PHP_SELF}?action=list" value="Cancelar" title="Cancelar">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <table class="adminheading">
            <tr>
                <th nowrap></th>
            </tr>
        </table>
        <table class="adminlist">
            <tbody>
            <!-- Id -->
            <tr>
                <td align="right" style="padding:4px;" width="20%">
                    <label for="id">{* Id: *}</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" width="80%">
                    <input type="hidden" id="idReadOnly" name="idReadOnly" title="{t}Id{/t}"
                        value="{$user_group->id}" readonly />
                </td>
            </tr>
            <!-- Nome -->
            <tr>
                <td valign="top" align="right" style="padding:4px;" width="20%">
                    <label for="name">{t}Name:{/t}</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" width="80%">
                    <input type="text" id="name" name="name" title="{t}Group name{/t}"
                        value="{$user_group->name}" class="required"
                        {if $user_group->name eq $smarty.const.NAME_GROUP_ADMIN}disabled="disabled"{/if} />
                </td>
            </tr>
            <!-- Privileges -->
            <tr>
                <td valign="top" align="right" style="padding:4px;" width="20%">
                    <label for="privileges">{t}Grants:{/t}</label>
                </td>

                <td style="padding:4px;" nowrap="nowrap" width="80%">
                    {foreach item=privileges from=$modules key=mod name=priv}
                    <div  style="float:left;width:45%; margin-right:10px;">
                        <div style="background-color: #EEE;">
                            <a style="cursor:pointer;" onClick="Element.toggle('{$mod}');">
                                <h3 style="padding:4px;"> {t}{$mod}{/t} </h3>
                            </a>
                            <table border="0" cellpadding="0" cellspacing="0" id="{$mod}" class="fuente_cuerpo" width="100%" style="display:none;">
                                <tbody>
                                {section name=privilege loop=$privileges}
                                    <tr>
                                    <td style="padding:4px;" nowrap="nowrap" width="5%">

                                    {if $user_group->contains_privilege($privileges[privilege]->id)}
                                       <input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}" checked>
                                       <script  type="text/javascript">
                                            $('{$mod}').setStyle('display:block');
                                       </script>

                                    {else}
                                       <input type="checkbox" name="privileges[]" id="privileges[]" value="{$privileges[privilege]->id}">
                                    {/if}
                                    </td>
                                    <td valign="top" align="left" style="padding:4px;" width="95%">
                                            {t}{$privileges[privilege]->description}{/t}
                                    </td>
                                    </tr>
                                {/section}
                            </tbody>
                            </table>
                        </div>
                    </div>
                    {/foreach}
                </td>
            </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="center">{$paginacion->links|default:""}</td>
                </tr>
            </tfoot>
        </table>
	</div>

	<input type="hidden" id="action" name="action" value="" />
	<input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{/block}
