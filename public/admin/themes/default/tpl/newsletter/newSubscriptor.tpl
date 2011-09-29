{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/admin.css"}
    <style>
    input[type="text"]  {
        max-height: 80%;
        width: 300px;
    }
    </style>
{/block}

{block name="footer-js" append}

    </script>
{/block}

{block name="content"}
    <form action="#" method="post" name="formulario" id="formulario">

        <div class="top-action-bar clearfix">
            <div class="wrapper-content">
                <div class="title"><h2>{$titulo_barra}&nbsp; </h2></div>
                    <ul class="old-button">
                        <li>
                            <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$user->id}', 'formulario');" value="Validar" title="Validar">
                                <img border="0" src="{$params.IMAGE_DIR}validate.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
                            </a>
                        </li>
                        <li>
                            <a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', '{$user->id}');" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
                                <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />Cancelar
                            </a>
                        </li>
                        <li>
                        {if isset($user->id) }
                           <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$user->id}', 'formulario');">
                        {else}
                           <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
                        {/if}
                                <img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar" alt="Guardar"><br />Guardar
                            </a>
                        </li>
                    </ul>
            </div>
        </div>

        <div class="wrapper-content">
            <table  class="adminform">
                <!-- Email -->
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="emailDA">{t}Email{/t}:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="emailDA" name="email" title="Correo Electr&oacute;nico"
                            value="{$user->email}" class="required validate-email" />
                    </td>
                </tr>

                    <!-- Nome -->
                    <tr>
                        <td valign="top" align="right" style="padding:4px;" width="200">
                            <label for="nombreDA">{t}Name{/t}:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="nombreDA" name="name" title="Nombre del usuario"
                                value="{$user->name}" class="required validate-alpha" />
                        </td>
                    </tr>
                    <!-- Primeiro apelido -->
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="apellidoDA">{t}Surname{/t}:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="apellidoDA" name="firstname" title="Primer apellido del usuario"
                                value="{$user->firstname}" class="required validate-alpha" />
                        </td>
                    </tr>
                    <!-- Segundo apelido -->
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="segApellidoDA">{t}Lastname{/t}</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="segApellidoDA" name="lastname" title="Segundo apellido del usuario"
                                class="validate-alpha" value="{$user->lastname}" />
                        </td>
                    </tr>
                     <tr>
                    <td valign="middle" align="right" style="padding:4px">
                        <label>
                            {t}Subscribed{/t}:
                        </label>
                    </td>
                    <td style="padding:10px;">
                        <select name="subscription">
                            <option value="1" {if !(isset($user->subscription)) || $user->subscription eq 1 }checked="checked"{/if}>{t}Yes{/t}</option>
                            <option value="0" {if (isset($user->subscription)) && $user->subscription eq 0}checked="checked"{/if}>{t}No{/t}</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px;" width="200" align="right">
                        <label>
                            {t}Activated{/t}
                        </label>
                    </td>
                    <td style="padding:10px;">
                        <select name="status">
                            <option value="3" {if $user->status eq 3 }checked="checked"{/if}>{t}Yes{/t}</option>
                            <option value="2" {if !isset($user) || $user->status eq 2 || $user->status eq 1 }checked="checked"{/if}>{t}No{/t}</option>
                        </select>
                    </td>
                </tr>
            </table>

            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" name="id" id="id" value="{$id|default:""}" />

        </div>
    </form>
{/block}
