{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/admin.css"}
    <style>
    input[type="text"]  {
        max-height: 80%;
        width: 300px;
    }
    label {
        float:right;
    }
    td {
        padding:10px;
    }
    </style>
{/block}

{block name="footer-js" append}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>
                    {if $smarty.request.action eq "new"}
                        {t}Creating new subscriptor{/t}
                    {else}
                        {t 1=$user->name}Editing subscriptor "%1"{/t}{/if}
                </h2>
            </div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$user->id}', 'formulario');" title="Validar">
                        <img src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />{t}Save and continue{/t}
                    </a>
                </li>
                <li>
                {if isset($user->id) }
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$user->id}', 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
                {/if}
                    <img src="{$params.IMAGE_DIR}save.png" title="Guardar" alt="Guardar"><br />{t}Save and exit{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="?action=list" class="admin_add" title="Cancelar">
                        <img src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        <table class="adminheading">
             <tr>
                 <th>{t}Add new subscriptor{/t}</th>
             </tr>
        </table>
        <table  class="adminform">
            <tr>
                <td>
                    <label for="emailDA">{t}Email{/t}</label>
                </td>
                <td>
                    <input type="text" id="emailDA" name="email" title="Correo Electr&oacute;nico"
                        value="{$user->email}" class="required validate-email" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="nombreDA">{t}Name{/t}</label>
                </td>
                <td>
                    <input type="text" id="nombreDA" name="name" title="Nombre del usuario"
                        value="{$user->name}" class="required validate-alpha" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="apellidoDA">{t}Surname{/t}</label>
                </td>
                <td>
                    <input type="text" id="apellidoDA" name="firstname" title="Primer apellido del usuario"
                        value="{$user->firstname}" class="required validate-alpha" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="segApellidoDA">{t}Lastname{/t}</label>
                </td>
                <td>
                    <input type="text" id="segApellidoDA" name="lastname" title="Segundo apellido del usuario"
                        class="validate-alpha" value="{$user->lastname}" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="subscribed">
                        {t}Subscribed{/t}:
                    </label>
                </td>
                <td>
                    <select name="subscription" id="subscribed">
                        <option value="1" {if is_null($user->subscription) || $user->subscription eq 1 }selected="selected"{/if}>{t}Yes{/t}</option>
                        <option value="0" {if (isset($user->subscription)) && $user->subscription eq 0}selected="selected"{/if}>{t}No{/t}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="activated">
                        {t}Activated{/t}
                    </label>
                </td>
                <td>
                    <select name="status" id="activated">
                        <option value="2" {if is_null($user) || $user->status eq 2}selected="selected"{/if}>{t}Yes{/t}</option>
                        <option value="3" {if $user->status eq 3 || $user->status eq 1}selected="selected"{/if}>{t}No{/t}</option>
                    </select>
                </td>
            </tr>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div>
</form>
{/block}
