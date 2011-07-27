{extends file="base/admin.tpl"}

{block name="header-js"}
{$smarty.block.parent}
<script type='text/javascript' src='{$params.JS_DIR}prototip.js'></script>
{/block}

{block name="header-css" append}
    <style type="text/css">
    label {
        width:150px;
        padding-left:10px;
        display:inline-block;
    }
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    .form-wrapper {
        margin:10px auto;
        width:50%;
    }
    </style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EuropaPress importer{/t} :: {t}Module configuration{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=list" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
   <form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario" {$formAttrs}>
        <br>

        {if $message}
        <div class="error">
             <ul>
                {foreach from=$message item=msg}
                <li>{$msg}</li>
                {/foreach}
             </ul>
        </div>
        {/if}

        {if (!empty($error))}
        <div class="error">
             {render_error}
        </div>
        {/if}

        <div id="{$category}">

             <table class="adminheading">
                 <tr>
                     <th align="left">{t}Auth credentials{/t}</th>
                 </tr>
             </table>

             <table class="adminlist" border=0>

                <tr>
                    <td>
                        <div class="form-wrapper">
                            <div>
                                <label for="server">{t}Server:{/t}</label>
                                <input type="text" class="required" name="server" value="{$server|default:""}" />
                            </div>
                            <div>
                                <label for="username">{t}Username:{/t}</label>
                                <input type="text" class="required" id="username" name="username" value="{$username|default:""}" />
                            </div>
                            <div>
                                <label for="password">{t}Password:{/t}</label>
                                <input type="password" class="required" id="password" name="password" value="{$password|default:""}" />
                            </div>
                        </div>
                    </td>
                </tr>

            </table>
            <div class="action-bar">
                <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="config" />
   </form>
</div>
{/block}
