{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="/admin.css"}
    <style>
    input[type="text"]  {
        max-height: 80%;
        width: 500px;
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
                    <label for="emailDA">{t}Email{/t}:</label>
                </td>
                <td>
                    <input type="text" id="emailDA" name="email" title="{t}Email{/t}"
                        value="{$user->email}" class="required validate-email" />
                </td>
                <td rowspan="6">
                    <div class="onm-help-block margin-left-1">
                        <div class="title"><h4>{t}Basic parameters{/t}</h4></div>
                        <div class="content">
                            <dl>
                                <dt><strong>{t}Subscriptor data{/t}</strong></dt>
                                <dd>{t}The complete name and email address of the subscriptor{/t}</dd>
                                <dt><strong>{t}Subscribed{/t}</strong></dt>
                                <dd>{t}If is subscribed, the user email address will be available on the account provider{/t}</dd>
                                <dt><strong>{t}Activated{/t}</strong></dt>
                                <dd>{t}If is activated means that the user is ready to receive newsletters{/t}</dd>
                            </dl>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="nombreDA">{t}Name{/t}:</label>
                </td>
                <td colspan="2">
                    <input type="text" id="nombreDA" name="name" title="{t}Name{/t}"
                        value="{$user->name}" class="required validate-alpha" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="apellidoDA">{t}Surname{/t}:</label>
                </td>
                <td colspan="2">
                    <input type="text" id="apellidoDA" name="firstname" title="{t}Surname{/t}"
                        value="{$user->firstname}" class="required validate-alpha" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="segApellidoDA">{t}Lastname{/t}:</label>
                </td>
                <td colspan="2">
                    <input type="text" id="segApellidoDA" name="lastname" title="{t}Lastname{/t}"
                        class="validate-alpha" value="{$user->lastname}" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="subscribed">
                        {t}Subscribed{/t}:
                    </label>
                </td>
                <td colspan="2">
                    <select name="subscription" id="subscribed">
                        <option value="1" {if is_null($user->subscription) || $user->subscription eq 1 }selected="selected"{/if}>{t}Yes{/t}</option>
                        <option value="0" {if (isset($user->subscription)) && $user->subscription eq 0}selected="selected"{/if}>{t}No{/t}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="activated">
                        {t}Activated{/t}:
                    </label>
                </td>
                <td colspan="2">
                    <select name="status" id="activated">
                        <option value="2" {if is_null($user) || $user->status eq 2}selected="selected"{/if}>{t}Yes{/t}</option>
                        <option value="3" {if $user->status eq 3 || $user->status eq 1}selected="selected"{/if}>{t}No{/t}</option>
                    </select>
                </td>
            </tr>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div>
</form>
{/block}
